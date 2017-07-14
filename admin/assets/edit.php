<?php
/*------------------------------------------------------------------------
Solidres - Hotel booking plugin for WordPress
------------------------------------------------------------------------
@Author    Solidres Team
@Website   http://www.solidres.com
@Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
@License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sr_edit_asset( $id ) {
	global $wpdb;
	$context = 'solidres.edit.asset.data';
	$current_user   = wp_get_current_user();
	$author_id      = $current_user->ID;
	$today          = date( 'Y-m-d H:i:s' );
	$action         = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$message        = isset( $_GET['message'] ) ? $_GET['message'] : '';
	$hub            = 'solidres-hub/solidres-hub.php';
	$solidres_asset = new SR_Asset();
	wp_enqueue_script( 'solidres_geocomplete' );
	$solidres_payment_gateways = solidres()->payment_gateways();

	if ( ! isset( $_POST['save_asset'] ) ) {
		$sr_form_data = $solidres_asset->load( $id );
	} else {
		$sr_form_data = (object) $_POST['srform'];
		if ( $action == 'edit' ) {
			$check_slug = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_reservation_assets WHERE alias = %s AND id != %d", $sr_form_data->alias, $id ) );
		} else {
			$check_slug = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_reservation_assets WHERE alias = %s", $sr_form_data->alias ) );
		}

		if ( $check_slug <= 0 ) {
			$metadata_json_data = json_encode( $sr_form_data->metadata );
			$param_json_data    = json_encode( $sr_form_data->params );
			$columns            = array(
				'category_id'           => $sr_form_data->category_id,
				'name'                  => $sr_form_data->name,
				'alias'                 => $sr_form_data->alias,
				'address_1'             => $sr_form_data->address_1,
				'address_2'             => $sr_form_data->address_2,
				'city'                  => $sr_form_data->city,
				'postcode'              => $sr_form_data->postcode,
				'phone'                 => $sr_form_data->phone,
				'description'           => $sr_form_data->description,
				'email'                 => $sr_form_data->email,
				'website'               => $sr_form_data->website,
				'featured'              => 0,
				'fax'                   => $sr_form_data->fax,
				'geo_state_id'          => $sr_form_data->geo_state_id != '' ? $sr_form_data->geo_state_id : null,
				'country_id'            => $sr_form_data->country_id,
				'currency_id'           => $sr_form_data->currency_id,
				'metakey'               => $sr_form_data->metakey,
				'metadesc'              => $sr_form_data->metadesc,
				'xreference'            => $sr_form_data->xreference,
				'state'                 => $sr_form_data->state,
				'default'               => $sr_form_data->default,
				'rating'                => $sr_form_data->rating,
				'deposit_required'      => $sr_form_data->deposit_required,
				'deposit_is_percentage' => $sr_form_data->deposit_is_percentage,
				'deposit_amount'        => $sr_form_data->deposit_amount,
				'deposit_by_stay_length' => $sr_form_data->deposit_by_stay_length,
				'deposit_include_extra_cost' => $sr_form_data->deposit_include_extra_cost,
				'created_by'            => empty($sr_form_data->created_by) ? $author_id : 0 ,
				'modified_by'           => $author_id,
				'partner_id'            => !empty($sr_form_data->partner_id) ? $sr_form_data->partner_id : null,
				'lat'                   => $sr_form_data->lat,
				'lng'                   => $sr_form_data->lng,
				'tax_id'                => $sr_form_data->tax_id != '' ? $sr_form_data->tax_id : null,
				'booking_type'          => $sr_form_data->booking_type,
			);

			$columns['modified_date'] = $action == 'edit' ? $today : '0000-00-00 00:00:00';
			if ( $action != 'edit' ) {
				$columns['metadata']     = $metadata_json_data;
				$columns['created_date'] = $today;
				$columns['params']       = $param_json_data;
			}

			$format = array(
				'%d', // category_id
				'%s', // name
				'%s', // alias
				'%s', // address_1
				'%s', // address_2
				'%s', // city
				'%s', // postcode
				'%s', // phone
				'%s', // description
				'%s', // email
				'%s', // website
				'%d', // featured
				'%s', // fax
				'%d', // geo_state_id
				'%d', // country_id
				'%d', // currency_id
				'%s', // metakey
				'%s', // metadesc
				'%s', // xreference
				'%d', // state
				'%d', // default
				'%d', // rating
				'%d', // deposit_required
				'%d', // deposit_is_percentage
				'%f', // deposit_amount
				'%d', // deposit_by_stay_length
				'%d', // deposit_include_extra_cost
				'%d', // created_by
				'%d', // modified_by
				'%d', // partner_id
				'%f', // lat
				'%f', // lng
				'%d', // tax_id
				'%d', // booking_type
				'%s', // modified_date
				'%s', // metadata
				'%s', // created_date
				'%s', // params
			);

			if ( $sr_form_data->default == 1 ) {
				$wpdb->query( 'UPDATE ' . $wpdb->prefix . 'sr_reservation_assets SET `default` = 0' );
			}

			if ( $action == 'edit' ) {
				$save_result = $wpdb->update(
					$wpdb->prefix . 'sr_reservation_assets',
					$columns,
					array( 'id' => $id ),
					$format
				);
			} else {
				$save_result = $wpdb->insert(
					$wpdb->prefix . 'sr_reservation_assets',
					$columns,
					$format
				);
			}

			if ( $action != 'edit' ) {
				$id = $wpdb->insert_id;
			}

			do_action( 'sr_after_save', $sr_form_data, $context, $id);

			if ( $save_result == true ) {

				if ( $action == 'edit' ) {
					$wpdb->delete( $wpdb->prefix . 'sr_media_reservation_assets_xref', array( 'reservation_asset_id' => $id ) );
					$wpdb->update( $wpdb->prefix . 'sr_reservation_assets', array( 'metadata' => '' ), array( 'id' => $id ) );
					$wpdb->update( $wpdb->prefix . 'sr_reservation_assets', array( 'metadata' => $metadata_json_data ), array( 'id' => $id ) );
					$wpdb->update( $wpdb->prefix . 'sr_reservation_assets', array( 'params' => '' ), array( 'id' => $id ) );
					$wpdb->update( $wpdb->prefix . 'sr_reservation_assets', array( 'params' => $param_json_data ), array( 'id' => $id ) );
				}

				if ( ! empty( $sr_form_data->mediaId ) ) {
					foreach ( $sr_form_data->mediaId as $key => $value ) {
						$wpdb->insert( $wpdb->prefix . 'sr_media_reservation_assets_xref',
							array(
								'media_id'             => $value,
								'reservation_asset_id' => $id,
								'weight'               => $key
							) );
					}
				}

				if ( is_plugin_active( $hub ) ) {
					if ( $action == 'edit' ) {
						$wpdb->delete( $wpdb->prefix . 'sr_facility_reservation_asset_xref', array( 'reservation_asset_id' => $id ) );
					}
					if ( ! empty( $sr_form_data->facility_id ) ) {
						foreach ( $sr_form_data->facility_id as $key => $value ) {
							$wpdb->insert( $wpdb->prefix . 'sr_facility_reservation_asset_xref',
								array(
									'facility_id'          => $value,
									'reservation_asset_id' => $id
								) );
						}
					}

					if ( $action == 'edit' ) {
						$wpdb->delete( $wpdb->prefix . 'sr_reservation_asset_theme_xref', array( 'reservation_asset_id' => $id ) );
					}
					if ( ! empty( $sr_form_data->theme_id ) ) {
						foreach ( $sr_form_data->theme_id as $key => $value ) {
							$wpdb->insert( $wpdb->prefix . 'sr_reservation_asset_theme_xref',
								array(
									'theme_id'             => $value,
									'reservation_asset_id' => $id
								) );
						}
					}
				}

				$custom_field_data_update = array();
				foreach ( $sr_form_data->customfields as $keys => $values ) {
					foreach ( $values as $key => $value ) {
						$field_key                = $keys . '.' . $key;
						$custom_field_data_update = array_merge( $custom_field_data_update, array( $field_key => $value ) );
					}
				}
				$solidres_custom_fields = new SR_Custom_Field( array(
					'id'              => $id,
					'group_namespace' => 'reservationasset_extra_fields'
				) );
				$solidres_custom_fields->set( $custom_field_data_update );

				$message = $action == 'edit' ? 2 : 1;
				wp_redirect( admin_url( 'admin.php?page=sr-assets&action=edit&id=' . $id . '&message=' . $message ) );
				exit;
			}
		}
	}
	?>

	<div id="wpbody">
		<div id="wpbody-content" aria-label="Main content" tabindex="0" style="overflow: hidden;">
			<div class="wrap srform_wrapper">
				<?php if ( isset( $check_slug ) && $check_slug > 0 ) {
					$text_message = __( 'Asset slug already exists.', 'solidres' );
					SR_Helper::show_message( $text_message, 'error' );
				}
				if ( isset( $save_result ) && $save_result == false ) {
					$message = $action == 'edit' ? __( 'Update asset failed', 'solidres' ) : __( 'Add new asset failed', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				}
				if ( $action == 'edit' ) :
					$text_message = $message == 1 ? __( 'Asset published.', 'solidres' ) : __( 'Asset updated.', 'solidres' );
					if ( $message != '' ) {
						SR_Helper::show_message( $text_message );
					}
					?>
					<h2><?php _e( 'Edit asset', 'solidres' ); ?> <a
							href="<?php echo admin_url( 'admin.php?page=sr-add-new-asset' ); ?>"
							class="add-new-h2"><?php _e( 'Add New', 'solidres' ); ?></a>
					</h2>
				<?php else: ?>
					<h2><?php _e( 'Add new asset', 'solidres' ); ?></h2>
				<?php endif; ?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<form name="srform_edit_asset" action="" method="post" id="srform">

							<div id="postbox-container-2" class="postbox-container">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<?php require( 'layouts/general.php' ); ?>
									<?php require( 'layouts/publishing.php' ); ?>
									<?php require( 'layouts/custom-fields.php' ); ?>
									<?php require( 'layouts/metadata.php' ); ?>
									<?php require( 'layouts/payments.php' ); ?>
									<?php //require( 'layouts/facility.php' ); ?>
									<?php //require( 'layouts/theme.php' ); ?>
									<?php require( 'layouts/plugins.php' ); ?>
									<?php do_action('sr_form_postbox_normal', 'reservation_asset'); ?>
								</div>
							</div>

							<div id="postbox-container-1" class="postbox-container">
								<div id="side-sortables" class="meta-box-sortables ui-sortable">
									<?php require( 'layouts/roomtype.php' ); ?>
									<?php require( 'layouts/media.php' ); ?>
									<?php require( 'layouts/extra.php' ); ?>
									<?php do_action('sr_form_postbox_side', 'reservation_asset'); ?>
								</div>
							</div>

							<input type="hidden" name="srform[id]" value="<?php echo $id ?>" />
							<input type="submit" name="save_asset" value="<?php _e( 'Save', 'solidres' ); ?>"
							       class="srform_button button button-primary button-large save_asset">
						</form>
					</div>
				</div>
				<script>
					jQuery( function($) {
						$( ".solidres-accordion" ).accordion({
							heightStyle: "content",
							collapsible: true
						});
					} );
				</script>
			</div>
		</div>
	</div>
<?php }