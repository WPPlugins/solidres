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
 * Coupon handler class
 * @package 	Solidres
 * @subpackage	Coupon
 * @since 		0.1.0
 */
class SR_Coupon {
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
	 * @param $coupon_id
	 * @param $ids
	 */
	public function update_states( $action, $coupon_id, $ids ){
		$states = array(
			'draft' => array( 'state' => 0, 'action' => 'moved', 'title' => 'Draft' ),
			'publish' => array( 'state' => 1, 'action' => 'moved', 'title' => 'Publish' ),
			'trash' => array( 'state' => -2, 'action' => 'moved', 'title' => 'Trash' ),
			'untrash' => array( 'state' => 0, 'action' => 'restored', 'title' => 'Trash' ),
		);

		if ( isset( $action ) && array_key_exists ( $action, $states ) &&  isset( $coupon_id ) && $coupon_id != null ) {
			foreach ( $ids as $id ) {
				$this->wpdb->update( $this->wpdb->prefix . 'sr_coupons', array( 'state' => $states[$action]['state'] ), array( 'id' => $id ) );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 coupon ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			}
			else {
				$message = __( count( $ids ).' coupons ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
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
		$this->wpdb->update( $this->wpdb->prefix.'sr_reservations', array( 'coupon_id' => NULL ), array( 'coupon_id' => $id ) );
		$this->wpdb->delete( $this->wpdb->prefix.'sr_room_type_coupon_xref', array( 'coupon_id' => $id ) );
		$this->wpdb->delete( $this->wpdb->prefix.'sr_coupons', array( 'id' => $id ) );
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function load( $id ) {
		$item = $this->wpdb->get_row( "SELECT * FROM {$this->wpdb->prefix}sr_coupons WHERE id = $id" );
		if ( $item ) {
			$tzoffset = get_option( 'timezone_string' );
			$tzoffset = $tzoffset == '' ? 'UTC' : $tzoffset;
			$timezone = new DateTimeZone( $tzoffset );
			$valid_from = new DateTime( $item->valid_from, $timezone );
			$item->valid_from = $valid_from->getTimestamp();
			$valid_to = new DateTime( $item->valid_to, $timezone );
			$item->valid_to = $valid_to->getTimestamp();
			$valid_from_checkin = new DateTime( $item->valid_from_checkin, $timezone );
			$item->valid_from_checkin = $valid_from_checkin->getTimestamp();
			$valid_to_checkin = new DateTime( $item->valid_to_checkin, $timezone );
			$item->valid_to_checkin = $valid_to_checkin->getTimestamp();
		}
		return $item;
	}

	public function load_by_code( $code ) {
		$item = $this->wpdb->get_row( $this->wpdb->prepare("SELECT * FROM {$this->wpdb->prefix}sr_coupons WHERE coupon_code = %s", $code)  );
		if ( $item ) {
			$tzoffset = get_option( 'timezone_string' );
			$tzoffset = $tzoffset == '' ? 'UTC' : $tzoffset;
			$timezone = new DateTimeZone( $tzoffset );
			$valid_from = new DateTime( $item->valid_from, $timezone );
			$item->valid_from = $valid_from->getTimestamp();
			$valid_to = new DateTime( $item->valid_to, $timezone );
			$item->valid_to = $valid_to->getTimestamp();
			$valid_from_checkin = new DateTime( $item->valid_from_checkin, $timezone );
			$item->valid_from_checkin = $valid_from_checkin->getTimestamp();
			$valid_to_checkin = new DateTime( $item->valid_to_checkin, $timezone );
			$item->valid_to_checkin = $valid_to_checkin->getTimestamp();
		}
		return $item;
	}

	/**
	 * Check a coupon code to see if it is valid to use.
	 *
	 * @param   string    $coupon_code       The coupon code to check
	 * @param   int $asset_id          The reservation asset id
	 * @param   int $checking_date     The date of checking
	 * @param   int $checkin           The checkin date
	 * @param   int $customer_group_id The customer group id
	 *
	 * @since   0.1.0
	 * @return  boolean
	 */
	public function is_valid( $coupon_code, $asset_id, $checking_date, $checkin, $customer_group_id = NULL ){
		$solidres_asset = new SR_Asset();
		$coupon = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->wpdb->prefix}sr_coupons WHERE coupon_code = %s",
				$coupon_code
			)
		);

		$coupon->valid_from = strtotime($coupon->valid_from);
		$coupon->valid_to = strtotime($coupon->valid_to);
		$coupon->valid_from_checkin = strtotime($coupon->valid_from_checkin);
		$coupon->valid_to_checkin = strtotime($coupon->valid_to_checkin);

		$asset = $solidres_asset->load( $asset_id );

		$registry = $asset->params;
		$asset_params = json_decode( $registry, true );

		if ( ! isset( $asset_params['enable_coupon'] ) ) {
			$asset_params['enable_coupon'] = 0;
		}

		$response = true;

		if (
			empty($coupon->id)
			|| $coupon->state != 1
			|| !( $coupon->valid_from <= $checking_date && $checking_date <= $coupon->valid_to)
			|| $coupon->reservation_asset_id != $asset_id
			|| !($coupon->valid_from_checkin <= $checkin && $checkin <= $coupon->valid_to_checkin)
			|| $coupon->customer_group_id != $customer_group_id
			|| ( !is_null($coupon->quantity)  && $coupon->quantity == 0)
			|| ($asset_params['enable_coupon'] == 0)
		) {
			$response = false;
		}

		return $response;
	}

	/**
	 * Check to see if the given coupon is applicable to the given room type
	 *
	 * @param   $couponId
	 * @param   $roomTypeId
	 * @since   0.1.0
	 * @return  bool
	 */
	public function is_applicable( $couponId, $roomTypeId ) {
		$count = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_room_type_coupon_xref
					WHERE room_type_id = %d AND coupon_id = %d",
				$roomTypeId,
				$couponId
			)
		);

		if ($count > 0) {
			$response = true;
		}

		return $response;
	}

	public function get_customer_group_id( $user_id )
	{
		$customer_group_id = NULL;

		if ( defined( 'SR_PLUGIN_USER_ENABLED' ) && SR_PLUGIN_USER_ENABLED && $user_id > 0) {
			$customer_group_id = get_the_author_meta( 'customer_group_id', $user_id );
		}

		return $customer_group_id;
	}
}