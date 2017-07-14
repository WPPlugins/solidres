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
 * SR_Media handler class
 * @package 	Solidres
 * @subpackage	SR_Media
 */
class SR_Media {
	/**
	 * The database object
	 * @var object
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function getItems( $id, $type, $created_by = 0 ){
		$createby = ( ( ! is_admin() ) && $created_by > 0  ) ? "AND a.created_by = $created_by " : '';
		switch ( $type ){
			case 'asset':
				$query = "SELECT a.*, x.weight AS weight
					FROM {$this->wpdb->prefix}sr_media AS a
					LEFT JOIN {$this->wpdb->prefix}sr_media_reservation_assets_xref AS x
					ON a.id = x.media_id
					WHERE x.reservation_asset_id = $id
					$createby
					ORDER BY x.weight ASC";
				break;
			case 'roomtype':
				$query = "SELECT a.*, x.weight AS weight
					FROM {$this->wpdb->prefix}sr_media AS a
					LEFT JOIN {$this->wpdb->prefix}sr_media_roomtype_xref AS x
					ON a.id = x.media_id
					WHERE x.room_type_id = $id
					$createby
					ORDER BY x.weight ASC";
				break;
		}
		return $this->wpdb->get_results( $query );
	}

	/**
	 * Load media by asset's id
	 *
	 * @param $id
	 *
	 * @return mixed
	 *
	 * @since 0.1.0
	 */
	public function load_by_asset_id( $id ){
		return $this->wpdb->get_results( '
			SELECT t1.*, t2.guid as img_url
			FROM '.$this->wpdb->prefix.'sr_media_reservation_assets_xref t1
			LEFT JOIN '.$this->wpdb->posts.' t2 ON t1.media_id = t2.id
			WHERE t1.reservation_asset_id = '.$id.'
			ORDER BY t1.weight ASC' );
	}

	/**
	 * Load media by room type's id
	 *
	 * @param $id
	 *
	 * @return mixed
	 *
	 * @since 0.1.0
	 */
	public function load_by_room_type_id( $id ){
		return $this->wpdb->get_results( '
			SELECT t1.*, t2.guid as img_url
			FROM '.$this->wpdb->prefix.'sr_media_roomtype_xref t1
			LEFT JOIN '.$this->wpdb->posts.' t2 ON t1.media_id = t2.id
			WHERE t1.room_type_id = '.$id.'
			ORDER BY t1.weight ASC' );
	}
}