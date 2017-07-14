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

class Solidres_Shortcodes {
	public static function init() {
		$shortcodes = array(
			'solidres_reservation' => __CLASS__ . '::reservation',
			'solidres_customer_dashboard' => __CLASS__ . '::customer_dashboard',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'solidres',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	public static function reservation( $atts ) {
		return self::shortcode_wrapper( array( 'Solidres_Shortcode_Reservation', 'output' ), $atts );
	}

	public static function customer_dashboard( $atts ) {

		if ( !defined( 'SR_PLUGIN_USER_ENABLED' ) || !SR_PLUGIN_USER_ENABLED) {
			return;
		}

		return self::shortcode_wrapper( array( 'Solidres_Shortcode_Customer_Dashboard', 'output' ), $atts );
	}
}