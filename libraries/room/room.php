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
 * Room class
 * @package 	Solidres
 * @subpackage	Room
 */
class SR_Room{

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function load( $id ) {
		$item = $this->wpdb->get_row( "SELECT * FROM {$this->wpdb->prefix}sr_rooms WHERE id = $id" );

		return $item;
	}

	/**
	 * Get a list of room by room type id
	 *
	 * @param $room_type_id
	 *
	 * @return mixed
	 */
	public function load_by_room_type_id( $room_type_id = 0 ) {
		return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT id, label FROM {$this->wpdb->prefix}sr_rooms WHERE room_type_id = %d", $room_type_id ) );
	}
}