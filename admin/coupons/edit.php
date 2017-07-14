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

function sr_edit_coupon( $id ) {
	global $wpdb;
	$action         = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$message = isset( $_GET['message'] ) ? $_GET['message'] : '';

	if ( ! isset( $_POST['save_coupon'] ) ) {
		$sr_form_data = $wpdb->get_row( $wpdb->prepare( "SELECT c.*, r.name as assetname FROM {$wpdb->prefix}sr_coupons c LEFT JOIN {$wpdb->prefix}sr_reservation_assets r ON c.reservation_asset_id = r.id WHERE c.id = %d", $id ) );
	} else {
		$sr_form_data = (object) $_POST['srform'];
		if ( $action == 'edit' ) {
			$check_coupon = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_coupons WHERE coupon_code = %s AND id != %d", $sr_form_data->coupon_code, $id ) );
		} else {
			$check_coupon = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_coupons WHERE coupon_code = %s", $sr_form_data->coupon_code ) );
		}

		if ( $check_coupon <= 0 ) {
			$params_json_data = json_encode( $sr_form_data->params );
			$columns            = array(
				'coupon_name'          => $sr_form_data->coupon_name,
				'coupon_code'          => $sr_form_data->coupon_code,
				'amount'               => $sr_form_data->amount,
				'reservation_asset_id' => $sr_form_data->reservation_asset_id,
				'is_percent'           => $sr_form_data->is_percent,
				'valid_from'           => date( 'Y-m-d', strtotime($sr_form_data->valid_from) ),
				'valid_to'             => date( 'Y-m-d', strtotime($sr_form_data->valid_to ) ),
				'valid_from_checkin'   => date( 'Y-m-d', strtotime($sr_form_data->valid_from_checkin ) ),
				'valid_to_checkin'     => date( 'Y-m-d', strtotime($sr_form_data->valid_to_checkin ) ),
				'customer_group_id'    => empty($sr_form_data->customer_group_id) ? NULL : $sr_form_data->customer_group_id,
				'quantity'             => $sr_form_data->quantity,
				'state'                => $sr_form_data->state,
				'params'               => $params_json_data,
			);

			$format = array(
				'%s',
				'%s',
				'%f',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
			);

			if ( $action == 'edit' ) {
				$save_result = $wpdb->update( $wpdb->prefix . 'sr_coupons', $columns, array( 'id' => $id ), $format );
			} else {
				$save_result = $wpdb->insert( $wpdb->prefix . 'sr_coupons', $columns, $format );
			}

			if ( $save_result == true ) {
				if ( $action != 'edit' ) {
					$id = $wpdb->insert_id;
				}
				$message = $action == 'edit' ? 2 : 1;
				wp_redirect( admin_url( 'admin.php?page=sr-coupons&action=edit&id=' . $id . '&message=' . $message ) );
				exit;
			}
		}
	}
	?>

	<div id="wpbody">
		<div id="wpbody-content" aria-label="Main content" tabindex="0" style="overflow: hidden;">
			<div class="wrap srform_wrapper">
				<?php if( isset( $check_coupon ) && $check_coupon > 0 ) {
					$text_message = __( 'Your coupon code is duplicated, please enter another coupon code', 'solidres' );
					SR_Helper::show_message( $text_message, 'error' );
				}
				if( isset( $save_result ) && $save_result == false ) {
					$message = $action == 'edit' ? __( 'Update coupon failed', 'solidres' ) : __( 'Add new coupon failed', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				}
				if ( $action == 'edit' ) :
					$text_message = $message == 1 ? __( 'Coupon published.', 'solidres' ) : __( 'Coupon updated.', 'solidres' );
					if ( $message != '' ) {
					SR_Helper::show_message( $text_message );
					} ?>
					<h2><?php _e( 'Edit coupon', 'solidres' ); ?> <a
							href="<?php echo admin_url( 'admin.php?page=sr-add-new-coupon' ); ?>"
							class="add-new-h2"><?php _e( 'Add New', 'solidres' ); ?></a>
					</h2>
				<?php else: ?>
					<h2><?php _e( 'Add new Coupon', 'solidres' ); ?></h2>
				<?php endif; ?>
				<div id="poststuff">
					<div id="post-body-content" class="metabox-holder columns-2">
						<form name="srform_edit_coupon" action="" method="post" id="srform">

							<?php require( 'layouts/general.php' ); ?>
							<?php require( 'layouts/publishing.php' ); ?>

							<input type="submit" name="save_coupon" value="<?php _e( 'Save', 'solidres' ); ?>"
							       class="srform_button button button-primary button-large save_coupon">
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }