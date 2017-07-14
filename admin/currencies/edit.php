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

function sr_edit_currency( $id ) {
	global $wpdb;
	$action     = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$message    = isset( $_GET['message'] ) ? $_GET['message'] : '';
	$currencies = new SR_Currency();
	if ( ! isset( $_POST['save_currency'] ) ) {
		$sr_form_data = $currencies->load( $id );
	} else {
		$sr_form_data = (object) $_POST['srform'];
		$columns      = array(
			'currency_name' => $sr_form_data->currency_name,
			'currency_code' => $sr_form_data->currency_code,
			'state'         => $sr_form_data->state,
			'exchange_rate' => $sr_form_data->exchange_rate,
			'sign'          => $sr_form_data->sign,
			'filter_range'  => !empty($sr_form_data->filter_range) ? $sr_form_data->filter_range : NULL,
		);

		$format = array(
			'%s',
			'%s',
			'%d',
			'%f',
			'%s',
			'%s',
		);

		if ( $action == 'edit' ) {
			$save_result = $wpdb->update( $wpdb->prefix . 'sr_currencies', $columns, array( 'id' => $id ), $format );
		} else {
			$save_result = $wpdb->insert( $wpdb->prefix . 'sr_currencies', $columns, $format );
		}

		if ( $save_result == true ) {
			if ( $action != 'edit' ) {
				$id = $wpdb->insert_id;
			}
			$message = $action == 'edit' ? 2 : 1;
			wp_redirect( admin_url( 'admin.php?page=sr-currencies&action=edit&id=' . $id . '&message=' . $message ) );
			exit;
		}
	}
	?>

	<div class="wrap">
		<div id="wpbody">
			<?php
			if ( isset( $save_result ) && $save_result == false ) {
				$message = $action == 'edit' ? __( 'Update currency failed', 'solidres' ) : __( 'Add new currency failed', 'solidres' );
				SR_Helper::show_message( $message, 'error' );
			}
			if ( $action == 'edit' ) :
				$text_message = $message == 1 ? __( 'Currency published.', 'solidres' ) : __( 'Currency updated.', 'solidres' );
				if ( $message != '' ) {
				SR_Helper::show_message( $text_message );
				} ?>
				<h2><?php _e( 'Edit currency', 'solidres' ); ?> <a
						href="<?php echo admin_url( 'admin.php?page=sr-add-new-currency' ); ?>"
						class="add-new-h2"><?php _e( 'Add New', 'solidres' ); ?></a>
				</h2>
			<?php else: ?>
				<h2><?php _e( 'Add new currency', 'solidres' ); ?></h2>
			<?php endif; ?>
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" class="edit-form-section">
					<div id="namediv" class="stuffbox">
						<h3><label for="name"><?php _e( 'General infomartion', 'solidres' ); ?></label></h3>

						<div class="inside">
							<form name="srform_edit_currency" action="" method="post" id="srform">
								<?php require( 'layouts/general.php' ); ?>
								<input type="submit" name="save_currency" value="<?php _e( 'Save', 'solidres' ); ?>"
								       class="srform_button button button-primary button-large save_currency">
							</form>
							<br>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }