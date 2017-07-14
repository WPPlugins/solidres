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
 * @since 		0.1.0
 */
class SR_Custom_Field {
	/**
	 * Config scope id, 0 is Global
	 * @var int
	 */
	private $id = 0;

	/**
	 * Config data
	 * @var array
	 */
	private $data = null;
	private $type = null;

	/**
	 * Data name space
	 * @var string
	 */
	private $groupNamespace = '';

	public function __construct( $custom_field = array() )
	{
		if ( array_key_exists( 'id', $custom_field ) ) {
			$this->id = $custom_field['id'];
		}

		if ( array_key_exists( 'group_namespace', $custom_field ) ) {
			$this->groupNamespace = $custom_field['group_namespace'];
		}

		if ( array_key_exists( 'type', $custom_field ) ) {
			$this->type = $custom_field['type'];
		}

		if ( isset( $this->id ) ) {
			$this->data = $this->load_from_db();
		}
	}

	public function get_data() {
		return $this->data;
	}

	/**
	 * Retrive data by key name
	 * @param string $fieldKey
	 * @param int $defaultValue The default value to be returned when no results are found
	 * @return mixed
	 */
	public function get( $fieldKey, $defaultValue = null )
	{
		$target = null;
		if ( isset( $this->data ) ) {
			foreach ( $this->data as $fieldItem ) {
				if ( $fieldKey == $fieldItem[0] ) {
					$target = $fieldItem[1];
				}
			}
		}
		if ( is_null( $target ) ) {
			$target = $defaultValue;
		}
		return $target;
	}

	/**
	 * Split string from field_key
	 *
	 * @param $field_key
	 * @return mixed
	 */
	public function split_get_group_name ( $field_key ) {
		$name_group = explode( '.', $field_key );
		return $name_group[1];
	}

	/**
	 * Split string from field_name
	 *
	 * @param $field_name
	 * @return mixed
	 */
	public function split_field_name ( $field_name ) {
		$field_name = explode( '.', $field_name );
		return $field_name[2];
	}

	/**
	 * Get array group data name
	 *
	 * @return array
	 */
	public function get_group_name(){
		$data = $this->load_from_db();
		$group_name = array();
		foreach ( $data as $keys => $values ) {
			foreach ( $values as $key => $value ){
				$name_group = $this->split_get_group_name( $values[0] );
				if ( ! in_array( $name_group, $group_name ) ){
					array_push( $group_name, $name_group );
				}
			}
		}
		return $group_name;
	}

	/**
	 * Create array group with array and all element
	 *
	 * @return array
	 */
	public function create_array_group(){
		$group_name = $this->get_group_name();
		$data = $this->load_from_db();
		$complete_array = array();
		foreach ( $group_name as $key_group => $value_group ){
			$array_key_value_group = array();
			foreach ( $data as $keys => $values ){
				if ( $this->split_get_group_name( $values[0] ) === $value_group ) {
					array_push( $array_key_value_group, array( $values[0], $values[1] ) );
				}
			}
			$complete_array[$value_group] = $array_key_value_group;
		}
		return $complete_array;
	}


	/**
	 * Write data into database
	 * @param  array $data
	 * @return bool
	 */
	public function set( $data ) {
		global $wpdb;
		$table = $wpdb->prefix.'sr_reservation_asset_fields';
		$type_id = 'reservation_asset_id';
		if ( $this->type == 'room_type' ) {
			$table = $wpdb->prefix.'sr_room_type_fields';
			$type_id = 'room_type_id';
		}
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE $type_id = %d AND field_key LIKE %s", $this->id, $this->groupNamespace.'%' ) );
		foreach ( $data as $k => $v ) {
			$wpdb->insert( $table, array( $type_id => $this->id, 'field_key' => $this->groupNamespace . '.' . $k, 'field_value' => $v ) );
		}
	}

	/**
	 * Load config data from database
	 * @return mixed
	 */
	public function load_from_db() {
		global $wpdb;
		$table = $wpdb->prefix.'sr_reservation_asset_fields';
		$type_id = 'reservation_asset_id';
		if ( $this->type == 'room_type' ) {
			$table = $wpdb->prefix.'sr_room_type_fields';
			$type_id = 'room_type_id';
		}
		$data_array = $wpdb->get_results( $wpdb->prepare( "SELECT field_key, field_value FROM $table WHERE $type_id = %d", $this->id ), ARRAY_N );
		return $data_array;
	}
}