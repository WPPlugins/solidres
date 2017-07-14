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

/**
 * Config handler class
 * @package 	Solidres
 * @subpackage	Config
 * @since 		0.3.0
 */
class SR_Config
{
	/**
	 * Config scope id, 0 is Global
	 * @var int
	 */
	private $scopeId = 0;

	/**
	 * Config data
	 * @var array
	 */
	private $data = null;

	/**
	 * Data name space
	 * @var string
	 */
	private $dataNamespace = '';

	public function __construct( $config = array() )
	{
		if ( array_key_exists( 'scope_id', $config ) ) {
			$this->scopeId = $config['scope_id'];
		}

		if ( array_key_exists( 'data_namespace', $config ) ) {
			$this->dataNamespace = $config['data_namespace'];
		}

		if ( isset( $this->scopeId ) ) {
			$this->data = $this->load_from_db();
		}
	}

	public function get_data() {
		return $this->data;
	}

	/**
	 * Retrive data by key name
	 * @param string $dataKey
	 * @param int $defaultValue The default value to be returned when no results are found
	 * @return mixed
	 */
	public function get( $dataKey, $defaultValue = null )
	{
		$target = null;
		if ( isset( $this->data ) ) {
			foreach ( $this->data as $dataItem ) {
				if ( $dataKey == $dataItem[0] ) {
					$target = $dataItem[1];
				}
			}
		}

		if ( is_null( $target ) ) {
			$target = $defaultValue;
		}

		return $target;
	}

	/**
	 * Write data into database
	 * @param  array $data
	 * @return bool
	 */
	public function set( $data ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}sr_config_data WHERE scope_id = %d AND data_key LIKE %s", $this->scopeId, $this->dataNamespace.'%' ) );
		foreach ( $data as $k => $v ) {
			$wpdb->insert( $wpdb->prefix.'sr_config_data', array( 'scope_id' => $this->scopeId, 'data_key' => $this->dataNamespace . '/' . $k, 'data_value' => $v ) );
		}
	}

	/**
	 * Load config data from database
	 * @return mixed
	 */
	public function load_from_db() {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( "SELECT data_key, data_value FROM {$wpdb->prefix}sr_config_data WHERE scope_id = %d", $this->scopeId ), ARRAY_N );
	}
}