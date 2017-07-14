<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Joomla
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
 * Extra handler class
 *
 * @package 	Solidres
 * @subpackage	Extra
 *
 * @since 		0.2.0
 */
abstract class SR_Extra_Base
{
	public static $charge_types = array(
		0 => 'Per room',
		1 => 'Per booking',
		2 => 'Per booking per stay (night or day)',
		3 => 'Per booking per person',
		4 => 'Per room per stay',
		5 => 'Per room per person',
		6 => 'Per room per person per stay'
	);

	public $name;

	public $state;

	public $price = 0;

	public $price_tax_incl = 0;

	public $price_tax_excl = 0;

	public $price_adult = 0;

	public $price_adult_tax_incl = 0;

	public $price_adult_tax_excl = 0;

	public $price_child = 0;

	public $price_child_tax_incl = 0;

	public $price_child_tax_excl = 0;

	public $charge_type;

	public $tax_id;

	public $quantity;

	public $adults_number;

	public $children_number;

	public $stay_length; // could be day or night

	public function __construct($extra_details = array()) {
		foreach ($extra_details as $key => $val) {
			$this->{$key} = $val;
		}

		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function calculate_extra_cost() {
	}

	/**
	 * Update states for listview
	 *
	 * @param $action
	 * @param $extra_id
	 * @param $ids
	 */
	public function update_states( $action, $extra_id, $ids ){
		$states = array(
			'draft' => array( 'state' => 0, 'action' => 'moved', 'title' => 'Draft' ),
			'publish' => array( 'state' => 1, 'action' => 'moved', 'title' => 'Publish' ),
			'trash' => array( 'state' => -2, 'action' => 'moved', 'title' => 'Trash' ),
			'untrash' => array( 'state' => 0, 'action' => 'restored', 'title' => 'Trash' ),
		);

		if ( isset( $action ) && array_key_exists ( $action, $states ) &&  isset( $extra_id ) && $extra_id != null ) {
			foreach ( $ids as $id ) {
				$this->wpdb->update( $this->wpdb->prefix . 'sr_extras', array( 'state' => $states[$action]['state'] ), array( 'id' => $id ) );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 extra ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			}
			else {
				$message = __( count( $ids ).' extras ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
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
	public function delete( $id ) {
		$this->wpdb->update( $this->wpdb->prefix.'sr_reservation_room_extra_xref', array( 'extra_id' => NULL ), array( 'extra_id' => $id ) );
		$this->wpdb->delete( $this->wpdb->prefix.'sr_room_type_extra_xref', array( 'extra_id' => $id ) );
		$this->wpdb->update( $this->wpdb->prefix.'sr_reservation_extra_xref', array( 'extra_id' => NULL ), array( 'extra_id' => $id ) );
		$this->wpdb->delete( $this->wpdb->prefix.'sr_extras', array( 'id' => $id ) );
	}

	/**
	 * Get a single extra by id
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function load( $id ) {
		$item = $this->wpdb->get_row( "SELECT * FROM {$this->wpdb->prefix}sr_extras WHERE id = $id" );
		if ( isset ( $item->id ) ) {
			$assetTable = new SR_Asset();
			$solidres_tax = new SR_Tax();
			$options = get_option( 'solidres_plugin' );
			$showTaxIncl = isset( $options['show_price_with_tax'] ) ? $options['show_price_with_tax'] : 0;

			$assettable = $assetTable->load( $item->reservation_asset_id );
			$solidresCurrency = new SR_Currency( 0, $assettable->currency_id );

			$tax = $solidres_tax->load( $item->tax_id );
			$taxAmount = 0;
			$taxAdultAmount = 0;
			$taxChildAmount = 0;
			if ( ! empty( $tax->rate ) ) {
				$taxAmount = $item->price * $tax->rate;
				$taxAdultAmount = $item->price_adult * $tax->rate;
				$taxChildAmount = $item->price_child * $tax->rate;
			}
			$item->currencyTaxIncl = clone $solidresCurrency;
			$item->currencyTaxExcl = clone $solidresCurrency;
			$item->currencyTaxIncl->set_value( $item->price + $taxAmount );
			$item->currencyTaxExcl->set_value( $item->price );
			$item->price_tax_incl = $item->price + $taxAmount;
			$item->price_tax_excl = $item->price;

			// For adult
			$item->currencyAdultTaxIncl = clone $solidresCurrency;
			$item->currencyAdultTaxExcl = clone $solidresCurrency;
			$item->currencyAdultTaxIncl->set_value($item->price_adult + $taxAdultAmount);
			$item->currencyAdultTaxExcl->set_value($item->price_adult);
			$item->price_adult_tax_incl = $item->price_adult + $taxAdultAmount;
			$item->price_adult_tax_excl = $item->price_adult;

			// For child
			$item->currencyChildTaxIncl = clone $solidresCurrency;
			$item->currencyChildTaxExcl = clone $solidresCurrency;
			$item->currencyChildTaxIncl->set_value($item->price_child + $taxChildAmount);
			$item->currencyChildTaxExcl->set_value($item->price_child);
			$item->price_child_tax_incl = $item->price_child + $taxChildAmount;
			$item->price_child_tax_excl = $item->price_child;

			if ( $showTaxIncl ) {
				$item->currency = $item->currencyTaxIncl;
				$item->currencyAdult = $item->currencyAdultTaxIncl;
				$item->currencyChild = $item->currencyChildTaxIncl;
			}
			else {
				$item->currency = $item->currencyTaxExcl;
				$item->currencyAdult = $item->currencyAdultTaxExcl;
				$item->currencyChild = $item->currencyChildTaxExcl;
			}
		}
		return $item;
	}

	public function load_by_room_type_id( $room_type_id, $state, $show_price_with_tax ) {
		$extras = $this->wpdb->get_results( "
			SELECT * FROM {$this->wpdb->prefix}sr_extras as a
 			INNER JOIN {$this->wpdb->prefix}sr_room_type_extra_xref as b
 			ON a.id = b.extra_id AND b.room_type_id = $room_type_id
 			WHERE a.state = '$state'
 			" );

		if ( !empty( $extras ) ) {
			$solidres_asset = new SR_Asset();
			$asset = $solidres_asset->load( $extras[0]->reservation_asset_id );
			$solidres_tax = new SR_Tax();
			$tax = $solidres_tax->load( $extras[0]->tax_id );
			$solidresCurrency = new SR_Currency( 0, $asset->currency_id );

			foreach ( $extras as $extra ) {
				if ( $asset->id != $extra->reservation_asset_id ) {
					$asset = $solidres_asset->load( $extra->reservation_asset_id );
				}

				if ( isset( $tax->id ) && $tax->id != $extra->tax_id ) {
					$tax = $solidres_tax->load( $extra->tax_id );
				}

				$taxAmount = 0;
				$taxAdultAmount = 0;
				$taxChildAmount = 0;
				if ( ! empty( $tax->rate ) ) {
					$taxAmount = $extra->price * $tax->rate;
					$taxAdultAmount = $extra->price_adult * $solidres_tax->rate;
					$taxChildAmount = $extra->price_child * $solidres_tax->rate;
				}

				$extra->currencyTaxIncl = clone $solidresCurrency;
				$extra->currencyTaxExcl = clone $solidresCurrency;
				$extra->currencyTaxIncl->set_value( $extra->price + $taxAmount );
				$extra->currencyTaxExcl->set_value( $extra->price );
				$extra->price_tax_incl = $extra->price + $taxAmount;
				$extra->price_tax_excl = $extra->price;

				// For adult
				$extra->currencyAdultTaxIncl = clone $solidresCurrency;
				$extra->currencyAdultTaxExcl = clone $solidresCurrency;
				$extra->currencyAdultTaxIncl->set_value($extra->price_adult + $taxAdultAmount);
				$extra->currencyAdultTaxExcl->set_value($extra->price_adult);
				$extra->price_adult_tax_incl = $extra->price_adult + $taxAdultAmount;
				$extra->price_adult_tax_excl = $extra->price_adult;

				// For child
				$extra->currencyChildTaxIncl = clone $solidresCurrency;
				$extra->currencyChildTaxExcl = clone $solidresCurrency;
				$extra->currencyChildTaxIncl->set_value($extra->price_child + $taxChildAmount);
				$extra->currencyChildTaxExcl->set_value($extra->price_child);
				$extra->price_child_tax_incl = $extra->price_child + $taxChildAmount;
				$extra->price_child_tax_excl = $extra->price_child;
				
				if ( $show_price_with_tax ) {
					$extra->currency = $extra->currencyTaxIncl;
					$extra->currencyAdult = $extra->currencyAdultTaxIncl;
					$extra->currencyChild = $extra->currencyChildTaxIncl;
				} else {
					$extra->currency = $extra->currencyTaxExcl;
					$extra->currencyAdult = $extra->currencyAdultTaxExcl;
					$extra->currencyChild = $extra->currencyChildTaxExcl;
				}
			}
		}

		return $extras;
	}

	/**
	 * Get a list of room type by asset's id
	 *
	 * @param $asset_id
	 *
	 * @return mixed
	 */
	public function load_by_asset_id ( $asset_id ) {
		return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_extras WHERE reservation_asset_id = %d AND state = 1", $asset_id ) );
	}

	public function load_by_reservation_asset_id( $asset_id, $state, $show_price_with_tax, $charge_types = array() ) {
		$extras = $this->wpdb->get_results( $this->wpdb->prepare(
			"SELECT a.* FROM {$this->wpdb->prefix}sr_extras as a
 			WHERE a.state = %d AND a.reservation_asset_id = %d AND a.charge_type IN (" . implode(',', $charge_types) .")", $state, $asset_id
		) );

		if ( !empty( $extras ) ) {
			$solidres_asset = new SR_Asset();
			$asset = $solidres_asset->load( $extras[0]->reservation_asset_id );
			$solidres_tax = new SR_Tax();
			$tax = $solidres_tax->load( $extras[0]->tax_id );
			$solidresCurrency = new SR_Currency( 0, $asset->currency_id );

			foreach ( $extras as $extra ) {
				if ( $asset->id != $extra->reservation_asset_id ) {
					$asset = $solidres_asset->load( $extra->reservation_asset_id );
				}

				if ( isset( $tax->id ) && $tax->id != $extra->tax_id ) {
					$tax = $solidres_tax->load( $extra->tax_id );
				}

				$taxAmount = 0;
				$taxAdultAmount = 0;
				$taxChildAmount = 0;
				if ( ! empty( $tax->rate ) ) {
					$taxAmount = $extra->price * $tax->rate;
					$taxAdultAmount = $extra->price_adult * $tax->rate;
					$taxChildAmount = $extra->price_child * $tax->rate;
				}

				$extra->currencyTaxIncl = clone $solidresCurrency;
				$extra->currencyTaxExcl = clone $solidresCurrency;
				$extra->currencyTaxIncl->set_value( $extra->price + $taxAmount );
				$extra->currencyTaxExcl->set_value( $extra->price );
				$extra->price_tax_incl = $extra->price + $taxAmount;
				$extra->price_tax_excl = $extra->price;

				// For adult
				$extra->currencyAdultTaxIncl = clone $solidresCurrency;
				$extra->currencyAdultTaxExcl = clone $solidresCurrency;
				$extra->currencyAdultTaxIncl->set_value($extra->price_adult + $taxAdultAmount);
				$extra->currencyAdultTaxExcl->set_value($extra->price_adult);
				$extra->price_adult_tax_incl = $extra->price_adult + $taxAdultAmount;
				$extra->price_adult_tax_excl = $extra->price_adult;

				// For child
				$extra->currencyChildTaxIncl = clone $solidresCurrency;
				$extra->currencyChildTaxExcl = clone $solidresCurrency;
				$extra->currencyChildTaxIncl->set_value($extra->price_child + $taxChildAmount);
				$extra->currencyChildTaxExcl->set_value($extra->price_child);
				$extra->price_child_tax_incl = $extra->price_child + $taxChildAmount;
				$extra->price_child_tax_excl = $extra->price_child;

				if ( $show_price_with_tax ) {
					$extra->currency = $extra->currencyTaxIncl;
					$extra->currencyAdult = $extra->currencyAdultTaxIncl;
					$extra->currencyChild = $extra->currencyChildTaxIncl;
				} else {
					$extra->currency = $extra->currencyTaxExcl;
					$extra->currencyAdult = $extra->currencyAdultTaxExcl;
					$extra->currencyChild = $extra->currencyChildTaxExcl;
				}
			}
		}

		return $extras;
	}
}