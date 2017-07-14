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

class SR_Form_Handler {

	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'save_account_details' ) );
		add_action( 'init', array( __CLASS__, 'save_reservation' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'startover_reservation' ), 20 );
	}

	public static function save_reservation() {
		$task = isset( $_POST['task'] ) ? $_POST['task']  : '';
		if ( $task == 'save_reservation' ) {
			$solidres_reservation = new SR_Reservation();
			$context = 'com_solidres.reservation.process';
			$is_guest_making_reservation = solidres()->session->get( 'sr_is_guest_making_reservation' );

			if ( !$is_guest_making_reservation ) {
				// Get overridden cost
				$overridden_cost = isset( $_POST[ 'srform' ] ) ?  $_POST[ 'srform' ] : array();

				// Get current cost
				$room_type_prices_mapping = solidres()->session->get( 'sr_room_type_prices_mapping' );
				$cost = solidres()->session->get( 'sr_cost' );
				$reservation_rooms = solidres()->session->get( 'sr_room' );
				$reservation_guest = solidres()->session->get( 'sr_guest' );
				$deposit = solidres()->session->get( 'sr_deposit' );

				$total_price_tax_excl = 0;
				$total_imposed_tax_amount = 0;
				$total_room_type_extra_cost_tax_excl = 0;
				$total_room_type_extra_cost_tax_incl = 0;
				$total_per_booking_extra_cost_tax_incl = 0;
				$total_per_booking_extra_cost_tax_excl = 0;
				foreach ($overridden_cost['override_cost']['room_types'] as $room_type_id => $tariffs)
				{
					foreach ($tariffs as $tariff_id => $rooms)
					{
						foreach ($rooms as $room_id => $room)
						{
							$total_price_tax_excl += $room['total_price_tax_excl'];

							$total_imposed_tax_amount += $room['tax_amount'];
							$room_total_price_tax_incl = $room['total_price_tax_excl'] + $room['tax_amount'];

							$room_type_prices_mapping[$room_type_id][$tariff_id][$room_id]['total_price'] = $room_total_price_tax_incl;
							$room_type_prices_mapping[$room_type_id][$tariff_id][$room_id]['total_price_tax_incl'] = $room_total_price_tax_incl;
							$room_type_prices_mapping[$room_type_id][$tariff_id][$room_id]['total_price_tax_excl'] = $room['total_price_tax_excl'];

							// Override extra cost
							if (is_array($room['extras']))
							{
								foreach ($room['extras'] as $overridden_extra_key => $overridden_extra_cost)
								{
									$reservation_rooms['room_types'][$room_type_id][$tariff_id][$room_id]['extras'][$overridden_extra_key]['total_extra_cost_tax_incl'] = $overridden_extra_cost['price'] + $overridden_extra_cost['tax_amount'];
									$reservation_rooms['room_types'][$room_type_id][$tariff_id][$room_id]['extras'][$overridden_extra_key]['total_extra_cost_tax_excl'] = $overridden_extra_cost['price'];
									$total_room_type_extra_cost_tax_incl += $reservation_rooms['room_types'][$room_type_id][$tariff_id][$room_id]['extras'][$overridden_extra_key]['total_extra_cost_tax_incl'];
									$total_room_type_extra_cost_tax_excl += $reservation_rooms['room_types'][$room_type_id][$tariff_id][$room_id]['extras'][$overridden_extra_key]['total_extra_cost_tax_excl'];

								}
							}

						}
					}
				}

				// Override extra per booking if available
				if (is_array($overridden_cost['override_cost']['extras_per_booking']))
				{
					foreach ($overridden_cost['override_cost']['extras_per_booking'] as $overridden_extra_booking_key => $overridden_extra_booking_cost)
					{
						$reservation_guest['extras'][$overridden_extra_booking_key]['total_extra_cost_tax_incl'] = $overridden_extra_booking_cost['price'] + $overridden_extra_booking_cost['tax_amount'];
						$reservation_guest['extras'][$overridden_extra_booking_key]['total_extra_cost_tax_excl'] = $overridden_extra_booking_cost['price'];
						$total_per_booking_extra_cost_tax_incl += $reservation_guest['extras'][$overridden_extra_booking_key]['total_extra_cost_tax_incl'];
						$total_per_booking_extra_cost_tax_excl += $reservation_guest['extras'][$overridden_extra_booking_key]['total_extra_cost_tax_excl'];
					}
				}

				$total_price_tax_incl = $total_price_tax_excl + $total_imposed_tax_amount;
				$reservation_rooms['total_extra_price_per_room'] = $total_room_type_extra_cost_tax_incl;
				$reservation_rooms['total_extra_price_tax_incl_per_room'] = $total_room_type_extra_cost_tax_incl;
				$reservation_rooms['total_extra_price_tax_excl_per_room'] = $total_room_type_extra_cost_tax_excl;

				$reservationGuest['total_extra_price_per_booking'] = $total_per_booking_extra_cost_tax_incl;
				$reservationGuest['total_extra_price_tax_incl_per_booking'] = $total_per_booking_extra_cost_tax_incl;
				$reservationGuest['total_extra_price_tax_excl_per_booking'] = $total_per_booking_extra_cost_tax_excl;

				$cost['total_price'] = $total_price_tax_incl;
				$cost['total_price_tax_incl'] = $total_price_tax_incl;
				$cost['total_price_tax_excl'] = $total_price_tax_excl;
				$cost['tax_amount'] = $total_imposed_tax_amount;
				$deposit['deposit_amount'] = $overridden_cost['override_cost']['deposit_amount'];

				// Update existing prices with overridden prices
				solidres()->session->set( 'sr_cost', $cost );
				solidres()->session->set( 'sr_room_type_prices_mapping', $room_type_prices_mapping );
				solidres()->session->set( 'sr_room', $reservation_rooms );
				solidres()->session->set( 'sr_guest', $reservation_guest );
				solidres()->session->set( 'sr_deposit', $deposit );
			}

			$room_data        = solidres()->session->get( 'sr_room' );
			$reservation_data = array();
			if ( is_array( $room_data ) ) {
				$reservation_data = array_merge( $reservation_data, $room_data );
			}

			$guest_data = solidres()->session->get( 'sr_guest' );
			if ( is_array( $guest_data ) ) {
				$reservation_data = array_merge( $reservation_data, $guest_data );
			}

			$cost_data = solidres()->session->get( 'sr_cost' );
			if ( is_array( $cost_data ) ) {
				$reservation_data = array_merge( $reservation_data, $cost_data );
			}

			$discount_data = solidres()->session->get( 'sr_discount' );
			if ( is_array( $discount_data ) ) {
				$reservation_data = array_merge( $reservation_data, $discount_data );
			}

			$coupon_data = solidres()->session->get( 'sr_coupon' );
			if ( is_array( $coupon_data ) ) {
				$reservation_data = array_merge( $reservation_data, $coupon_data );
			}

			$deposit_data = solidres()->session->get( 'sr_deposit' );
			if ( is_array( $deposit_data ) ) {
				$reservation_data = array_merge( $reservation_data, $deposit_data );
			}

			$reservation_data['total_extra_price']          = $reservation_data['total_extra_price_per_room'] + $reservation_data['total_extra_price_per_booking'];
			$reservation_data['total_extra_price_tax_incl'] = $reservation_data['total_extra_price_tax_incl_per_room'] + $reservation_data['total_extra_price_tax_incl_per_booking'];
			$reservation_data['total_extra_price_tax_excl'] = $reservation_data['total_extra_price_tax_excl_per_room'] + $reservation_data['total_extra_price_tax_excl_per_booking'];

			$solidres_asset                             = new SR_Asset();
			$asset                                      = $solidres_asset->load( $reservation_data['raid'] );
			$reservation_data['reservation_asset_name'] = apply_filters( 'solidres_asset_name', $asset->name );
			$reservation_data['reservation_asset_id']   = $reservation_data['raid'];
			$reservation_data['currency_id']            = solidres()->session->get( 'sr_currency_id' );
			$reservation_data['currency_code']          = solidres()->session->get( 'sr_currency_code' );
			$reservation_data['checkin']                = solidres()->session->get( 'sr_checkin' );
			$reservation_data['checkout']      = solidres()->session->get( 'sr_checkout' );
			$reservation_data['created_date']  = date( 'Y-m-d H:i:s', time() );
			$reservation_data['modified_date'] = date( 'Y-m-d H:i:s', time() );
			$reservation_data['created_by']    = 0;
			$reservation_data['modified_by']   = 0;
			$reservation_data['payment_data']  = $reservation_data[ 'payment_method_id' ] == 'offline' ? $reservation_data['offline'] : '';
			$reservation_data['id']            = solidres()->session->get( 'sr_id' );
			$reservation_data['booking_type']  = solidres()->session->get( 'sr_booking_type' );

			$result = $solidres_reservation->save( $reservation_data );

			if ( false == $result ) {
				// Fail, turn back and correct
			} else {

				$reservation = $solidres_reservation->load( solidres()->session->get( 'sr_saved_reservation_id' ) );

				$payment_gateways = solidres()->payment_gateways();

				foreach ( $payment_gateways->get_payment_gateways() as $key => $gateway ) {
					if( $reservation->payment_method_id == $gateway->identifier ) {
						if ($gateway->no_process) {
							break;
						}
						$result = $gateway->process_payment( $reservation );
						// Redirect to success/confirmation/payment page
						if ( $result['result'] == 'success' ) {

							$result = apply_filters( 'solidres_payment_successful_result', $result, $reservation );

							if ( solidres()->is_request( 'ajax' ) ) {
								wp_send_json( $result );
							} else {
								wp_redirect( $result['redirect'] );
								exit;
							}

						}
					}
				}

				do_action( 'sr_reservation_finalize', $context, $reservation);

				$is_guest_making_reservation = solidres()->session->get( 'sr_is_guest_making_reservation' );
				if ( isset( $is_guest_making_reservation ) && $is_guest_making_reservation == true) {
					wp_redirect(
						solidres_get_reservation_completed_url( $reservation )
					);
				} else {
					wp_redirect(
						admin_url( 'admin.php?page=sr-reservations&action=edit&id='. $reservation->id )
					);
				}

				exit;
			}
		}
	}

	public static function send_reservation_email( $reservation ) {
		// Send confirmation emails
		$solidres_reservation = new SR_Reservation();
		$solidres_room_type   = new SR_Room_Type();
		$solidres_asset       = new SR_Asset();
		$solidres_state       = new SR_State();
		$subject              = array();
		$body                 = array();
		$tzoffset             = get_option( 'timezone_string' );
		$tzoffset             = $tzoffset == '' ? 'UTC' : $tzoffset;
		$timezone             = new DateTimeZone( $tzoffset );
		$dateFormat           = get_option( 'date_format', 'd-m-Y' );
		$saved_reservation_id   = solidres()->session->get( 'sr_saved_reservation_id' );
		$reserved_room_details  = $solidres_reservation->load_reserved_rooms( $saved_reservation_id );
		$reserved_extras        = $solidres_reservation->load_extras( $saved_reservation_id );
		$stay_length            = SR_Utilities::calculate_date_diff( $reservation->checkin, $reservation->checkout );
		$asset                  = $solidres_asset->load( $reservation->reservation_asset_id );
		$asset_custom_fields    = $solidres_asset->load_custom_fields( $reservation->reservation_asset_id );
		$options_plugin         = get_option( 'solidres_plugin' );
		$asset->name            = apply_filters( 'solidres_asset_name', $asset->name );
		$asset_params           = json_decode( $asset->params, true );
		$asset_state            = null;
		if ($asset->geo_state_id) {
			$asset_state        = $solidres_state->load($asset->geo_state_id);
		}
		$hotelEmail             = $asset->email;
		// Hold a list of emails (commas seprated) that Solidres will send notification to (beside customer's email)
		$notification_emails = array();
		$notification_emails[] = $asset->email;

		$additional_notification_emails = array();
		if ( isset( $asset_params['additional_notification_emails'] ) && !empty( $asset_params['additional_notification_emails'] ) ) {
			$additional_notification_emails = explode(',', $asset_params['additional_notification_emails']);
			$notification_emails[] = $asset_params['additional_notification_emails'];
		}

		$customerEmail    = $reservation->customer_email;
		$hotelEmailList[] = $hotelEmail;
		// If User plugin is installed and enabled
		if ( defined( 'SR_PLUGIN_USER_ENABLED' ) && SR_PLUGIN_USER_ENABLED && ! is_null( $asset->partner_id ) ) {
			$partner = get_user_by( 'id', $asset->partner_id );
			if ( ! empty( $partner->user_email ) && $partner->user_email != $hotelEmail && !in_array($partner->user_email, $additional_notification_emails) ) {
				$hotelEmailList[] = $partner->user_email;
			}
		}

		$subject[ $customerEmail ] = __( 'Your reservation is completed', 'solidres' );
		$subject[ $hotelEmail ]    = sprintf(__( 'New reservation %s from %s %s', 'solidres' ), $reservation->code, $reservation->customer_firstname, $reservation->customer_lastname);

		$bankWireInstructions = array();
		if ( $reservation->payment_method_id == 'bankwire' ) {
			$solidres_payment_config_data            = new SR_Config( array( 'scope_id' => $reservation->reservation_asset_id ) );
			$bankWireInstructions['account_name']    = $solidres_payment_config_data->get( 'payments/bankwire/bankwire_accountname' );
			$bankWireInstructions['account_details'] = $solidres_payment_config_data->get( 'payments/bankwire/bankwire_accountdetails' );
		}

		// We are free to choose between the inliner version and noninliner version
		// Inliner version is hard to maintain but it displays well in gmail (web).
		$reservationCompleteCustomerEmailTemplate = WP_PLUGIN_DIR . '/solidres/templates/emails/reservation_complete_customer_html_inliner.php';
		$reservationCompleteOwnerEmailTemplate    = WP_PLUGIN_DIR . '/solidres/templates/emails/reservation_complete_owner_html_inliner.php';

		// Prepare some currency data to be showed
		$baseCurrency = new SR_Currency( 0, $reservation->currency_id );
		$subTotal     = clone $baseCurrency;
		$subTotal->set_value( $reservation->total_price_tax_excl );
		$discountTotal = clone $baseCurrency;
		$discountTotal->set_value($reservation->total_discount);
		$tax = clone $baseCurrency;
		$tax->set_value( $reservation->total_price_tax_incl - $reservation->total_price_tax_excl );
		$totalExtraPriceTaxExcl = clone $baseCurrency;
		$totalExtraPriceTaxExcl->set_value( $reservation->total_extra_price_tax_excl );
		$extraTax = clone $baseCurrency;
		$extraTax->set_value( $reservation->total_extra_price_tax_incl - $reservation->total_extra_price_tax_excl );
		$grandTotal = clone $baseCurrency;
		if ($reservation->discount_pre_tax) {
			$grandTotal->set_value($reservation->total_price_tax_excl - $reservation->total_discount + $reservation->tax_amount + $reservation->total_extra_price);
		} else {
			$grandTotal->set_value($reservation->total_price_tax_excl + $reservation->tax_amount  - $reservation->total_discount + $reservation->total_extra_price);
		}
		$depositAmount = clone $baseCurrency;
		$depositAmount->set_value( isset( $reservation->deposit_amount ) ? $reservation->deposit_amount : 0 );
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

		$display_data = array(
			'reservation'                         => $reservation,
			'reserved_room_details'               => $reserved_room_details,
			'reserved_extras'                     => $reserved_extras,
			'sub_total'                           => $subTotal->format(),
			'total_discount'                      => $reservation->total_discount > 0.00 ? $discountTotal->format() : NULL,
			'tax'                                 => $tax->format(),
			'total_extra_price_tax_excl'          => $totalExtraPriceTaxExcl->format(),
			'extra_tax'                           => $extraTax->format(),
			'grand_total'                         => $grandTotal->format(),
			'stay_length'                         => $stay_length,
			'deposit_amount'                      => $depositAmount->format(),
			'bankwire_instructions'               => $bankWireInstructions,
			'asset'                               => $asset,
			'asset_custom_fields'                 => $custom_field_data,
			'date_format'                         => $dateFormat,
			'timezone'                            => $timezone,
			'base_currency'                       => $baseCurrency,
			'payment_method_custom_email_content' => solidres()->session->get( 'sr_payment_method_custom_email_content' ),
			'discount_pre_tax'                    => $options_plugin['discount_pre_tax'],
			'social_network'                      => $social_network,
			'asset_state'                         => $asset_state
		);

		$attachment_pdf = '';
		if ( defined('SR_PLUGIN_INVOICE_ENABLED') && SR_PLUGIN_INVOICE_ENABLED ) {
			$pdf                                    = null;
			$pdfAttachmentFolder                    = WP_PLUGIN_DIR . '/solidres-invoice/libraries/pdf-attachment/';
			$reservationCompleteCustomerPdfTemplate = WP_PLUGIN_DIR . '/solidres-invoice/layouts/emails/reservation_complete_customer_pdf.php';
			if ( file_exists( $reservationCompleteCustomerPdfTemplate ) ) {
				ob_start();
				include $reservationCompleteCustomerPdfTemplate;
				$pdf = ob_get_contents();
				ob_end_clean();
			}

			$pdf_attachment = $pdf;
			$invoices       = new SR_Invoice();
			$fileName       = $invoices->createPDF( $pdf_attachment, $reservation->code, 1 );

			$options_invoice       = get_option( 'solidres_invoice' );
			$enable_pdf_attachment = isset( $options_invoice['enable_pdf_attachment'] ) ? $options_invoice['enable_pdf_attachment'] : 1;
			if ( $enable_pdf_attachment == 1 ) {
				$attachment_pdf = $pdfAttachmentFolder . $fileName;
			}
		}

		// Send to customer
		if ( file_exists( $reservationCompleteCustomerEmailTemplate ) ) {
			ob_start();
			include $reservationCompleteCustomerEmailTemplate;
			$body[ $customerEmail ] = ob_get_contents();
			ob_end_clean();
		}

		$headers[] = "From: {$asset->name}  <{$asset->email}>";
		add_filter( 'wp_mail_content_type', 'solidres_set_html_content_type' );
		$attachment = array( $attachment_pdf );
		wp_mail( $customerEmail, $subject[ $customerEmail ], $body[ $customerEmail ], $headers, $attachment );
		remove_filter( 'wp_mail_content_type', 'solidres_set_html_content_type' );

		// Send to the hotel owner
		if ( file_exists( $reservationCompleteOwnerEmailTemplate ) ) {
			ob_start();
			include $reservationCompleteOwnerEmailTemplate;
			$body[ $hotelEmail ] = ob_get_contents();
			ob_end_clean();
		}

		$headers[] = "From: {$asset->name}  <{$asset->email}>";
		add_filter( 'wp_mail_content_type', 'solidres_set_html_content_type' );
		wp_mail( implode( ',', $notification_emails ), $subject[ $hotelEmail ], $body[ $hotelEmail ], $headers );
		remove_filter( 'wp_mail_content_type', 'solidres_set_html_content_type' );
	}

	public static function startover_reservation() {
		if ( ! empty( $_GET['startover'] ) && $_GET['startover'] == 1 ) {
			$redirect_link = get_site_url() . '/' . get_page_uri( solidres()->session->get( 'sr_wp_page_id' ) );
			solidres_reservation_cleanup();
			wp_redirect( apply_filters( 'solidres_startover_redirect', $redirect_link ) );
			exit;
		}
	}

	public static function process_login() {
		if ( ! empty( $_POST['login'] ) && ! empty( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'solidres-login' ) ) {

			try {
				$creds    = array();
				$username = trim( $_POST['username'] );

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'solidres_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error', 'solidres' ) . ':</strong> ' . $validation_error->get_error_message() );
				}

				if ( empty( $username ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'solidres' ) . ':</strong> ' . __( 'Username is required.', 'solidres' ) );
				}

				if ( empty( $_POST['password'] ) ) {
					throw new Exception( '<strong>' . __( 'Error', 'solidres' ) . ':</strong> ' . __( 'Password is required.', 'solidres' ) );
				}

				if ( is_email( $username ) && apply_filters( 'solidres_get_username_from_email', true ) ) {
					$user = get_user_by( 'email', $username );

					if ( isset( $user->user_login ) ) {
						$creds['user_login'] = $user->user_login;
					} else {
						throw new Exception( '<strong>' . __( 'Error', 'solidres' ) . ':</strong> ' . __( 'A user could not be found with this email address.', 'solidres' ) );
					}

				} else {
					$creds['user_login'] = $username;
				}

				$creds['user_password'] = $_POST['password'];
				$creds['remember']      = isset( $_POST['rememberme'] );
				$secure_cookie          = is_ssl() ? true : false;
				$user                   = wp_signon( apply_filters( 'solidres_login_credentials', $creds ), $secure_cookie );

				if ( is_wp_error( $user ) ) {
					$message = $user->get_error_message();
					$message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $username ) . '</strong>', $message );
					throw new Exception( $message );
				} else {

					if ( ! empty( $_POST['redirect'] ) ) {
						$redirect = $_POST['redirect'];
					} elseif ( wp_get_referer() ) {
						$redirect = wp_get_referer();
					} else {
						$redirect = solidres_get_page_permalink( 'customerdashboard' );
					}

					// Feedback
					solidres_add_notice( sprintf( __( 'You are now logged in as <strong>%s</strong>', 'solidres' ), $user->display_name ) );

					wp_redirect( apply_filters( 'solidres_login_redirect', $redirect, $user ) );
					exit;
				}

			} catch (Exception $e) {

				solidres_add_notice( apply_filters('login_errors', $e->getMessage() ), 'error' );

			}
		}
	}

	public static function save_account_details() {

		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}

		if ( empty( $_POST[ 'action' ] ) || 'save_account_details' !== $_POST[ 'action' ] || empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'save_account_details' ) ) {
			return;
		}

		$errors       = new WP_Error();
		$user         = new stdClass();

		$user->ID     = (int) get_current_user_id();
		$current_user = get_user_by( 'id', $user->ID );

		if ( $user->ID <= 0 ) {
			return;
		}

		$first_name = ! empty( $_POST[ 'firstname' ] ) ? sanitize_text_field( $_POST[ 'firstname' ] ) : '';
		$last_name  = ! empty( $_POST[ 'lastname' ] ) ? sanitize_text_field( $_POST[ 'lastname' ] ) : '';
		$account_email      = ! empty( $_POST[ 'email' ] ) ? sanitize_email( $_POST[ 'email' ] ) : '';
		$pass_cur           = ! empty( $_POST[ 'password_current' ] ) ? $_POST[ 'password_current' ] : '';
		$pass1              = ! empty( $_POST[ 'password_1' ] ) ? $_POST[ 'password_1' ] : '';
		$pass2              = ! empty( $_POST[ 'password_2' ] ) ? $_POST[ 'password_2' ] : '';
		$save_pass          = true;

		$user->first_name   = $first_name;
		$user->last_name    = $last_name;

		// Prevent emails being displayed, or leave alone.
		$user->display_name = is_email( $current_user->display_name ) ? $user->first_name : $current_user->display_name;

		// Handle required fields
		$required_fields = apply_filters( 'solidres_save_account_details_required_fields', array(
			'firstname' => __( 'First Name', 'solidres' ),
			'lastname'  => __( 'Last Name', 'solidres' ),
			'email'      => __( 'Email address', 'solidres' ),
		) );

		foreach ( $required_fields as $field_key => $field_name ) {
			if ( empty( $_POST[ $field_key ] ) ) {
				solidres_add_notice( '<strong>' . esc_html( $field_name ) . '</strong> ' . __( 'is a required field.', 'solidres' ), 'error' );
			}
		}

		if ( $account_email ) {
			if ( ! is_email( $account_email ) ) {
				solidres_add_notice( __( 'Please provide a valid email address.', 'solidres' ), 'error' );
			} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
				solidres_add_notice( __( 'This email address is already registered.', 'solidres' ), 'error' );
			}
			$user->user_email = $account_email;
		}

		if ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
			solidres_add_notice( __( 'Your current password is incorrect.', 'solidres' ), 'error' );
			$save_pass = false;
		}

		if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
			solidres_add_notice( __( 'Please fill out all password fields.', 'solidres' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
			solidres_add_notice( __( 'Please enter your current password.', 'solidres' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			solidres_add_notice( __( 'Please re-enter your password.', 'solidres' ), 'error' );
			$save_pass = false;
		} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			solidres_add_notice( __( 'New passwords do not match.', 'solidres' ), 'error' );
			$save_pass = false;
		}

		save_user_profile_field( $user->ID );

		if ( $pass1 && $save_pass ) {
			$user->user_pass = $pass1;
		}

		// Allow plugins to return their own errors.
		do_action_ref_array( 'solidres_save_account_details_errors', array( &$errors, &$user ) );

		if ( $errors->get_error_messages() ) {
			foreach ( $errors->get_error_messages() as $error ) {
				solidres_add_notice( $error, 'error' );
			}
		}

		if ( solidres_notice_count( 'error' ) === 0 ) {

			wp_update_user( $user ) ;

			solidres_add_notice( __( 'Account details changed successfully.', 'solidres' ) );

			do_action( 'solidres_save_account_details', $user->ID );

			wp_safe_redirect( solidres_get_page_permalink( 'customerdashboard' ) );
			exit;
		}
	}
}

SR_Form_Handler::init();