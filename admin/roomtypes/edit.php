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

function sr_edit_room_type( $id ) {
	global $wpdb;
	$current_user       = wp_get_current_user();
	$author_id          = $current_user->ID;
	$today              = date( 'Y-m-d H:i:s' );
	$action             = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$message            = isset( $_GET['message'] ) ? $_GET['message'] : '';
	$hub                = 'solidres-hub/solidres-hub.php';
	$solidres_room_type = new SR_Room_Type();

	if ( ! isset( $_POST['save_room_type'] ) ) {
		$sr_form_data = $solidres_room_type->load( $id );
	} else {
		$sr_form_data = (object) $_POST['srform'];
		if ( $action == 'edit' ) {
			$check_slug = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_room_types WHERE alias = %s AND id != %d", $sr_form_data->alias, $id ) );
		} else {
			$check_slug = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_room_types WHERE alias = %s", $sr_form_data->alias ) );
		}

		if ( $check_slug <= 0 ) {
			$params_json_data = json_encode( $sr_form_data->params );
			$columns          = array(
				'name'                 => $sr_form_data->name,
				'alias'                => $sr_form_data->alias,
				'reservation_asset_id' => $sr_form_data->reservation_asset_id,
				'occupancy_max'        => $sr_form_data->occupancy_max,
				'occupancy_adult'      => $sr_form_data->occupancy_adult,
				'occupancy_child'      => $sr_form_data->occupancy_child,
				'state'                => $sr_form_data->state,
				'description'          => $sr_form_data->description,
				'modified_by'          => $author_id,
				'created_by'           => !empty( $sr_form_data->created_by ) ? $sr_form_data->created_by : $author_id ,
				'params'               => $params_json_data,
				'modified_date'        => $action == 'edit' ? $today : '0000-00-00 00:00:00',
				'featured'             => $sr_form_data->featured,
				'ordering'             => $sr_form_data->ordering,
			);

			$format = array(
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
				'%d',
			);
			if ( $action != 'edit' ) {
				$columns['checked_out']      = 0;
				$columns['checked_out_time'] = '0000-00-00 00:00:00';
				$columns['created_date']     = $today;
				$columns['language']         = '*';
				$columns['smoking']          = 0;
				$format = array_merge($format, array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
				));
			}

			if ( $action == 'edit' ) {
				$save_result = $wpdb->update( $wpdb->prefix . 'sr_room_types', $columns, array( 'id' => $id ), $format );
			} else {
				$save_result = $wpdb->insert( $wpdb->prefix . 'sr_room_types', $columns, $format );
			}

			if ( $save_result == true ) {
				if ( $action != 'edit' ) {
					$id = $wpdb->insert_id;
				}
				$get_currency_id = $wpdb->get_var( $wpdb->prepare( "SELECT currency_id FROM {$wpdb->prefix}sr_reservation_assets WHERE id = %d", $sr_form_data->reservation_asset_id ) );

				if ( is_plugin_active( $hub ) ) {
					if ( $action == 'edit' ) {
						$wpdb->delete( $wpdb->prefix . 'sr_facility_room_type_xref', array( 'room_type_id' => $id ) );
					}
					if ( ! empty( $sr_form_data->facility_id ) ) {
						foreach ( $sr_form_data->facility_id as $key => $value ) {
							$wpdb->insert( $wpdb->prefix . 'sr_facility_room_type_xref', array(
								'facility_id'  => $value,
								'room_type_id' => $id
							) );
						}
					}
				}

				if ( $action == 'edit' ) {
					$wpdb->delete( $wpdb->prefix . 'sr_room_type_coupon_xref', array( 'room_type_id' => $id ) );
					$wpdb->delete( $wpdb->prefix . 'sr_room_type_extra_xref', array( 'room_type_id' => $id ) );
				}

				if ( ! empty( $sr_form_data->coupons ) ) {
					foreach ( $sr_form_data->coupons as $srform_coupon ) {
						$wpdb->insert( $wpdb->prefix . 'sr_room_type_coupon_xref', array(
							'room_type_id' => $id,
							'coupon_id'    => $srform_coupon
						) );
					}
				}

				if ( ! empty( $sr_form_data->extras ) ) {
					foreach ( $sr_form_data->extras as $srform_extra ) {
						$wpdb->insert( $wpdb->prefix . 'sr_room_type_extra_xref', array(
							'room_type_id' => $id,
							'extra_id'     => $srform_extra
						) );
					}
				}

				$solidres_tariff  = new SR_Tariff();
				$get_tariffs_info = $solidres_tariff->load_by_room_type_id( $id );

				if ( $action == 'edit' ) {
					$wpdb->update( $wpdb->prefix . 'sr_tariffs',
						array(
							'currency_id' => $get_currency_id,
							'title'       => $sr_form_data->standard_tariff_title,
							'description' => $sr_form_data->standard_tariff_description,
						),
						array(
							'id' => $get_tariffs_info[0]->id,
						),
						array(
							'%d',
							'%s',
							'%s',
						)
					);
				} else {
					$wpdb->insert( $wpdb->prefix . 'sr_tariffs',
						array(
							'currency_id'   => $get_currency_id,
							'valid_from'    => '0000-00-00',
							'valid_to'      => '0000-00-00',
							'room_type_id'  => $id,
							'title'         => $sr_form_data->standard_tariff_title,
							'description'   => $sr_form_data->standard_tariff_description,
							'type'          => 0,
							'limit_checkin' => '',
						)
					);
				}
				$get_last_tariff_id = $wpdb->insert_id;
				$values             = array();
				foreach ( $sr_form_data->default_tariff as $day => $price ) {
					if ( $action == 'edit' ) {
						$values[] = "WHEN $day THEN $price";
					} else {
						$values[] = "('$get_last_tariff_id', $price, $day, NULL, NULL, NULL)";
					}
				}
				$values_convert = $action == 'edit' ? implode( ' ', $values ) : implode( ',', $values );

				if ( $action == 'edit' ) {
					$query_tariff_details = 'UPDATE ' . $wpdb->prefix . 'sr_tariff_details
					SET price = CASE w_day ' . $values_convert . '
					END
					WHERE tariff_id = ' . $get_tariffs_info[0]->id;
				} else {
					$query_tariff_details = "INSERT INTO {$wpdb->prefix}sr_tariff_details (`tariff_id`, `price`, `w_day`, `guest_type`, `from_age`, `to_age`) VALUES $values_convert";
				}
				$wpdb->query( $wpdb->prepare( $query_tariff_details, 10 ) );

				$custom_field_data_update = array();
				foreach ( $sr_form_data->customfields as $keys => $values ) {
					foreach ( $values as $key => $value ) {
						$field_key                = $keys . '.' . $key;
						$custom_field_data_update = array_merge( $custom_field_data_update, array( $field_key => $value ) );
					}
				}
				$solidres_custom_fields = new SR_Custom_Field( array(
					'id'              => $id,
					'group_namespace' => 'roomtype_custom_fields',
					'type'            => 'room_type'
				) );
				$solidres_custom_fields->set( $custom_field_data_update );

				if ( $action == 'edit' ) {
					$wpdb->delete( $wpdb->prefix . 'sr_media_roomtype_xref', array( 'room_type_id' => $id ) );
				}
				if ( ! empty( $sr_form_data->mediaId ) ) {
					foreach ( $sr_form_data->mediaId as $key => $value ) {
						$wpdb->insert( $wpdb->prefix . 'sr_media_roomtype_xref', array(
							'media_id'     => $value,
							'room_type_id' => $id,
							'weight'       => $key
						) );
					}
				}

				if ( $action == 'edit' ) {
					foreach ( $sr_form_data->rooms as $key => $value ) {
						$wpdb->update( $wpdb->prefix . 'sr_rooms', array( 'label' => $value ), array( 'id' => $key ) );
					}
				}

				if ( ! empty( $sr_form_data->roomsnew ) ) {
					foreach ( $sr_form_data->roomsnew as $key => $value ) {
						$wpdb->insert( $wpdb->prefix . 'sr_rooms', array(
							'label'        => $value,
							'room_type_id' => $id
						) );
					}
				}

				$message = $action == 'edit' ? 2 : 1;
				wp_redirect( admin_url( 'admin.php?page=sr-room-types&action=edit&id=' . $id . '&message=' . $message ) );
				exit;
			}
		}
	}
	?>

	<div id="wpbody">
		<div id="wpbody-content" aria-label="Main content" tabindex="0" style="overflow: hidden;">
			<div class="wrap srform_wrapper">
				<?php if ( isset( $check_slug ) && $check_slug > 0 ) {
					$text_message = __( 'Room types slug already exists.', 'solidres' );
					SR_Helper::show_message( $text_message, 'error' );
				}
				if( isset( $save_result ) && $save_result == false ) {
					$message = $action == 'edit' ? __( 'Update roomtype failed', 'solidres' ) : __( 'Add new roomtype failed', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				}
				if ( $action == 'edit' ) :
					$text_message = $message == 1 ? __( 'Room type published.', 'solidres' ) : __( 'Room type updated.', 'solidres' );
					if ( $message != '' ) {
					SR_Helper::show_message( $text_message );
					} ?>
					<h2><?php _e( 'Edit room type', 'solidres' ); ?> <a
							href="<?php echo admin_url( 'admin.php?page=sr-add-new-room-type' ); ?>"
							class="add-new-h2"><?php _e( 'Add New', 'solidres' ); ?></a>
					</h2>
				<?php else: ?>
					<h2><?php _e( 'Add new room type', 'solidres' ); ?></h2>
				<?php endif; ?>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<form name="srform_edit_roomtype" action="" method="post" id="srform">

							<div id="postbox-container-2" class="postbox-container">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<?php require( 'layouts/general.php' ); ?>
									<?php require( 'layouts/publishing.php' ); ?>
									<?php require( 'layouts/custom-fields.php' ); ?>
									<?php require( 'layouts/complex-tariff.php' ); ?>
									<?php //require( 'layouts/facility.php' ); ?>
								</div>
							</div>

							<div id="postbox-container-1" class="postbox-container">
								<div id="side-sortables" class="meta-box-sortables ui-sortable">
									<?php require( 'layouts/room.php' ); ?>
									<?php require( 'layouts/media.php' ); ?>
								</div>
							</div>

							<input type="submit" name="save_room_type" value="<?php _e( 'Save', 'solidres' ); ?>"
							       class="srform_button button button-primary button-large save_room_type">
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }