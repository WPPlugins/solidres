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

function sr_edit_extra( $id ) {
	global $wpdb;
	$current_user = wp_get_current_user();
	$author_id    = $current_user->ID;
	$today        = date( 'd-m-Y' );
	$action       = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$message      = isset( $_GET['message'] ) ? $_GET['message'] : '';

	if ( ! isset( $_POST['save_extra'] ) ) {
		$sr_form_data = $wpdb->get_row( $wpdb->prepare( "SELECT e.*, r.name as assetname FROM {$wpdb->prefix}sr_extras e LEFT JOIN {$wpdb->prefix}sr_reservation_assets r ON e.reservation_asset_id = r.id WHERE e.id = %d", $id ) );
	} else {
		$sr_form_data     = (object) $_POST['srform'];
		$params_json_data = json_encode( $sr_form_data->params );
		$columns = array(
			'name'                 => $sr_form_data->name,
			'state'                => $sr_form_data->state,
			'description'          => $sr_form_data->description,
			'price'                => $sr_form_data->price,
			'price_adult'          => $sr_form_data->price_adult,
			'price_child'          => $sr_form_data->price_child,
			'modified_by'          => $author_id,
			'ordering'             => 1,
			'max_quantity'         => $sr_form_data->max_quantity,
			'daily_chargable'      => $sr_form_data->daily_chargable,
			'reservation_asset_id' => $sr_form_data->reservation_asset_id,
			'mandatory'            => $sr_form_data->mandatory,
			'charge_type'          => $sr_form_data->charge_type,
			'tax_id'               => !empty($sr_form_data->tax_id) ? $sr_form_data->tax_id : NULL,
			'params'               => $params_json_data,
			'modified_date'        => $action == 'edit' ? $today : '0000-00-00 00:00:00',
		);

		$format = array(
			'%s',
			'%d',
			'%s',
			'%f',
			'%f',
			'%f',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
		);

		if( $action != 'edit' ) {
			$columns['created_date'] = $today;
			$columns['created_by'] = $author_id;
			$format = array_merge( $format, array( '%s', '%d' ));
		}

		if ( $action == 'edit' ) {
			$save_result = $wpdb->update( $wpdb->prefix . 'sr_extras', $columns, array( 'id' => $id ), $format );
		} else {
			$save_result = $wpdb->insert( $wpdb->prefix . 'sr_extras', $columns, $format );
		}

		if ( $save_result == true ) {
			if ( $action != 'edit' ) {
				$id = $wpdb->insert_id;
			}
			$message = $action == 'edit' ? 2 : 1;
			wp_redirect( admin_url( 'admin.php?page=sr-extras&action=edit&id=' . $id . '&message=' . $message ) );
			exit;
		}
	}
	?>

	<div id="wpbody">
		<div id="wpbody-content" aria-label="Main content" tabindex="0" style="overflow: hidden;">
			<div class="wrap srform_wrapper">
				<?php
				if( isset( $save_result ) && $save_result == false ) {
					$message = $action == 'edit' ? __( 'Update extra failed', 'solidres' ) : __( 'Add new extra failed', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				}
				if ( $action == 'edit' ) :
					$text_message = $message == 1 ? __( 'Extra published.', 'solidres' ) : __( 'Extra updated.', 'solidres' );
					if ( $message != '' ) {
						SR_Helper::show_message( $text_message );
					} ?>
					<h2><?php _e( 'Edit extra', 'solidres' ); ?> <a
							href="<?php echo admin_url( 'admin.php?page=sr-add-new-extra' ); ?>"
							class="add-new-h2"><?php _e( 'Add New', 'solidres' ); ?></a>
					</h2>
				<?php else: ?>
					<h2><?php _e( 'Add new extra', 'solidres' ); ?></h2>
				<?php endif; ?>
				<div id="poststuff">
					<div id="post-body-content" class="metabox-holder columns-2">
						<form name="srform_edit_extra" action="" method="post" id="srform">

							<?php require( 'layouts/general.php' ); ?>
							<?php require( 'layouts/publishing.php' ); ?>

							<input type="submit" name="save_extra" value="<?php _e( 'Save', 'solidres' ); ?>"
							       class="srform_button button button-primary button-large save_extra">
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }