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

if ( ! class_exists( 'Solidres_Query' ) ) :

class Solidres_Query {

	/** @public array Query vars to add to wp */
	public $query_vars = array();

	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );

		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
			add_action( 'parse_request', array( $this, 'parse_request'), 0 );
		}

		$this->init_query_vars();
	}

	public function init_query_vars() {
		$options_page = get_option( 'solidres_pages' );
		$this->query_vars = array(
			'reservation-id'     => get_option( 'solidres_reservation_completed_endpoint', 'reservation-id' ), // currently hard coded
			'view-reservation'     => isset($options_page[ 'customerdashboard_viewreservation_endpoint' ]) ? $options_page[ 'customerdashboard_viewreservation_endpoint' ] : 'view-reservation',
			'cancel-reservation'     => isset($options_page[ 'customerdashboard_cancelreservation_endpoint' ]) ? $options_page[ 'customerdashboard_cancelreservation_endpoint' ] : 'cancel-reservation',
			'edit-account'       => isset($options_page[ 'customerdashboard_editaccount_endpoint' ]) ? $options_page[ 'customerdashboard_editaccount_endpoint' ] : 'edit-account',
			'sr-customer-logout'       => isset($options_page[ 'customerdashboard_logout_endpoint' ]) ? $options_page[ 'customerdashboard_logout_endpoint' ] : 'customer-logout',
		);
	}

	public function get_endpoint_title( $endpoint ) {
		global $wp;

		switch ( $endpoint ) {
			case 'reservation-id' :
				$title = __( 'Reservation Completed', 'woocommerce' );
				break;
			case 'view-reservation' :
				$title = __( 'View reservation', 'woocommerce' );
				break;
			default :
				$title = '';
				break;
		}

		return $title;
	}

	public function add_endpoints() {
		foreach ( $this->query_vars as $key => $var ) {
			add_rewrite_endpoint( $var, EP_ROOT | EP_PAGES );
		}
	}

	public function add_query_vars( $vars ) {
		foreach ( $this->query_vars as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	public function get_query_vars() {
		return $this->query_vars;
	}

	public function get_current_endpoint() {
		global $wp;
		foreach ( $this->get_query_vars() as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}
		return '';
	}

	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( $this->query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = $_GET[ $var ];
			}

			elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}
}

endif;

return new Solidres_Query();