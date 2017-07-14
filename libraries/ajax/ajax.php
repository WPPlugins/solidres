<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

class SR_Ajax {

	public static function init() {
		$events = array(
			'load_availability_calendar' => true,
			'load_checkinoutform' => true,
			'load_roomtypeform' => true,
			'load_states' => true,
			'load_taxes' => false,
			'load_coupons' => false,
			'load_extras' => false,
			'load_available_rooms' => false,
			'edit_reservation_field' => false,
			'save_reservation_note' => false,
			'cancel_reservation' => false,
			'delete_room' => false,
			'confirm_delete_room' => false,
			'reservation_count_unread' => false,
			'calculate_tariff' => true,
			'reservation_process' => true,
			'reservation_progress' => true,
			'set_currency' => true,
			'check_coupon' => true,
			'apply_coupon' => true,
			'remove_coupon' => true
		);

		foreach ( $events as $event => $nopriv ) {
			add_action( 'wp_ajax_solidres_' . $event, array( __CLASS__, $event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_solidres_' . $event, array( __CLASS__, $event ) );
			}
		}
	}

	public static function load_availability_calendar() {
		check_ajax_referer( 'load-calendar', 'security' );
		$room_type_id = (int)$_REQUEST['id'];
		$calendar = new SR_Asset();
		echo $calendar->get_availability_calendar( $room_type_id );
		wp_die();
	}

	public static function set_currency() {
		check_ajax_referer( 'set-currency', 'security' );
		$currency_id = (int) $_POST['id'];
		$currentCurrencyId = isset($_COOKIE[ 'solidres_currency' ]) ? (int) $_COOKIE[ 'solidres_currency' ] : 0;

		if (empty($currentCurrencyId) || $currentCurrencyId != $currency_id)
		{
			setcookie('solidres_currency', $currency_id, time()+60*60*24*30, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
		}

		solidres()->session->set( 'current_currency_id', $currency_id );
		wp_die();
	}

	public static function check_coupon() {
		check_ajax_referer( 'check-coupon', 'security' );

		$solidres_coupon = new SR_Coupon();
		$coupon_code = $_POST[ 'coupon_code' ];
		$asset_id = $_POST[ 'raid' ];
		$current_date = strtotime( 'now' );
		$checkin = strtotime( solidres()->session->get( 'sr_checkin' ) );
		$current_user = wp_get_current_user();
		$customer_group_id = $solidres_coupon->get_customer_group_id( $current_user->ID );

		$status = $solidres_coupon->is_valid( $coupon_code, $asset_id, $current_date, $checkin, $customer_group_id);

		if ($status) {
			$msg = '<span class="help-block accepted">'. __( 'Coupon is accepted', 'solidres' ).'
			        <a href="javascript:void(0)" id="apply-coupon">'. __( 'Apply coupon', 'solidres' ) .'</a></span>';
		} else {
			$msg = '<span class="help-block rejected">'. __( 'Coupon is not valid', 'solidres' ).'</span>';
		}

		$response = array('status' => $status, 'message' => $msg);

		echo json_encode($response);

		wp_die();
	}

	public static function apply_coupon() {
		check_ajax_referer( 'apply-coupon', 'security' );

		$solidres_coupon = new SR_Coupon();
		$coupon_code = $_POST[ 'coupon_code' ];
		$asset_id = $_POST[ 'raid' ];
		$current_date = strtotime( 'now' );
		$checkin = strtotime( solidres()->session->get( 'sr_checkin' ) );
		$current_user = wp_get_current_user();
		$customer_group_id = $solidres_coupon->get_customer_group_id( $current_user->ID );

		$is_valid = $solidres_coupon->is_valid($coupon_code, $asset_id, $current_date, $checkin, $customer_group_id);

		if ($is_valid) {
			$coupon_data = array();
			$coupon = $solidres_coupon->load_by_code( $coupon_code );
			$coupon_data['coupon_id'] = $coupon->id;
			$coupon_data['coupon_name'] = $coupon->coupon_name;
			$coupon_data['coupon_code'] = $coupon->coupon_code;
			$coupon_data['coupon_amount'] = $coupon->amount;
			$coupon_data['coupon_is_percent'] = $coupon->is_percent;
			$coupon_data['valid_from'] = $coupon->valid_from;
			$coupon_data['valid_to'] = $coupon->valid_to;
			$coupon_data['valid_from_checkin'] = $coupon->valid_from_checkin;
			$coupon_data['valid_to_checkin'] = $coupon->valid_to_checkin;
			$coupon_data['customer_group_id'] = $coupon->customer_group_id;
			solidres()->session->set( 'sr_coupon', $coupon_data );
			$response = array('status' => true, 'message' => '');
		} else {
			solidres()->session->set( 'sr_coupon', NULL );
			$response = array('status' => false, 'message' => '');
		}
		echo json_encode($response);

		wp_die();
	}

	public static function remove_coupon() {
		check_ajax_referer( 'remove-coupon', 'security' );

		$current_applied_coupon = solidres()->session->get( 'sr_coupon' );
		$removed_coupon_id = $_POST[ 'id' ];

		if ($current_applied_coupon['coupon_id'] == $removed_coupon_id)
		{
			solidres()->session->set( 'sr_coupon', NULL );
			$status = true;
		}

		$response = array('status' => $status, 'message' => '');

		echo json_encode($response);

		wp_die();
	}

	public static function load_checkinoutform() {

		check_ajax_referer( 'load-date-form', 'security' );

		$Itemid = (int)$_REQUEST['Itemid'];
		$id = (int)$_REQUEST['id'];
		$roomtype_id = (int)$_REQUEST['roomtype_id'];
		$tariff_id = (int)$_REQUEST['tariff_id'];
		$asset = new SR_Asset();

		echo $asset->get_check_in_out_form( $tariff_id, $roomtype_id, $id, $Itemid );
		wp_die();
	}

	public static function load_roomtypeform() {

		check_ajax_referer( 'load-room-form', 'security' );

		$asset_id = (int)$_GET[ 'raid' ];
		$room_type_id = (int)$_GET[ 'rtid' ];
		$tariff_id = (int)$_GET[ 'tariffid' ];
		$adjoining_layer = (int) $_GET[ 'adjoininglayer' ];
		$quantity = (int)$_GET[ 'quantity' ];

		$asset = new SR_Asset();
		echo $asset->get_room_type_form( $asset_id, $room_type_id, $tariff_id, $quantity, $adjoining_layer );
		wp_die();
	}

	public static function load_states() {

		check_ajax_referer( 'load-states', 'security' );

		global $wpdb;

		$statedata = '';
		$country_id = (int)$_REQUEST[ 'country_id' ];
		$states = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_geo_states WHERE country_id = %d", $country_id ) );
		$statedata .= '<option value="">Select state</option>';
		foreach ( $states as $state ) {
			$statedata .= '<option value="'.$state->id.'">'.$state->name.'</option>';
		}
		echo $statedata;
		wp_die();
	}

	public static function load_taxes() {
		check_ajax_referer( 'load-taxes', 'security' );
		global $wpdb;
		$taxdata = '';
		$country_id = (int)$_REQUEST['country_id'];
		$taxes = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_taxes WHERE country_id = %d", $country_id ) );
		$taxdata .= '<option value="">Select tax</option>';
		foreach ( $taxes as $tax ) {
			$tax_rate = $tax->rate * 100;
			$taxdata .= '<option value="'.$tax->id.'">'.$tax->name.' ('.$tax_rate.'%)</option>';
		}
		echo $taxdata;
		wp_die();
	}

	public static function load_coupons() {

		check_ajax_referer( 'load-coupons', 'security' );

		global $wpdb;

		$coupons_data = '';
		$reservation_asset_id = (int)$_REQUEST['reservation_asset_id'];
		$coupons = $wpdb->get_results( $wpdb->prepare( "SELECT id, coupon_name FROM {$wpdb->prefix}sr_coupons WHERE reservation_asset_id = %d", $reservation_asset_id ) );
		foreach ( $coupons as $coupon ) {
			$coupons_data .= '<input type="checkbox" name="srform[coupons][]" value="'.$coupon->id.'"/><a href="' . admin_url( "admin.php?page=sr-coupons&action=edit&id=" . $coupon->id ) . '" target="_blank">'.$coupon->coupon_name.'</a><br>';
		}

		echo $coupons_data;
		wp_die();
	}

	public static function load_extras() {

		check_ajax_referer( 'load-extras', 'security' );

		global $wpdb;

		$extras_data = '';
		$reservation_asset_id = (int)$_REQUEST['reservation_asset_id'];
		$extras = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$wpdb->prefix}sr_extras WHERE reservation_asset_id = %d", $reservation_asset_id ) );
		foreach ( $extras as $extra ) {
			$extras_data .= '<input type="checkbox" name="srform[extras][]" value="'.$extra->id.'"/><a href="' . admin_url( "admin.php?page=sr-extras&action=edit&id=" . $extra->id ) . '" target="_blank">'.$extra->name.'</a><br>';
		}

		echo $extras_data;
		wp_die();
	}

	public static function save_reservation_note() {

		check_ajax_referer( 'save-note', 'security' );

		global $wpdb;

		$notes_data = '';
		$current_user = wp_get_current_user();
		$author_id = $current_user->ID;
		$note_text = $_REQUEST['note_text'];
		$notify_check = $_REQUEST['notify_check'];
		$notify_check = $notify_check == 'true' ? 1 : 0;
		$visible_in_frontend_check = $_REQUEST['visible_in_frontend_check'];
		$visible_in_frontend_check = $visible_in_frontend_check == 'true' ? 1 : 0;
		$reservation_id = $_REQUEST['reservation_id'];
		$today = date( 'Y-m-d H:i:s' );

		$data = array();
		$data['reservation_id'] = ! empty( $reservation_id ) ? $reservation_id : 0;
		$data['text'] = ! empty( $note_text ) ? $note_text : '';
		$data['created_date'] = $today;
		$data['created_by'] = $author_id;
		$data['notify_customer'] = $notify_check;
		$data['visible_in_frontend'] = $visible_in_frontend_check;

		$wpdb->insert( $wpdb->prefix.'sr_reservation_notes', array( 'reservation_id' => $reservation_id, 'text' => $note_text, 'created_date' => $today, 'created_by' => $author_id, 'notify_customer' => $notify_check, 'visible_in_frontend' => $visible_in_frontend_check ) );
		$get_reservation_notes = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_reservation_notes WHERE reservation_id = %d", $reservation_id ) );

		foreach ( $get_reservation_notes as $get_reservation_note ) {

			$created_name = get_the_author_meta('display_name', $get_reservation_note->created_by);

			$notify_customer =  $get_reservation_note->notify_customer == 1 ? 'Yes' : 'No';
			$visible_frontend = $get_reservation_note->visible_in_frontend == 1 ? 'Yes' : 'No';

			$notes_data .= '<div class="reservation_note_item">';
			$notes_data .= '<p class="info">'.$get_reservation_note->created_date.' by '.$created_name.'</p>';
			$notes_data .= '<p>Notify customer via email: '.$notify_customer.' | Display in frontend: '.$visible_frontend.'</p>';
			$notes_data .= '<p>'.$get_reservation_note->text.'</p>';
			$notes_data .= '</div>';
		}

		if( $notify_check == 1 ){
			$reservations = new SR_Reservation();
			$resTable = $reservations->load( $reservation_id );
			$assets = new SR_Asset();
			$solidres_state = new SR_State();
			$asset = $assets->load( $resTable->reservation_asset_id );
			$asset_state = null;
			if ($asset->geo_state_id) {
				$asset_state = $solidres_state->load($asset->geo_state_id);
			}

			$asset_custom_fields = new SR_Custom_Field( array( 'id' => (int) $resTable->reservation_asset_id, 'type' => 'asset' ) );
			$custom_field_data = $asset_custom_fields->create_array_group();
			$social_networks = array();
			$social_networks = isset( $custom_field_data['socialnetworks'] ) ? $custom_field_data['socialnetworks'] : $social_networks;
			$social_network = array();
			if( ! empty ( $social_networks ) ) {
				foreach ( $social_networks as $keys => $values ) {
					$field_name = $asset_custom_fields->split_field_name( $values[0] );
					$field_value = $values[1];
					$social_network[$field_name] = $field_value;
				}
			}

			$asset->name = apply_filters( 'solidres_asset_name', $asset->name );

			$display_data = array(
				'reservation' => $resTable,
				'asset' => $asset,
				'social_network' => $social_network,
				'text' => $data['text'],
				'asset_state' => $asset_state
			);

			ob_start();
			include_once WP_PLUGIN_DIR . '/solidres/templates/emails/reservation_note_notification_customer_html_inliner.php';
			$emailTemplate = ob_get_contents();
			ob_end_clean ();

			$to = $resTable->customer_email;

			$subject = __( 'Reservation notification from ', 'solidres' ).$asset->name;
			$header = 'From: ' .$asset->name.' <'.$asset->email.'>';
			add_filter( 'wp_mail_content_type', 'solidres_set_html_content_type' );
			wp_mail( $to, $subject, $emailTemplate, $header );
			remove_filter( 'wp_mail_content_type', 'solidres_set_html_content_type' );
		}
		echo $notes_data;
		wp_die();
	}

	public static function cancel_reservation() {

		if ( ! wp_verify_nonce( $_REQUEST['nonce'], "cancel_reservation_nonce")) {
			exit( "Can't cancel this reservation error" );
		}

		global $wpdb;

		$reservation_id = (int)$_REQUEST['reservation_id'];
		$customer_id = (int)$_REQUEST['customer_id'];

		$return = $wpdb->update( $wpdb->prefix.'sr_reservations', array( 'state' => 4 ), array( 'id' => $reservation_id, 'customer_id' => $customer_id ) );
		if ( $return ){
			echo 1;
		} else {
			echo 0;
		}
		wp_die();
	}

	public static function delete_room() {

		check_ajax_referer( 'delete-room', 'security' );

		global $wpdb;

		$room_id = (int)$_REQUEST['room_id'];

		$room_exist = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_reservation_room_xref WHERE room_id = %d", $room_id ) );
		$room_extra_exist = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_reservation_room_extra_xref WHERE room_id = %d", $room_id ) );
		if ( defined('SR_PLUGIN_LIMITBOOKING_ENABLED') && SR_PLUGIN_LIMITBOOKING_ENABLED ) {
			$room_limitbooking_exist = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_limit_booking_details WHERE room_id = %d", $room_id ) );
		}

		if( $room_exist > 0 ){
			$result['type'] = "error";
		} else {
			if ( $room_extra_exist > 0 ){
				$result['type'] = "error";
			} else {
				if( $room_limitbooking_exist > 0 ){
					$result['type'] = "error";
				} else {
					$result['type'] = "success";
					$result['room_id'] = $room_id;
					$wpdb->delete( $wpdb->prefix.'sr_rooms', array( 'id' => $room_id ) );
				}
			}
		}

		$result = json_encode( $result );
		echo $result;
		wp_die();
	}

	public static function confirm_delete_room() {

		check_ajax_referer( 'confirm-delete-room', 'security' );

		global $wpdb;

		$room_id = (int)$_REQUEST['room_id'];

		$result['type'] = "success";
		$result['room_id'] = $room_id;

		$wpdb->update( $wpdb->prefix.'sr_reservation_room_xref', array( 'room_id' => NULL ), array( 'room_id' => $room_id ) );
		$wpdb->update( $wpdb->prefix.'sr_reservation_room_extra_xref', array( 'room_id' => NULL ), array( 'room_id' => $room_id ) );
		$wpdb->delete( $wpdb->prefix.'sr_limit_booking_details', array( 'room_id' => $room_id ) );
		$wpdb->delete( $wpdb->prefix.'sr_rooms', array( 'id' => $room_id ) );

		$result = json_encode( $result );
		echo $result;
		wp_die();
	}

	public static function edit_reservation_field() {

		check_ajax_referer( 'edit-reservation', 'security' );

		global $wpdb;
		$solidres_reservation = new SR_Reservation();

		$pk = $_REQUEST['pk'];
		$name = $_REQUEST['name'];
		$value = $_REQUEST['value'];

		$reservation = $solidres_reservation->load( $pk );

		$currencyFields = array(
			'total_price', 'total_price_tax_incl', 'total_price_tax_excl', 'total_extra_price', 'total_extra_price_tax_incl',
			'total_extra_price_tax_excl', 'total_discount', 'total_paid', 'deposit_amount'
		);

		if ($name == 'payment_status') {
			/*JPluginHelper::importPlugin('solidrespayment');

			$responses = $app->triggerEvent('OnReservationPaymentStatusBeforeChange', array( $table, $value ));

			if (in_array(false, $responses, true))
			{
				$canContinue = false;
			}*/
		}

		$result = $wpdb->update( $wpdb->prefix.'sr_reservations', array( $name => $value ), array( 'id' => $pk ) );

		// When payment status is chagned to cancelled, update the reservation status to cancelled as well
		if ($name == 'payment_status' && $value == 2) {
			$result = $wpdb->update( $wpdb->prefix.'sr_reservations', array( 'state' => 4 ), array( 'id' => $pk ) );
		}

		// Get the new value
		$newValue = $wpdb->get_var( $wpdb->prepare( "SELECT {$name} FROM {$wpdb->prefix}sr_reservations WHERE id = %d", $pk) );

		if (in_array($name, $currencyFields)) {
			$baseCurrency = new SR_Currency($value, $reservation->currency_id);
			$newValue = $baseCurrency->format();
		}

		if ('state' == $name) {
			do_action('sr_edit_reservation_status', $pk, $value);
		}

		wp_send_json(array('success' => $result, 'newValue' => $newValue));
	}

	public static function calculate_tariff() {
		check_ajax_referer( 'cal-tariff', 'security' );
		$adult_number = (int) isset( $_GET[ 'adult_number' ] ) ? $_GET[ 'adult_number' ] : 0 ;
		$child_number = (int) isset( $_GET[ 'child_number' ] ) ? $_GET[ 'child_number' ] : 0 ;
		$room_type_id = (int) isset( $_GET[ 'room_type_id' ] ) ? $_GET[ 'room_type_id' ] : 0 ;
		$room_index = (int) isset( $_GET[ 'room_index' ] ) ? $_GET[ 'room_index' ] : 0 ;
		// When reservation is made in backend, there is no room index, instead of that we use room id
		if ( $room_index == 'undefined' ) {
			$room_index = (int) isset( $_GET[ 'room_id' ] ) ? $_GET[ 'room_id' ] : 0 ;
		}
		$asset_id = (int) isset( $_GET[ 'raid' ] ) ? $_GET[ 'raid' ] : 0 ;
		$tariff_id = (int) isset( $_GET[ 'tariff_id' ] ) ? $_GET[ 'tariff_id' ] : 0 ;
		$adjoining_layer = isset( $_GET[ 'adjoining_layer' ] ) ? $_GET[ 'adjoining_layer' ] : 0;
		$currency_id = solidres()->session->get( 'sr_currency_id' );
		$tax_id = solidres()->session->get( 'sr_tax_id' );

		$checkin = solidres()->session->get( 'sr_checkin' );
		$checkout = solidres()->session->get( 'sr_checkout' );
		$coupon = solidres()->session->get( 'sr_coupon' );

		$options = get_option( 'solidres_plugin' );
		$show_price_with_tax = $options[ 'show_price_with_tax' ]  ;
		$is_discount_pre_tax = isset( $options[ 'discount_pre_tax' ] ) ? $options[ 'discount_pre_tax' ] : 0;
		$tariff_breakdown_net_or_gross = $show_price_with_tax == 1 ? 'net' : 'gross';

		// Get customer information
		$customer_group_id = null;
		if ( defined( 'SR_PLUGIN_USER_ENABLED' ) && SR_PLUGIN_USER_ENABLED && is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$customer_group_id = get_user_meta( $current_user->ID, 'customer_group_id', true );
			$customer_group_id = empty($customer_group_id) ? NULL : $customer_group_id;
		}

		$data = array(
			'asset_id' => $asset_id,
			'room_type_id' => $room_type_id,
			'tariff_id' => $tariff_id,
			'room_index' => $room_index,
			'adult_number' => $adult_number,
			'child_number' => $child_number,
			'show_price_with_tax' => $show_price_with_tax,
			'coupon' => $coupon,
			'tariff_breakdown_net_or_gross' => $tariff_breakdown_net_or_gross,
			'checkin' => $checkin,
			'checkout' => $checkout,
			'currency_id' => $currency_id,
			'tax_id' => $tax_id,
			'customer_group_id' => $customer_group_id,
			'adjoining_layer' => $adjoining_layer,
			'discount_pre_tax' => $is_discount_pre_tax
		);
		$asset = new SR_Asset();
		wp_send_json( $asset->calculate_tariff( $data ) );
	}

	public static function reservation_process() {
		check_ajax_referer( 'process-reservation', 'security' );
		$data = $_POST[ 'srform' ];
		$step = $_POST[ 'step' ];

		$solidres_reservation = new SR_Reservation();

		switch ( $step ) {
			case 'room':
				$solidres_reservation->process_room( $data );
				break;
			case 'guestinfo':
				$solidres_reservation->process_guest_info( $data );
				break;
			default:
				break;
		}
	}

	public static function reservation_progress() {
		check_ajax_referer( 'process-reservation', 'security' );
		$next = $_GET[ 'next_step' ];
		$solidres_reservation = new SR_Reservation();
		if ( ! empty( $next ) ) {
			switch ( $next ) {
				case 'guestinfo':
					$solidres_reservation->get_html_guest_info();
					break;
				case 'confirmation':
					$solidres_reservation->get_html_confirmation();
					break;
				default:
					$response = array( 'status' => 1, 'message' => '', 'next' => '' );
					echo json_encode( $response );
					break;
			}
		}
		wp_die();
	}
	
	public static function reservation_count_unread() {
		check_ajax_referer( 'reservation-count-unread', 'security' );
		$solidres_reservation = new SR_Reservation();

		$unread = $solidres_reservation->count_unread();

		echo json_encode( array( 'count' => $unread ) );
		wp_die();
	}

	public static function load_available_rooms() {
		check_ajax_referer( 'load-available-rooms', 'security' );

		$reservation_id = isset( $_GET[ 'id' ] ) ? $_GET[ 'id' ] : 0 ;
		$asset_id = isset( $_GET[ 'assetid' ] ) ? $_GET[ 'assetid' ] : 0 ;
		$checkin = isset( $_GET[ 'checkin' ] ) ? $_GET[ 'checkin' ] : '' ;
		$checkout = isset( $_GET[ 'checkout' ] ) ? $_GET[ 'checkout' ] : '' ;
		$state = isset( $_GET[ 'state' ] ) ? $_GET[ 'state' ] : 0 ;
		$payment_status = isset( $_GET[ 'payment_status' ] ) ? $_GET[ 'payment_status' ] : 0 ;
		$hub_dashboard = isset( $_GET[ 'hub_dashboard' ] ) ? $_GET[ 'hub_dashboard' ] : 0 ;
		$solidres_asset = new SR_Asset();
		$solidres_currency = new SR_Currency();
		$solidres_reservation = new SR_Reservation();
		$solidres_roomtype = new SR_Room_Type();

		$options_tariff = get_option('solidres_tariff');
		$options_plugin = get_option('solidres_plugin');
		$enable_adjoining_tariffs = isset($options_tariff[ 'enable_adjoining_tariffs' ]) ? $options_tariff[ 'enable_adjoining_tariffs' ] : 1;
		$adjoining_tariff_show_desc = isset($options_tariff[ 'adjoining_tariffs_show_desc' ]) ? $options_tariff[ 'adjoining_tariffs_show_desc' ] : 0;
		$child_max_age = isset($options_plugin[ 'child_max_age_limit' ]) ? $options_plugin[ 'child_max_age_limit' ] : 17;

		$reservation = $solidres_reservation->load( $reservation_id);
		$currency = $solidres_currency->load( $reservation->currency_id );
		$asset = $solidres_asset->load( $asset_id );
		$stay_length = (int) SR_Utilities::calculate_date_diff( $checkin, $checkout );

		if ( $reservation->booking_type == 1 ) {
			$stay_length ++;
		}

		$show_tax_incl = isset($options_plugin[ 'show_price_with_tax' ]) ? $options_plugin[ 'show_price_with_tax' ] : 0;
		
		$current_reservation_data = null;

		solidres()->session->set( 'sr_id', $reservation_id );
		solidres()->session->set( 'sr_checkin', $checkin );
		solidres()->session->set( 'sr_checkout', $checkout );
		solidres()->session->set( 'sr_state', $state );
		solidres()->session->set( 'sr_payment_status', $payment_status );
		solidres()->session->set( 'sr_hub_dashboard', $hub_dashboard ) ;
		solidres()->session->set( 'sr_is_guest_making_reservation', false );

		if ( $asset_id > 0 && $reservation_id > 0 ) {
			$current_reservation_data = $solidres_reservation->load( $reservation_id );

			$current_reservation_data->reserved_room_details = $solidres_reservation->load_reserved_rooms( $reservation_id );

			// We need to rebuild the data structure a little bit to make it easier for array looping here
			// The origina data structure for "reserved_room_details" array is numeric based (from 0, 1,...)
			// But we need the key of this array to be room's id
			$current_reservation_data->reserved_room_details_cloned = array();
			if (is_array($current_reservation_data->reserved_room_details))
			{
				$current_reservation_data->reserved_room_details_cloned = $current_reservation_data->reserved_room_details;
				$current_reservation_data->reserved_room_details = array();
				foreach ($current_reservation_data->reserved_room_details_cloned as $reserved_room_detail_cloned)
				{
					$current_reservation_data->reserved_room_details[$reserved_room_detail_cloned->room_id] = (array) clone $reserved_room_detail_cloned;
					// If guest also booked extra items for this room, we have to include it as well
					if (isset($reserved_room_detail_cloned->extras))
					{
						unset($current_reservation_data->reserved_room_details[$reserved_room_detail_cloned->room_id]['extras']);
						foreach ($reserved_room_detail_cloned->extras as $key => $reservedRoomExtra)
						{
							if ($reservedRoomExtra->room_id == $reserved_room_detail_cloned->room_id)
							{
								$current_reservation_data->reserved_room_details[$reserved_room_detail_cloned->room_id]['extras'][$reservedRoomExtra->extra_id]['quantity'] = $reservedRoomExtra->extra_quantity;
							}
						}
					}
				}
				unset($current_reservation_data->reserved_room_details_cloned);
			}
		}

		// Get the default currency
		/*$this->reservationData['currency_id'] = $currency->id;
		$this->reservationData['currency_code'] = $currency->currency_code;*/

		solidres()->session->set( 'sr_currency_id', $currency->id );
		solidres()->session->set( 'sr_currency_code', $currency->currency_code );
		solidres()->session->set( 'sr_deposit_required', $asset->deposit_required );
		solidres()->session->set( 'sr_deposit_is_percentage', $asset->deposit_is_percentage );
		solidres()->session->set( 'sr_deposit_amount', $asset->deposit_amount );
		solidres()->session->set( 'sr_deposit_by_stay_length', $asset->deposit_by_stay_length );
		solidres()->session->set( 'sr_deposit_include_extra_cost', $asset->deposit_include_extra_cost );
		solidres()->session->set( 'sr_tax_id', $asset->tax_id );
		solidres()->session->set( 'sr_booking_type', $asset->booking_type );

		$room_type_array = $solidres_roomtype->load_by_asset_id( $asset_id );

		foreach ( $room_type_array as $room_type_item ) {
			$room_types[] = $solidres_roomtype->load( $room_type_item->id );
		}

		$imposed_tax_types = array();
		if ( ! empty( $asset->tax_id ) ) {
			$solidres_tax = new SR_Tax;
			$imposed_tax_types[] = $solidres_tax->load( $asset->tax_id );
		}

		$solidres_currency = new SR_Currency( 0, $asset->currency_id );

		$solidres_room = new SR_Room();
		$solidres_tariff = new SR_Tariff();
		$solidres_extra = new SR_Extra();

		if ( !empty($room_types) ) {
			foreach ( $room_types as &$room_type ) {
				$rooms = $solidres_room->load_by_room_type_id( $room_type->id );

				// Tariff loading
				$standard_tariff   = null;
				$room_type->tariffs = array();
				if (!defined('SR_PLUGIN_COMPLEXTARIFF_ENABLED') || !SR_PLUGIN_COMPLEXTARIFF_ENABLED)  {
					$standard_tariff = $solidres_tariff->load_by_room_type_id( $room_type->id );
					$room_type_standard_tariff = null;
					if ( isset( $standard_tariff[0]->id ) ) {
						$room_type->tariffs[] = $solidres_tariff->load( $standard_tariff[0]->id );
					}
				} else {
					$complex_tariffs = NULL;
					$complex_tariffs = $solidres_tariff->load_by_room_type_id( $room_type->id, false, OBJECT, $checkin, $checkout, 1, $customer_group_id );
					foreach ( $complex_tariffs as $complex_tariff ) {

						if ( !empty( $complex_tariff->limit_checkin)) {
							if ( !empty($checkin) && !empty($checkout)) {
								$limit_checkin_array = $complex_tariff->limit_checkin;
								$checkinDate          = new DateTime($checkin);
								$dayInfo              = getdate($checkinDate->format('U'));

								// If the current check in date does not match the allowed check in dates, we ignore this tariff
								if (!in_array($dayInfo['wday'], $limit_checkin_array)) {
									continue;
								}
							}
						}

						if (!empty($rooms_occupancy_options)) {
							$is_valid_people_range    = true;
							$people_range_match_count = count($rooms_occupancy_options);

							foreach ($rooms_occupancy_options as $room_occupancy_options) {
								$total_people_requested = $room_occupancy_options['adults'] + $room_occupancy_options['children'];

								if ($complex_tariff->p_min > 0 && $complex_tariff->p_max > 0) {
									$is_valid_people_range = $total_people_requested >= $complex_tariff->p_min && $total_people_requested <= $complex_tariff->p_max;
								} elseif (empty($complex_tariff->p_min) && $complex_tariff->p_max > 0) {
									$is_valid_people_range = $total_people_requested <= $complex_tariff->p_max;
								} elseif ($complex_tariff->p_min > 0 && empty($complex_tariff->p_max)) {
									$is_valid_people_range = $total_people_requested >= $complex_tariff->p_min;
								}

								if (!$is_valid_people_range) {
									$people_range_match_count--;
								}
							}

							if ($people_range_match_count == 0) {
								continue;
							}
						}

						$room_type->tariffs[] = $solidres_tariff->load( $complex_tariff->id, false );
					}
				}

				if (!empty($checkin) && !empty($checkout)) {

					$solidres_roomtype = new SR_Room_Type();

					$coupon = solidres()->session->get( 'sr_coupon' );
					$customer_group_id = null;
					// Hard code the number of selected adult
					$adult = 1;
					$child = 0;

					// Check for number of available rooms first, if no rooms found, we should skip this room type
					$listAvailableRoom = $solidres_roomtype->getListAvailableRoom($room_type->id, $checkin, $checkout, $asset->booking_type);
					$room_type->totalAvailableRoom = is_array($listAvailableRoom) ? count($listAvailableRoom) : 0 ;

					// Build the config values
					$tariffConfig = array(
						'booking_type' => $asset->booking_type,
						'adjoining_tariffs_mode' => isset($options_tariff[ 'adjoining_tariffs_mode' ]) ? $options_tariff[ 'adjoining_tariffs_mode' ] : 0,
						'child_room_cost_calc' => isset($options_tariff[ 'child_room_cost_calc' ]) ? $options_tariff[ 'child_room_cost_calc' ] : 1,
						'adjoining_tariffs_show_desc' => $adjoining_tariff_show_desc
					);
					if (isset($room_type->params['enable_single_supplement'])
					    &&
					    $room_type->params['enable_single_supplement'] == 1)
					{
						$tariffConfig['enable_single_supplement'] = true;
						$tariffConfig['single_supplement_value'] = $room_type->params['single_supplement_value'];
						$tariffConfig['single_supplement_is_percent'] = $room_type->params['single_supplement_is_percent'];
					}
					else
					{
						$tariffConfig['enable_single_supplement'] = false;
					}

					// Get discount
					$discounts = array();
					$isDiscountPreTax = isset( $options_plugin[ 'discount_pre_tax' ] ) ? $options_plugin[ 'discount_pre_tax' ] : 0;
					if (defined( 'SR_PLUGIN_DISCOUNT_ENABLED' ) && SR_PLUGIN_DISCOUNT_ENABLED)
					{
						$solidres_discount = new SR_Discount();
						$discounts = $solidres_discount->load_discounts( $checkin, $checkout, array(1), 1, $asset_id);
					}

					// Holds all available tariffs (filtered) that takes checkin/checkout into calculation to be showed in front end
					$availableTariffs = array();
					$room_type->availableTariffs = array();
					if (defined( 'SR_PLUGIN_COMPLEXTARIFF_ENABLED' ) && SR_PLUGIN_COMPLEXTARIFF_ENABLED)
					{
						if (!empty($room_type->tariffs))
						{
							foreach ($room_type->tariffs as $filteredComplexTariff)
							{
								$availableTariffs[] = $solidres_roomtype->getPrice($room_type->id, $customer_group_id, $imposed_tax_types, false, true, $checkin, $checkout, $solidres_currency, $coupon, $adult, $child, array(), $stay_length, $filteredComplexTariff->id, $discounts, $isDiscountPreTax, $tariffConfig);
							}
						}
						/*else
						{*/
						if ($enable_adjoining_tariffs)
						{
							$isApplicableAdjoiningTariffs = SR_Utilities::isApplicableForAdjoiningTariffs($room_type->id, $checkin, $checkout);

							$tariffAdjoiningLayer = 0;
							$isApplicableAdjoiningTariffs2 = array();
							while (count($isApplicableAdjoiningTariffs) == 2)
							{
								$isApplicableAdjoiningTariffs2 = array_merge($isApplicableAdjoiningTariffs, $isApplicableAdjoiningTariffs2);
								$tariffConfig['adjoining_layer'] = $tariffAdjoiningLayer;
								$availableTariffs[] = $solidres_roomtype->getPrice($room_type->id, $customer_group_id, $imposed_tax_types, false, true, $checkin, $checkout, $solidres_currency, $coupon, $adult, $child, array(), $stay_length, NULL, $discounts, $isDiscountPreTax, $tariffConfig);
								$isApplicableAdjoiningTariffs = SR_Utilities::isApplicableForAdjoiningTariffs($room_type->id, $checkin, $checkout, $isApplicableAdjoiningTariffs2);
								if (empty($isApplicableAdjoiningTariffs))
								{
									break;
								}
								$tariffAdjoiningLayer ++;
							}
						}
						/*}*/
					}
					else
					{
						$availableTariffs[] = $solidres_roomtype->getPrice($room_type->id, $customer_group_id, $imposed_tax_types, true, false, $checkin, $checkout, $solidres_currency, $coupon, 0, 0, array(), $stay_length, $room_type->tariffs[0]->id, $discounts, $isDiscountPreTax, $tariffConfig);
					}

					foreach ($availableTariffs as $availableTariff)
					{
						$id = $availableTariff['id'];
						if ($show_tax_incl)
						{
							$room_type->availableTariffs[$id]['val'] = $availableTariff['total_price_tax_incl_discounted_formatted'];
						}
						else
						{
							$room_type->availableTariffs[$id]['val'] = $availableTariff['total_price_tax_excl_discounted_formatted'];
						}
						$room_type->availableTariffs[$id]['tariffTaxIncl'] = $availableTariff['total_price_tax_incl_discounted_formatted'];
						$room_type->availableTariffs[$id]['tariffTaxExcl'] = $availableTariff['total_price_tax_excl_discounted_formatted'];
						$room_type->availableTariffs[$id]['tariffIsAppliedCoupon'] = $availableTariff['is_applied_coupon'];
						$room_type->availableTariffs[$id]['tariffType'] = $availableTariff['type']; // Per room per night or Per person per night
						$room_type->availableTariffs[$id]['tariffBreakDown'] = $availableTariff['tariff_break_down'];
						// Useful for looping with Hub
						$room_type->availableTariffs[$id]['tariffTitle'] = $availableTariff['title'];
						$room_type->availableTariffs[$id]['tariffDescription'] = $availableTariff['description'];
						// For adjoining cases
						$room_type->availableTariffs[$id]['tariffAdjoiningLayer'] = $availableTariff['adjoining_layer'];
					}

					$tariffsForFilter = array();
					if (is_array($room_type->availableTariffs))
					{
						foreach ($room_type->availableTariffs as $tariffId => $tariffInfo)
						{
							if (is_null($tariffInfo['val']))
							{
								continue;
							}
							$tariffsForFilter[$tariffId] = $tariffInfo['val']->getValue();
						}
					}

					// Remove tariffs that has the same price
					$tariffsForFilter = array_unique($tariffsForFilter);
					foreach ($room_type->availableTariffs as $tariffId => $tariffInfo)
					{
						$uniqueTariffIds = array_keys($tariffsForFilter);
						if (!in_array($tariffId, $uniqueTariffIds))
						{
							unset($room_type->availableTariffs[$tariffId]);
						}
					}


					// Take overlapping mode into consideration
					$overlappingTariffsMode = isset($options_tariff[ 'overlapping_tariffs_mode' ]) ? $options_tariff[ 'overlapping_tariffs_mode' ] : 0;
					$tariffsForFilterOverlapping = $tariffsForFilter;
					asort($tariffsForFilterOverlapping); // from lowest to highest
					$lowestTariffId = NULL;
					$highestTariffId = NULL;
					switch ($overlappingTariffsMode)
					{
						case 0:
							break;
						case 1: // Lowest
							$tariffsForFilterOverlappingKeys = array_keys($tariffsForFilterOverlapping);
							$lowestTariffId = current($tariffsForFilterOverlappingKeys);
							SR_Utilities::removeArrayElementsExcept($room_type->availableTariffs, $lowestTariffId);
							break;
						case 2: // Highest
							$tariffsForFilterOverlappingKeys = array_keys($tariffsForFilterOverlapping);
							$highestTariffId = end($tariffsForFilterOverlappingKeys);
							SR_Utilities::removeArrayElementsExcept( $room_type->availableTariffs, $highestTariffId);
							break;
					}
				}

				if ( !empty( $rooms ) ) {
					// Get list of reserved rooms
					$reservedRoomsForThisReservation = $solidres_roomtype->getListReservedRoom($room_type->id, $reservation_id);
					$reservedRoomIds = array();
					foreach ($reservedRoomsForThisReservation as $roomObj) {
						$reservedRoomIds[] = $roomObj->id;
					}

					foreach ( $rooms as $room ) {
						$isAvailable = $solidres_reservation->is_room_available( $room->id, $checkin, $checkout, $asset->booking_type );
						$room->isAvailable = true;
						$room->isReservedForThisReservation = false;
						if (!$isAvailable)
						{
							$room->isAvailable = false;
						}

						if (in_array($room->id, $reservedRoomIds))
						{
							$room->isReservedForThisReservation = true;
						}
					}
				}

				$room_type->rooms = $rooms;

				$room_type->extras = $solidres_extra->load_by_room_type_id( $room_type->id, 1, $show_tax_incl );
			}
		}

		$display_data = array(
			'room_types' => $room_types,
			'raid' => $asset_id,
			'current_reservation_data' => $current_reservation_data,
			'childMaxAge' => $child_max_age,
			'currency' => $solidres_currency

		);

		$html = '';
		$path = WP_PLUGIN_DIR . '/solidres/templates/reservation/rooms.php';
		if ( file_exists( $path ) ) {
			ob_start();
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}

		echo $html;
		wp_die();
	}
}
SR_Ajax::init();