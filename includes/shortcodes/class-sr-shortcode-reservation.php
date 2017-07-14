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

class Solidres_Shortcode_Reservation {

	public static function get( $atts ) {
		return Solidres_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	public static function output( $atts ) {
		global $wp;

		if ( isset( $wp->query_vars['reservation-id'] ) ) {
			self::reservation_completed( $wp->query_vars['reservation-id'] );
		}
	}

	public static function reservation_completed( $reservation_id = 0 ) {

		$reservation_code = !empty( $_GET[ 'code' ] ) ? $_GET[ 'code' ] : '';

		if ( $reservation_id > 0 ) {
			$solidres_reservation = new SR_Reservation();
			$solidres_asset = new SR_Asset();
			$solidres_country = new SR_Country();
			$reservation = $solidres_reservation->load( $reservation_id );
			if ( $reservation->code != $reservation_code) {
				return;
			}

			SR_Form_Handler::send_reservation_email( $reservation );

			$reservation->reserved_room_details = $solidres_reservation->load_reserved_rooms( $reservation_id );
			$asset = $solidres_asset->load( $reservation->reservation_asset_id );
			$country = $solidres_country->load( $reservation->customer_country_id );
			$length_of_stay = (int) SR_Utilities::calculate_date_diff(
				$reservation->checkin,
				$reservation->checkout
			);
		}

		solidres_get_template( 'reservation/final.php', array(
			'reservation' => $reservation,
			'asset' => $asset,
			'country' => $country,
			'length_of_stay' => $length_of_stay
		) );
	}
}