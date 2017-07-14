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
 * State handler class
 * @package 	Solidres
 * @subpackage	State
 */
class SR_State {
	/**
	 * The database object
	 * @var object
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Update states for listview
	 *
	 * @param $action
	 * @param $state_id
	 * @param $ids
	 */
	public function update_states( $action, $state_id, $ids ){
		$states = array(
			'draft' => array( 'state' => 0, 'action' => 'moved', 'title' => 'Draft' ),
			'publish' => array( 'state' => 1, 'action' => 'moved', 'title' => 'Publish' ),
			'trash' => array( 'state' => -2, 'action' => 'moved', 'title' => 'Trash' ),
			'untrash' => array( 'state' => 0, 'action' => 'restored', 'title' => 'Trash' ),
		);

		if ( isset( $action ) && array_key_exists ( $action, $states ) &&  isset( $state_id ) && $state_id != null ) {
			foreach ( $ids as $id ) {
				$this->wpdb->update( $this->wpdb->prefix . 'sr_geo_states', array( 'state' => $states[$action]['state'] ), array( 'id' => $id ) );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 state ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			}
			else {
				$message = __( count( $ids ).' states ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
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
		$count_asset = $this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_reservation_assets WHERE geo_state_id = $id" );
		$user_query = new WP_User_Query( array( 'meta_key' => 'geo_state_id', 'meta_value' => $id ) );
		$count_customer = $user_query->get_total();
		$count_tax = $this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_taxes WHERE geo_state_id = $id" );
		if ( $count_asset > 0 || $count_customer > 0 || $count_tax > 0 ) {
			return false;
		} else {
			$this->wpdb->delete( $this->wpdb->prefix.'sr_geo_states', array( 'id' => $id ) );
		}
	}

	/**
	 * Get a single state by id
	 *
	 * @param $id
	 * @param $output
	 *
	 * @return mixed
	 */
	public function load( $id, $output = OBJECT ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_geo_states WHERE id = %d", $id ), $output );
	}
}