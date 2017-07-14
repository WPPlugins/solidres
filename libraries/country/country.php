<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Country handler class
 * @package 	Solidres
 * @subpackage	Country
 */
class SR_Country {
	/**
	 * The database object
	 * @var object
	 */
	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Update states for listview
	 *
	 * @param $action
	 * @param $country_id
	 * @param $ids
	 */
	public function update_states( $action, $country_id, $ids ){
		$states = array(
			'draft' => array( 'state' => 0, 'action' => 'moved', 'title' => 'Draft' ),
			'publish' => array( 'state' => 1, 'action' => 'moved', 'title' => 'Publish' ),
			'trash' => array( 'state' => -2, 'action' => 'moved', 'title' => 'Trash' ),
			'untrash' => array( 'state' => 0, 'action' => 'restored', 'title' => 'Trash' ),
		);

		if ( isset( $action ) && array_key_exists ( $action, $states ) &&  isset( $country_id ) && $country_id != null ) {
			foreach ( $ids as $id ) {
				$this->wpdb->update( $this->wpdb->prefix . 'sr_countries', array( 'state' => $states[$action]['state'] ), array( 'id' => $id ) );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 country ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			}
			else {
				$message = __( count( $ids ).' countries ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			}
		}
	}

	/**
	 * Delete permanently action
	 *
	 * @param $id
	 * @return bool
	 */
	public function delete( $id ){
		$reservation_count = $this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_reservation_assets WHERE country_id = $id" );
		$state_count = $this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_geo_states WHERE country_id = $id" );
		if ( $reservation_count > 0 || $state_count > 0 ) {
			return false;
		}
		else {
			$this->wpdb->delete( $this->wpdb->prefix.'sr_countries', array( 'id' => $id ) );
		}
	}

	/**
	 * Get a single country by id
	 *
	 * @param $id
	 * @param $output
	 *
	 * @return mixed
	 */
	public function load( $id, $output = OBJECT ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_countries WHERE id = %d", $id ), $output );
	}
}