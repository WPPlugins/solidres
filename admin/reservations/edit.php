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

function sr_edit_reservation_item( $id ) {
	global $wpdb;
	$reservations = new SR_Reservation();
	$reservations->record_access( $id );
	$sr_form_data = $reservations->load( $id );
	$baseCurrency = new SR_Currency( 0, $sr_form_data->currency_id );
	$message = isset( $_GET['message'] ) ? $_GET['message'] : '';
	$text_message = '';
	$options_plugin = get_option( 'solidres_plugin' );
	wp_enqueue_script( 'solidres_editable' );
	wp_enqueue_style( 'solidres_editable', false );
	wp_enqueue_style( 'solidres_skeleton' );
	if ( $message == 1 ) {
		$text_message = __( 'Your invoice is sent.', 'solidres' );
	} else if ( $message == 2 ) {
		$text_message = __( 'Your invoice is not sent.', 'solidres' );
	} else if ( $message == 3 ) {
		$text_message = __( 'Your invoice is generated.', 'solidres' );
	} else if ( $message == 3 ) {
		$text_message = __( 'Your invoice is not generated.', 'solidres' );
	} ?>

	<script>
		jQuery(function($) {
			$.fn.editable.defaults.mode = 'inline';
			$("#state").editable({
				source: [
					{value: 0, text: '<?php _e( 'Pending arrival', 'solidres' ) ?>' },
					{value: 1, text: '<?php _e( 'Checked-in', 'solidres' ) ?>' },
					{value: 2, text: '<?php _e( 'Checked-out', 'solidres' ) ?>' },
					{value: 3, text: '<?php _e( 'Closed', 'solidres' ) ?>' },
					{value: 4, text: '<?php _e( 'Canceled', 'solidres' ) ?>' },
					{value: 5, text: '<?php _e( 'Confirmed', 'solidres' ) ?>' },
					{value: -2, text: '<?php _e( 'Trashed', 'solidres' ) ?>' }
				],
				params: function (params) {
					params.action = 'solidres_edit_reservation_field';
					params.security = '<?php echo wp_create_nonce( 'edit-reservation' ) ?>';
					return params;
				},
				url: '<?php echo admin_url( 'admin-ajax.php' ) ?>'
			});

			$("#payment_status").editable({
				source: [
					{value: 0, text: 'Unpaid'},
					{value: 1, text: 'Completed'},
					{value: 2, text: 'Cancelled'},
					{value: 3, text: 'Pending'}
				],
				params: function (params) {
					params.action = 'solidres_edit_reservation_field';
					params.security = '<?php echo wp_create_nonce( 'edit-reservation' ) ?>';
					return params;
				},
				url: '<?php echo admin_url( 'admin-ajax.php' ) ?>'
			});

			$("#total_paid").editable({
				params: function (params) {
					params.action = 'solidres_edit_reservation_field';
					params.security = '<?php echo wp_create_nonce( 'edit-reservation' ) ?>';
					return params;
				},
				url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
				display: function (value, response) {
					if (response) {
						if (response.success == true) {
							$(this).text(response.newValue);
						}
					}
				}
			});
			$( "#payment_method_txn_id" ).editable({
				params: function (params) {
					params.action = 'solidres_edit_reservation_field';
					params.security = '<?php echo wp_create_nonce( 'edit-reservation' ) ?>';
					return params;
				},
				url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
				display: function (value, response) {
					if (response) {
						if (response.success == true) {
							$(this).text(response.newValue);
						}
					}
				}
			});
			$( "#origin" ).editable({
				params: function (params) {
					params.action = 'solidres_edit_reservation_field';
					params.security = '<?php echo wp_create_nonce( 'edit-reservation' ) ?>';
					return params;
				},
				url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
				display: function (value, response) {
					if (response) {
						if (response.success == true) {
							$(this).text(response.newValue);
						}
					}
				}
			});
		});
	</script>

	<div class="wrap">
		<div id="wpbody">
			<div id="message" class="updated below-h2 <?php echo $message != '' ? '' : 'nodisplay'; ?>"><p><?php echo $text_message; ?></p></div>
			<h2>
				<?php _e( 'Edit Reservation', 'solidres' ); ?>
				<a
					href="<?php echo admin_url( 'admin.php?page=sr-reservations&action=amend&id=' . $id ); ?>"
					class="add-new-h2"><?php _e( 'Amend', 'solidres' ); ?></a>
			</h2>

			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" class="edit-form-section edit_reservation_table">
					<?php require( 'layouts/general.php' ); ?>
					<?php require( 'layouts/customer-info.php' ); ?>
					<?php require( 'layouts/room-extra-info.php' ); ?>
					<?php require( 'layouts/invoice.php' ); ?>
					<?php require( 'layouts/other-information.php' ); ?>
					<?php require( 'layouts/reservation-note.php' ); ?>
				</div>
			</div>
		</div>
	</div>
<?php }