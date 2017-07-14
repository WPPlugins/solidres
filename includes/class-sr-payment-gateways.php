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

class Solidres_Payment_Gateways {

	public $payment_gateways;

	public $lookup;

	protected static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->init();
	}

	public function init() {
		$gateways = array(
			'Solidres_Payment_Gateway_Paylater',
			'Solidres_Payment_Gateway_Bankwire'
		);

		$gateways = apply_filters( 'solidres_register_payment_gateways', $gateways );

		foreach ($gateways as $gateway) {
			$init_gateway = is_string($gateway) ? new $gateway() : $gateway;
			$this->payment_gateways[] = $init_gateway;
			// For easy reference and searching
			$this->lookup[ $init_gateway->identifier ] = $init_gateway;
		}
	}

	public function get_payment_gateways() {
		return $this->payment_gateways;
	}

}