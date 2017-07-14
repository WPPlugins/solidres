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

if ( ! class_exists( 'Solidres_API' ) ) :

class Solidres_API {

	/** This is the major version for the REST API and takes
	 * first-order position in endpoint URLs
	 */
	const VERSION = '1.0.0';

	/** @var SR_API_Server the REST API server */
	public $server;

	/** @var SR_API_Authentication REST API authentication class instance */
	public $authentication;

	/**
	 * Setup class
	 *
	 * @since 2.0
	 */
	public function __construct() {
		// add query vars
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// register API endpoints
		add_action( 'init', array( $this, 'add_endpoint' ), 0 );

		// handle REST API requests
		add_action( 'parse_request', array( $this, 'handle_rest_api_requests' ), 0 );

		// handle sr-api endpoint requests
		add_action( 'parse_request', array( $this, 'handle_api_requests' ), 0 );

		// Ensure payment gateways are initialized in time for API requests
		add_action( 'solidres_api_request', array( 'Solidres_Payment_Gateways', 'get_instance' ), 0 );
	}

	/**
	 * Add new query vars.
	 *
	 * @since 2.0
	 * @param $vars
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'sr-api';
		$vars[] = 'sr-api-version';
		$vars[] = 'sr-api-route';
		return $vars;
	}

	/**
	 * Add new endpoints.
	 *
	 * @since 2.0
	 */
	public static function add_endpoint() {

		// REST API
		add_rewrite_rule( '^sr-api/v([1-3]{1})/?$', 'index.php?sr-api-version=$matches[1]&sr-api-route=/', 'top' );
		add_rewrite_rule( '^sr-api/v([1-3]{1})(.*)?', 'index.php?sr-api-version=$matches[1]&sr-api-route=$matches[2]', 'top' );

		// SR API for payment gateway IPNs, etc
		add_rewrite_endpoint( 'sr-api', EP_ALL );
	}


	/**
	 * Handle REST API requests
	 *
	 * @since 2.2
	 */
	public function handle_rest_api_requests() {
		global $wp;

		if ( ! empty( $_GET['sr-api-version'] ) ) {
			$wp->query_vars['sr-api-version'] = $_GET['sr-api-version'];
		}

		if ( ! empty( $_GET['sr-api-route'] ) ) {
			$wp->query_vars['sr-api-route'] = $_GET['sr-api-route'];
		}

		// REST API request
		if ( ! empty( $wp->query_vars['sr-api-version'] ) && ! empty( $wp->query_vars['sr-api-route'] ) ) {

			define( 'SR_API_REQUEST', true );
			define( 'SR_API_REQUEST_VERSION', absint( $wp->query_vars['sr-api-version'] ) );

			// legacy v1 API request
			if ( 1 === SR_API_REQUEST_VERSION ) {
				$this->handle_v1_rest_api_request();
			} else if ( 2 === SR_API_REQUEST_VERSION ) {
				$this->handle_v2_rest_api_request();
			} else {
				$this->includes();

				$this->server = new SR_API_Server( $wp->query_vars['sr-api-route'] );

				// load API resource classes
				$this->register_resources( $this->server );

				// Fire off the request
				$this->server->serve_request();
			}

			exit;
		}
	}

	/**
	 * Include required files for REST API request
	 *
	 * @since 2.1
	 */
	public function includes() {

		// API server / response handlers
		//include_once( 'api/class-sr-api-exception.php' );
		//include_once( 'api/class-sr-api-server.php' );
		include_once( 'api/interface-sr-api-handler.php' );
		//include_once( 'api/class-sr-api-json-handler.php' );

		// authentication
		//include_once( 'api/class-sr-api-authentication.php' );
		//$this->authentication = new SR_API_Authentication();

		//include_once( 'api/class-sr-api-resource.php' );
		//include_once( 'api/class-sr-api-assets.php' );
		//include_once( 'api/class-sr-api-webhooks.php' );

		// allow plugins to load other response handlers or resource classes
		do_action( 'solidres_api_loaded' );
	}

	/**
	 * Register available API resources
	 *
	 * @since 2.1
	 * @param SR_API_Server $server the REST server
	 */
	public function register_resources( $server ) {

		$api_classes = array();
		/*$api_classes = apply_filters( 'woocommerce_api_classes',
			array(
				'SR_API_Assets',
				'SR_API_Webhooks',
			)
		);*/

		foreach ( $api_classes as $api_class ) {
			$this->$api_class = new $api_class( $server );
		}
	}


	/**
	 * Handle legacy v1 REST API requests.
	 *
	 * @since 2.2
	 */
	private function handle_v1_rest_api_request() {

		// include legacy required files for v1 REST API request
		// include_once( 'api/v1/class-sr-api-server.php' );
		include_once( 'api/v1/interface-sr-api-handler.php' );
		// include_once( 'api/v1/class-sr-api-json-handler.php' );
		// include_once( 'api/v1/class-sr-api-xml-handler.php' );

		// include_once( 'api/v1/class-sr-api-authentication.php' );
		// $this->authentication = new SR_API_Authentication();

		// include_once( 'api/v1/class-sr-api-resource.php' );
		// include_once( 'api/v1/class-sr-api-assets.php' );

		// allow plugins to load other response handlers or resource classes
		do_action( 'solidres_api_loaded' );

		//$this->server = new SR_API_Server( $GLOBALS['wp']->query_vars['sr-api-route'] );

		// Register available resources for legacy v1 REST API request
		$api_classes = array();
		/*$api_classes = apply_filters( 'solidres_api_classes',
			array(
				'SR_API_Assets',
			)
		);*/

		// foreach ( $api_classes as $api_class ) {
		//	$this->$api_class = new $api_class( $this->server );
		// }

		// Fire off the request
		// $this->server->serve_request();
	}

	/**
	 * API request - Trigger any API requests.
	 *
	 * @since    2.0
	 * @version  2.4
	 */
	public function handle_api_requests() {
		global $wp;

		if ( ! empty( $_GET['sr-api'] ) ) {
			$wp->query_vars['sr-api'] = $_GET['sr-api'];
		}

		// sr-api endpoint requests
		if ( ! empty( $wp->query_vars['sr-api'] ) ) {

			// Buffer, we won't want any output here
			ob_start();

			// No cache headers
			nocache_headers();

			// Clean the API request
			$api_request = strtolower( sanitize_text_field( $wp->query_vars['sr-api'] ) );

			// Trigger generic action before request hook
			do_action( 'solidres_api_request', $api_request );

			// Is there actually something hooked into this API request? If not trigger 400 - Bad request
			status_header( has_action( 'solidres_api_' . $api_request ) ? 200 : 400 );

			// Trigger an action which plugins can hook into to fulfill the request
			do_action( 'solidres_api_' . $api_request );

			// Done, clear buffer and exit
			ob_end_clean();
			die('-1');
		}
	}
}

endif;

return new Solidres_API();
