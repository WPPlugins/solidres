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
 * RoomType handler class
 * @package 	Solidres
 * @subpackage	RoomType
 */
class SR_Room_Type {
	const PER_ROOM_PER_NIGHT = 0;
	const PER_PERSON_PER_NIGHT = 1;
	const PACKAGE_PER_ROOM = 2;
	const PACKAGE_PER_PERSON = 3;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Update states for listview
	 *
	 * @param $action
	 * @param $room_type_id
	 * @param $ids
	 */
	public function update_states( $action, $room_type_id, $ids ){
		$states = array(
			'draft' => array( 'state' => 0, 'action' => 'moved', 'title' => 'Draft' ),
			'publish' => array( 'state' => 1, 'action' => 'moved', 'title' => 'Publish' ),
			'trash' => array( 'state' => -2, 'action' => 'moved', 'title' => 'Trash' ),
			'untrash' => array( 'state' => 0, 'action' => 'restored', 'title' => 'Trash' ),
		);

		if ( isset( $action ) && array_key_exists ( $action, $states ) &&  isset( $room_type_id ) && $room_type_id != null ) {
			foreach ( $ids as $id ) {
				$this->wpdb->update( $this->wpdb->prefix . 'sr_room_types', array( 'state' => $states[$action]['state'] ), array( 'id' => $id ) );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 room types ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			}
			else {
				$message = __( count( $ids ).' rooms types ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
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

		// Delete all rooms belong to it
		$rooms = $this->wpdb->get_results( "SELECT id FROM {$this->wpdb->prefix}sr_rooms WHERE room_type_id = $id" );
		foreach ( $rooms as $room ) {
			$this->wpdb->update( $this->wpdb->prefix . 'sr_reservation_room_xref', array( 'room_id' => null ), array( 'room_id' => $room->id ), array( '%d'), array( '%d') );
			$this->wpdb->update( $this->wpdb->prefix . 'sr_reservation_room_extra_xref', array( 'room_id' => null ), array( 'room_id' => $room->id ), array( '%d'), array( '%d') );
			if ( defined( 'SR_PLUGIN_LIMITBOOKING_ENABLED' ) && SR_PLUGIN_LIMITBOOKING_ENABLED ) {
				$this->wpdb->delete( $this->wpdb->prefix . 'sr_limit_booking_details', array( 'room_id' => $room->id ), array( '%d' ) );
			}
			$this->wpdb->delete( $this->wpdb->prefix . 'sr_rooms', array( 'id' => $room->id ) );
		}


		// Delete all coupons relation
		$this->wpdb->delete( $this->wpdb->prefix . 'sr_room_type_coupon_xref', array( 'room_type_id' => $id ) );

		// Delete all extras relation
		$this->wpdb->delete( $this->wpdb->prefix . 'sr_room_type_extra_xref', array( 'room_type_id' => $id ) );

		// Delete all custom fields
		$this->wpdb->delete( $this->wpdb->prefix . 'sr_room_type_fields', array( 'room_type_id' => $id ) );

		// Delete all media
		$this->wpdb->delete( $this->wpdb->prefix . 'sr_media_roomtype_xref', array( 'room_type_id' => $id ) );

		// Delete all tariffs
		$tariffs = $this->wpdb->get_results( "SELECT id FROM {$this->wpdb->prefix}sr_tariffs WHERE room_type_id = $id" );
		$solidres_tariff = new SR_Tariff();
		foreach ($tariffs as $tariff ) {
			$solidres_tariff->delete( $tariff->id );
		}

		// Delete related facilities
		if ( defined('SR_PLUGIN_HUB_ENABLED') && SR_PLUGIN_HUB_ENABLED ) {
			$this->wpdb->delete( $this->wpdb->prefix . 'sr_facility_room_type_xref', array( 'room_type_id' => $id ) );
		}

		$this->wpdb->delete( $this->wpdb->prefix.'sr_room_types', array( 'id' => $id ) );
	}

	/**
	 * Get list of Room is reserved and belong to a RoomType.
	 * @param int $roomTypeId
	 * @param int $reservationId
	 * @return array An array of room object
	 */
	public function getListReservedRoom( $roomTypeId, $reservationId ) {
		$results = $this->wpdb->get_results( "SELECT r1.id, r1.label, r2.adults_number, r2.children_number FROM {$this->wpdb->prefix}sr_rooms as r1 INNER JOIN {$this->wpdb->prefix}sr_reservation_room_xref as r2 ON r1.id = r2.room_id WHERE r1.room_type_id = $roomTypeId AND r2.reservation_id = $reservationId" );
		return $results;
	}

	/**
	 * Get list rooms belong to a RoomType
	 * @param int $roomtypeId
	 * @return array object
	 */
	public function getListRooms( $roomtypeId ) {
		$result = $this->wpdb->get_results( "SELECT id, label, room_type_id FROM {$this->wpdb->prefix}sr_rooms WHERE room_type_id = $roomtypeId" );
		if(empty($result)) {
			return false;
		}
		return $result;
	}

	/**
	 * Method to get a list of available rooms of a RoomType based on check in and check out date
	 * @param   int     $roomtypeId
	 * @param   int     $checkin
	 * @param   int     $checkout
	 * @param   int     $bookingType 0 is booking per night and 1 is booking per day
	 *
	 * @return  mixed   An array of room object if successfully
	 *                  otherwise return false
	 */
	public function getListAvailableRoom( $roomtypeId = 0, $checkin, $checkout, $bookingType = 0 ) {
		$srReservation = new SR_Reservation();
		$availableRooms = array();
		$query_default = "SELECT id, label FROM {$this->wpdb->prefix}sr_rooms";
		$query_filter = array();

		if ( $roomtypeId > 0 ) {
			$query_filter[] = ' room_type_id = '.$roomtypeId;
		}
		if ( defined('SR_PLUGIN_LIMITBOOKING_ENABLED') && SR_PLUGIN_LIMITBOOKING_ENABLED ) {

			$checkinMySQLFormat = "STR_TO_DATE('" . $checkin . "', '%Y-%m-%d')";
			$checkoutMySQLFormat = "STR_TO_DATE('" . $checkout . "', '%Y-%m-%d')";

			if (0 == $bookingType) {
				$query_filter[] = ' id NOT IN (SELECT room_id FROM '.$this->wpdb->prefix.'sr_limit_booking_details
											WHERE limit_booking_id IN ( SELECT id FROM '.$this->wpdb->prefix.'sr_limit_bookings
											WHERE
											(
												('.$checkinMySQLFormat.' <= start_date AND '.$checkoutMySQLFormat.' > start_date )
												OR
												('.$checkinMySQLFormat.' >= start_date AND '.$checkoutMySQLFormat.' <= end_date )
												OR
												('.$checkinMySQLFormat.' <= end_date AND '.$checkoutMySQLFormat.' >= end_date )
											)
											AND state = 1
											) )';
			} else {
				$query_filter[] = ' id NOT IN (SELECT room_id FROM '.$this->wpdb->prefix.'sr_limit_booking_details
											WHERE limit_booking_id IN ( SELECT id FROM '.$this->wpdb->prefix.'sr_limit_bookings
											WHERE
											(
												('.$checkinMySQLFormat.' <= start_date AND '.$checkoutMySQLFormat.' >= start_date )
												OR
												('.$checkinMySQLFormat.' >= start_date AND '.$checkoutMySQLFormat.' <= end_date )
												OR
												('.$checkinMySQLFormat.' <= end_date AND '.$checkoutMySQLFormat.' >= end_date )
											)
											AND state = 1
											) )';
			}

		}
		$query_default = $query_default . ' WHERE '.implode( ' AND', $query_filter );
		$rooms = $this->wpdb->get_results( $query_default );

		if ( empty( $rooms ) ) {
			return false;
		}

		foreach ( $rooms as $room ) {
			// If this room is available, add it to the returned list
			if ( $srReservation->is_room_available( $room->id, $checkin, $checkout, $bookingType ) ) {
				$availableRooms[] = $room;
			}
		}
		return $availableRooms;
	}

	/**
	 * Check a room to determine whether it can be deleted or not, if yes then delete it
	 * When delete a room, we will need to make sure that all related
	 * Reservation of that room must be removed first
	 * @param 	int 	    $roomId
	 * @return 	boolean     True if a room is safe to be deleted
	 *                      False otherwise
	 */
	public function canDeleteRoom( $roomId = 0 ) {
		$result = (int)$this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_reservation_room_xref WHERE room_id = $roomId" );
		if ( $result > 0 ) {
			return false;
		}
		$result = $this->wpdb->delete( $this->wpdb->prefix.'sr_rooms', array( 'id' => $roomId ) );
		if ( ! $result ) {
			return false;
		}
		return true;
	}

	/**
	 * @param  int $roomtypeId
	 * @param  int $couponId
	 * @return bool|mixed
	 */
	public function storeCoupon( $roomtypeId = 0, $couponId = 0 ) {
		if( $roomtypeId <= 0 && $couponId <= 0 ) {
			return false;
		}
		return $this->wpdb->insert( $this->wpdb->prefix.'sr_room_type_coupon_xref', array( 'room_type_id' => (int)$roomtypeId, 'coupon_id' => (int)$couponId ) );
	}


	/**
	 * @param  int $roomtypeId
	 * @param  int $extraId
	 * @return bool|mixed
	 */
	public function storeExtra($roomtypeId = 0, $extraId = 0) {
		global $wpdb;
		if( $roomtypeId <= 0 && $extraId <= 0 ) {
			return false;
		}
		return $wpdb->insert( $wpdb->prefix.'sr_room_type_extra_xref', array( 'room_type_id' => (int)$roomtypeId, 'extra_id' => (int)$extraId ) );
	}

	/**
	 * Method to store Room information
	 * TODO move this function to corresponding model/table
	 * @param   int     $roomTypeId
	 * @param   string  $roomLabel
	 * @return  boolean
	 */
	public function storeRoom( $roomTypeId = 0, $roomLabel = '' ){
		global $wpdb;
		return $wpdb->insert( $wpdb->prefix.'sr_rooms', array( 'room_type_id' => $roomTypeId, 'label' => $roomLabel ) );
	}

	/**
	 * Find room type by room id
	 * TODO move this function to corresponding model/table
	 * @param  int $roomId
	 * @return mixed
	 */
	public function findByRoomId( $roomId ){
		return $this->wpdb->get_results( "SELECT * FROM {$this->wpdb->prefix}sr_room_types WHERE id IN ( SELECT room_type_id FROM {$this->wpdb->prefix}sr_rooms WHERE id = $roomId )" );

	}

	/**
	 * Get list coupon id belong to $roomtypeId
	 * @param   int $roomtypeId
	 * @return  array
	 */
	public function getCoupon( $roomtypeId ) {
		return $this->wpdb->get_results( "SELECT coupon_id FROM {$this->wpdb->prefix}sr_room_type_coupon_xref WHERE room_type_id = $roomtypeId" );
	}

	/**
	 * Get list extra id belong to $roomtypeId
	 * @param   int $roomtypeId
	 * @return  array
	 */
	public function getExtra( $roomtypeId ) {
		return $this->wpdb->get_results( "SELECT extra_id FROM {$this->wpdb->prefix}sr_room_type_extra_xref WHERE room_type_id = $roomtypeId" );
	}

	/**
	 * Get price of a room type from a list of room type's tariff that matches the conditions:
	 *        Customer group
	 *        Checkin && Checkout date
	 *        Adult number
	 *        Child number & ages
	 *        Min & Max number of nights
	 *
*@param   int $room_type_id
	 * @param   $customer_group_id
	 * @param   $imposed_tax_types
	 * @param   bool $default_tariff
	 * @param   bool $date_constraint          @deprecated
	 * @param   string $checkin
	 * @param   string $checkout
	 * @param   SR_Currency $solidres_currency The currency object
	 * @param   array $coupon                  An array of coupon information
	 * @param   int $adult_number              Number of adult, default is 0
	 * @param   int $child_number              Number of child, default is 0
	 * @param   array $child_ages              An array of children age, it is associated with the $childNumber
	 * @param   int $stay_length               0 means ignore this condition
	 * @param   int $tariff_id                 Search for specific tariff id
	 *
	 * @return  array    An array of SR_Currency for Tax and Without Tax
	 */
	public function getPrice( $room_type_id, $customer_group_id, $imposed_tax_types, $default_tariff = false, $date_constraint = false, $checkin = '', $checkout = '', SR_Currency $solidres_currency, $coupon = NULL, $adult_number = 0, $child_number = 0, $child_ages = array(), $stay_length = 0, $tariff_id = NULL, $discounts = array(), $is_discount_pre_tax, $config = array() ) {

		$solidres_tariff   = new SR_Tariff();
		$tariff_with_details = null;

		// This is package type, do not need to calculate per day
		if ( isset ( $tariff_id ) ) {
			$tariff_with_details = $solidres_tariff->load( $tariff_id );

			if ( isset ( $tariff_with_details ) && ( $tariff_with_details->type == 2 || $tariff_with_details->type == 3 ) ) {
				$response = $this->getPricePackage( $tariff_with_details, $room_type_id, $checkin, $checkout, $imposed_tax_types, $solidres_currency, $coupon, $adult_number, $child_number, $child_ages, $stay_length, $discounts, $is_discount_pre_tax, $config );
			} else { // This is normal tariffs, need to calculate per day
				$response = $this->getPriceDaily( $tariff_with_details, $room_type_id, $checkin, $checkout, $imposed_tax_types, $solidres_currency, $coupon, $adult_number, $child_ages, $stay_length, $discounts, $is_discount_pre_tax, $config );
			}
		} else { // No tariff id specified, back to old behavior of 0.6.x and before
			$response = $this->getPriceLegacy($room_type_id, $customer_group_id, $imposed_tax_types, $default_tariff, $date_constraint, $checkin, $checkout, $solidres_currency, $coupon, $adult_number, $child_ages, $stay_length, $discounts, $is_discount_pre_tax, $config);
		}

		return $response;
	}

	/**
	 * Get price for Package tariff type: either Package per room or Package per person.
	 *
	 */
	public function getPricePackage( $tariffWithDetails, $roomTypeId, $checkin, $checkout, $imposedTaxTypes, $solidresCurrency, $coupon = NULL, $adultNumber, $childNumber, $childAges, $stayLength = 0, $discounts, $isDiscountPreTax, $config ) {
		$isAppliedCoupon = false;
		$tariffBreakDown = array();
		$totalBookingCost = 0;
		$totalBookingCostIncludedTaxedFormatted = NULL;
		$totalBookingCostExcludedTaxedFormatted = NULL;
		$totalBookingCostTaxed = NULL;
		$totalBookingCostTaxInclDiscounted = 0;
		$totalBookingCostTaxExclDiscounted = 0;
		$totalBookingCostIncludedTaxedDiscountedFormatted = NULL;
		$totalBookingCostExcludedTaxedDiscountedFormatted = NULL;
		$totalDiscount = 0;
		$totalDiscountFormatted = NULL;
		$appliedDiscounts = array();
		$totalSingleSupplement = 0;
		$totalSingleSupplementFormatted = NULL;

		$checkinDay = new DateTime( $checkin );
		$checkoutDay = new DateTime( $checkout );
		$checkinDayInfo = getdate( $checkinDay->format( 'U' ) );
		$checkoutDay = getdate( $checkoutDay->format( 'U' ) );
		$isValid = self::isValid($tariffWithDetails, $checkin, $checkout, $stayLength, $checkinDayInfo);

		if ( $isValid ) {
			$cost = 0;
			$costAdults = 0;
			$costChildren = 0;
			$singleSupplementAmount = 0;

			if ( $tariffWithDetails->type == self::PACKAGE_PER_ROOM ) {
				$cost = $tariffWithDetails->details['per_room'][0]->price;
				// Calculate single supplement
				if ($config['enable_single_supplement'] && $adultNumber == 1) {
					$singleSupplementAmount = $config['single_supplement_value'];
					if ($config['single_supplement_is_percent']) {
						$singleSupplementAmount = $cost * ($config['single_supplement_value'] / 100);
					}
					$cost += $singleSupplementAmount;
				}
			} else if ( $tariffWithDetails->type == self::PACKAGE_PER_PERSON ) {
				for ( $i = 1; $i <= $adultNumber; $i++ ) {
					$cost += $tariffWithDetails->details['adult'.$i][0]->price;
					$costAdults += $tariffWithDetails->details['adult'.$i][0]->price;
					// Calculate single supplement
					if ($config['enable_single_supplement'] && $adultNumber == 1 && count($childAges) == 0)
					{
						$singleSupplementAmount = $config['single_supplement_value'];
						if ($config['single_supplement_is_percent'])
						{
							$singleSupplementAmount = $cost * ($config['single_supplement_value'] / 100);
						}
						$cost += $singleSupplementAmount;
						$costAdults += $singleSupplementAmount;
					}
				}

				if ($config['child_room_cost_calc'] == 1) { // calculate per child age range
					for ( $i = 0; $i < count( $childAges ); $i++ ) {
						foreach ( $tariffWithDetails->details as $guestType => $guesTypeTariff ) {
							if ( substr( $guestType, 0, 5 ) == 'adult' ) {
								continue; // skip all adult's tariff
							}

							if
							(
								$childAges[$i] >= $tariffWithDetails->details[$guestType][0]->from_age
								&&
								$childAges[$i] <= $tariffWithDetails->details[$guestType][0]->to_age
							)
							{
								$cost += $tariffWithDetails->details[$guestType][0]->price;
							}
						}
					}
				} else { // calculate per child quantity
					for ($i = 0; $i < count($childAges); $i++) {
						$guestType = 'child' . ($i + 1);
						if
						(
							$childAges[$i] >= $tariffWithDetails->details[$guestType][0]->from_age
							&&
							$childAges[$i] <= $tariffWithDetails->details[$guestType][0]->to_age
						)
						{
							$cost += $tariffWithDetails->details[$guestType][0]->price;
							$costChildren += $tariffWithDetails->details[$guestType][0]->price;
						}
					}
				}
			}

			if ( isset( $coupon ) && is_array( $coupon ) ) {
				if ( $coupon['coupon_is_percent'] == 1 ) {
					$deductionAmount = $cost * ( $coupon['coupon_amount'] / 100 );
				} else {
					$deductionAmount = $coupon['coupon_amount'];
				}
				$cost -= $deductionAmount;
				$isAppliedCoupon = true;
			}

			// Calculate the imposed tax amount per day
			$totalImposedTaxAmountPerDay = 0;
			$totalImposedTaxAmountPerDayAdults = 0;
			$totalImposedTaxAmountPerDayChildren = 0;
			foreach ( $imposedTaxTypes as $taxType )
			{
				$totalImposedTaxAmountPerDay += $cost * $taxType->rate;
				$totalImposedTaxAmountPerDayAdults += $costAdults * $taxType->rate;
				$totalImposedTaxAmountPerDayChildren += $costChildren * $taxType->rate;
			}

			$totalBookingCost = $cost;
			$tariffBreakDownTemp[8]['gross'] = $cost;
			$tariffBreakDownTemp[8]['gross_adults'] = $costAdults;
			$tariffBreakDownTemp[8]['gross_children'] = $costChildren;
			$tariffBreakDownTemp[8]['tax'] = $totalImposedTaxAmountPerDay;
			$tariffBreakDownTemp[8]['net'] = $cost + $totalImposedTaxAmountPerDay;
			$tariffBreakDownTemp[8]['net_adults'] = $costAdults + $totalImposedTaxAmountPerDayAdults;
			$tariffBreakDownTemp[8]['net_children'] = $costChildren + $totalImposedTaxAmountPerDayChildren;

			$result = array(
				'total_booking_cost' => $totalBookingCost,
				'tariff_break_down' => $tariffBreakDownTemp,
				'is_applied_coupon' => $isAppliedCoupon,
				'single_supplement' => $singleSupplementAmount
			);

			$totalBookingCost = $result['total_booking_cost'];
			$totalSingleSupplement += $result['single_supplement'];
			$tempKeyWeekDay = key( $result['tariff_break_down'] );
			$tempSolidresCurrencyCostPerDayGross = clone $solidresCurrency;
			$tempSolidresCurrencyCostPerDayGrossAdults = clone $solidresCurrency;
			$tempSolidresCurrencyCostPerDayGrossChildren = clone $solidresCurrency;
			$tempSolidresCurrencyCostPerDayTax = clone $solidresCurrency;
			$tempSolidresCurrencyCostPerDayNet = clone $solidresCurrency;
			$tempSolidresCurrencyCostPerDayNetAdults = clone $solidresCurrency;
			$tempSolidresCurrencyCostPerDayNetChildren = clone $solidresCurrency;
			$tempSolidresCurrencyCostPerDayGross->set_value( $result['tariff_break_down'][$tempKeyWeekDay]['gross'] );
			$tempSolidresCurrencyCostPerDayGrossAdults->set_value($result['tariff_break_down'][$tempKeyWeekDay]['gross_adults']);
			$tempSolidresCurrencyCostPerDayGrossChildren->set_value($result['tariff_break_down'][$tempKeyWeekDay]['gross_children']);
			$tempSolidresCurrencyCostPerDayTax->set_value( $result['tariff_break_down'][$tempKeyWeekDay]['tax'] );
			$tempSolidresCurrencyCostPerDayNet->set_value( $result['tariff_break_down'][$tempKeyWeekDay]['net'] );
			$tempSolidresCurrencyCostPerDayNetAdults->set_value($result['tariff_break_down'][$tempKeyWeekDay]['net_adults']);
			$tempSolidresCurrencyCostPerDayNetChildren->set_value($result['tariff_break_down'][$tempKeyWeekDay]['net_children']);
			$tariffBreakDown[][$tempKeyWeekDay] = array(
				'gross' => $tempSolidresCurrencyCostPerDayGross,
				'gross_adults' => $tempSolidresCurrencyCostPerDayGrossAdults,
				'gross_children' => $tempSolidresCurrencyCostPerDayGrossChildren,
				'tax' => $tempSolidresCurrencyCostPerDayTax,
				'net' => $tempSolidresCurrencyCostPerDayNet,
				'net_adults' => $tempSolidresCurrencyCostPerDayNetAdults,
				'net_children' => $tempSolidresCurrencyCostPerDayNetChildren,
				'single_supplement' => $result['single_supplement']
			);

			unset( $tempSolidresCurrencyCostPerDayGross );
			unset( $tempSolidresCurrencyCostPerDayGrossAdults);
			unset( $tempSolidresCurrencyCostPerDayGrossChildren);
			unset( $tempSolidresCurrencyCostPerDayTax );
			unset( $tempSolidresCurrencyCostPerDayNet );
			unset( $tempSolidresCurrencyCostPerDayNetAdults);
			unset( $tempSolidresCurrencyCostPerDayNetChildren );
			unset( $tempKeyWeekDay );

			if ( $totalBookingCost > 0 ) {
				// Calculate the imposed tax amount
				$totalImposedTaxAmount = 0;
				foreach ( $imposedTaxTypes as $taxType ) {
					$totalImposedTaxAmount += $totalBookingCost * $taxType->rate;
				}

				$totalBookingCostTaxed = $totalBookingCost + $totalImposedTaxAmount;

				// Format the number with correct currency
				$totalBookingCostExcludedTaxedFormatted = clone $solidresCurrency;
				$totalBookingCostExcludedTaxedFormatted->set_value( $totalBookingCost );

				// Format the number with correct currency
				$totalBookingCostIncludedTaxedFormatted = clone $solidresCurrency;
				$totalBookingCostIncludedTaxedFormatted->set_value($totalBookingCostTaxed);

				// Calculate discounts, need to take before and after tax into consideration
				if ( defined('SR_PLUGIN_DISCOUNT_ENABLED') && SR_PLUGIN_DISCOUNT_ENABLED && count($discounts) > 0)
				{
					$reservationData = array(
						'checkin' => $checkin,
						'checkout' => $checkout,
						'discount_pre_tax' => $isDiscountPreTax,
						'stay_length' => $stayLength,
						'scope' => 'roomtype',
						'scope_id' => $roomTypeId,
						'total_reserved_room' => NULL,
						'total_price_tax_excl' => $totalBookingCost,
						'total_price_tax_incl' => $totalBookingCostTaxed,
						'booking_type' => $config['booking_type']
					);

					$solidresDiscount = new SR_Discount_Process($discounts, $reservationData);
					$solidresDiscount->calculate();
					$appliedDiscounts = $solidresDiscount->appliedDiscounts;
					$totalDiscount = $solidresDiscount->totalDiscount;
				}

				$totalBookingCostTaxInclDiscounted = $totalBookingCostTaxed - $totalDiscount;
				$totalBookingCostTaxExclDiscounted = $totalBookingCost - $totalDiscount;

				$totalBookingCostIncludedTaxedDiscountedFormatted = clone $solidresCurrency;
				$totalBookingCostIncludedTaxedDiscountedFormatted->set_value($totalBookingCostTaxInclDiscounted);
				$totalBookingCostExcludedTaxedDiscountedFormatted = clone $solidresCurrency;
				$totalBookingCostExcludedTaxedDiscountedFormatted->set_value($totalBookingCostTaxExclDiscounted);
				$totalDiscountFormatted = clone $solidresCurrency;
				$totalDiscountFormatted->set_value($totalDiscount);
				// End of discount calculation

				$totalSingleSupplementFormatted = clone $solidresCurrency;
				$totalSingleSupplementFormatted->set_value($totalSingleSupplement);
			}
		}

		$response = array(
			'total_price_formatted' => $totalBookingCostIncludedTaxedFormatted,
			'total_price_tax_incl_formatted' => $totalBookingCostIncludedTaxedFormatted,
			'total_price_tax_excl_formatted' => $totalBookingCostExcludedTaxedFormatted,
			'total_price' => $totalBookingCostTaxed,
			'total_price_tax_incl' => $totalBookingCostTaxed,
			'total_price_tax_excl' => $totalBookingCost,
			'total_price_discounted' => $totalBookingCostTaxInclDiscounted,
			'total_price_tax_incl_discounted' => $totalBookingCostTaxInclDiscounted,
			'total_price_tax_excl_discounted' => $totalBookingCostTaxExclDiscounted,
			'total_price_discounted_formatted' => $totalBookingCostIncludedTaxedDiscountedFormatted,
			'total_price_tax_incl_discounted_formatted' => $totalBookingCostIncludedTaxedDiscountedFormatted,
			'total_price_tax_excl_discounted_formatted' => $totalBookingCostExcludedTaxedDiscountedFormatted,
			'total_discount' => $totalDiscount,
			'total_discount_formatted' => $totalDiscountFormatted,
			'applied_discounts' => $appliedDiscounts,
			'tariff_break_down' => $tariffBreakDown,
			'is_applied_coupon' => isset( $result['is_applied_coupon'] ) ? $result['is_applied_coupon'] : NULL,
			'type' => isset( $tariffWithDetails->type ) ? $tariffWithDetails->type : NULL,
			'id' => isset( $tariffWithDetails->id ) ? $tariffWithDetails->id : NULL,
			'title' => isset( $tariffWithDetails->title ) ? $tariffWithDetails->title : NULL,
			'description' => isset( $tariffWithDetails->description ) ? $tariffWithDetails->description : NULL,
			'total_single_supplement' => $totalSingleSupplement,
			'total_single_supplement_formatted' => $totalSingleSupplementFormatted,
			'adjoining_layer' => isset($config['adjoining_layer']) ? $config['adjoining_layer'] : NULL
		);
		return $response;
	}

	/**
	 * Get price for Rate tariff type: either Rate per room per night or Rate per person per night
	 *
	 * @param array 	$tariffWithDetails
	 * @param int		$roomTypeId
	 * @param string 	$checkin
	 * @param string 	$checkout
	 * @param array		$imposedTaxTypes
	 * @param SRCurrency	$solidresCurrency
	 * @param null 		$coupon
	 * @param int 		$adultNumber
	 * @param array 	$childAges
	 * @param int 		$stayLength
	 * @param array 	$discounts
	 * @param boolean 	$isDiscountPreTax
	 * @param	array 	$config An array which holds extra config values for tariff calculation (since v0.9.0)
	 *
	 * @return array
	 */
	public function getPriceDaily($tariffWithDetails, $roomTypeId, $checkin, $checkout, $imposedTaxTypes, SR_Currency $solidresCurrency, $coupon = NULL, $adultNumber = 0, $childAges = array(), $stayLength = 0, $discounts, $isDiscountPreTax, $config) {
		$solidres_coupon = new SR_Coupon();
		$totalBookingCost = 0;
		$totalSingleSupplement = 0;
		$bookWeekDays = $this->calculateWeekDay( $checkin, $checkout );

		$isCouponApplicable = false;
		if ( isset ( $coupon ) && is_array( $coupon ) ) {
			$isCouponApplicable = $solidres_coupon->is_applicable( $coupon['coupon_id'], $roomTypeId );
		}

		$stayCount = 1;
		$tariffBreakDown = array();
		$tmpKeyWeekDay = NULL;

		// Add check for limit check in to field
		$checkinDay = new DateTime($checkin);
		$checkinDayInfo = getdate($checkinDay->format('U'));

		$isValid = self::isValid($tariffWithDetails, $checkin, $checkout, $stayLength, $checkinDayInfo);

		if ( $isValid && isset( $tariffWithDetails ) ) {
			foreach ( $bookWeekDays as $bookWeekDay ) {

				if ( $stayCount <= $stayLength ) {

					$theDay = new DateTime($bookWeekDay);
					$dayInfo = getdate($theDay->format('U'));

					// Deal with Coupon
					if ( $isCouponApplicable ) {
						$result = $this->calculateCostPerDay( $tariffWithDetails, $dayInfo, $coupon, $adultNumber, $childAges, $imposedTaxTypes, $config );
					} else {
						$result = $this->calculateCostPerDay( $tariffWithDetails, $dayInfo, NULL, $adultNumber, $childAges, $imposedTaxTypes, $config );
					}

					$totalBookingCost += $result['total_booking_cost'];
					$totalSingleSupplement += $result['single_supplement'];
					$tempKeyWeekDay = key($result['tariff_break_down']);
					$tempSolidresCurrencyCostPerDayGross = clone $solidresCurrency;
					$tempSolidresCurrencyCostPerDayGrossAdults = clone $solidresCurrency;
					$tempSolidresCurrencyCostPerDayGrossChildren = clone $solidresCurrency;
					$tempSolidresCurrencyCostPerDayTax = clone $solidresCurrency;
					$tempSolidresCurrencyCostPerDayNet = clone $solidresCurrency;
					$tempSolidresCurrencyCostPerDayNetAdults = clone $solidresCurrency;
					$tempSolidresCurrencyCostPerDayNetChildren = clone $solidresCurrency;
					$tempSolidresCurrencyCostPerDayGross->set_value( $result['tariff_break_down'][$tempKeyWeekDay]['gross'] );
					$tempSolidresCurrencyCostPerDayGrossAdults->set_value($result['tariff_break_down'][$tempKeyWeekDay]['gross_adults']);
					$tempSolidresCurrencyCostPerDayGrossChildren->set_value($result['tariff_break_down'][$tempKeyWeekDay]['gross_children']);
					$tempSolidresCurrencyCostPerDayTax->set_value( $result['tariff_break_down'][$tempKeyWeekDay]['tax'] );
					$tempSolidresCurrencyCostPerDayNet->set_value( $result['tariff_break_down'][$tempKeyWeekDay]['net'] );
					$tempSolidresCurrencyCostPerDayNetAdults->set_value($result['tariff_break_down'][$tempKeyWeekDay]['net_adults']);
					$tempSolidresCurrencyCostPerDayNetChildren->set_value($result['tariff_break_down'][$tempKeyWeekDay]['net_children']);
					$tariffBreakDown[][$tempKeyWeekDay] = array(
						'gross' => $tempSolidresCurrencyCostPerDayGross,
						'gross_adults' => $tempSolidresCurrencyCostPerDayGrossAdults,
						'gross_children' => $tempSolidresCurrencyCostPerDayGrossChildren,
						'tax' => $tempSolidresCurrencyCostPerDayTax,
						'net' => $tempSolidresCurrencyCostPerDayNet,
						'net_adults' => $tempSolidresCurrencyCostPerDayNetAdults,
						'net_children' => $tempSolidresCurrencyCostPerDayNetChildren,
						'single_supplement' => $result['single_supplement']
					);
				}
				$stayCount ++;
			}
		}

		unset( $tempSolidresCurrencyCostPerDayGross );
		unset( $tempSolidresCurrencyCostPerDayTax );
		unset( $tempSolidresCurrencyCostPerDayNet );
		unset( $tempKeyWeekDay );

		$totalBookingCostIncludedTaxedFormatted = NULL;
		$totalBookingCostExcludedTaxedFormatted = NULL;
		$totalBookingCostTaxed = NULL;
		$totalBookingCostTaxedDiscounted = NULL; // Total booking cost (tax included) and discounted
		$totalBookingCostIncludedTaxedDiscountedFormatted = NULL;
		$totalBookingCostExcludedTaxedDiscountedFormatted = NULL;
		$totalBookingCostTaxInclDiscounted = 0;
		$totalBookingCostTaxExclDiscounted = 0;
		$totalDiscount = 0;
		$totalDiscountFormatted = NULL;
		$appliedDiscounts = NULL;
		$totalSingleSupplementFormatted = NULL;

		if ( $totalBookingCost > 0) {
			// Calculate the imposed tax amount
			$totalImposedTaxAmount = 0;
			foreach ( $imposedTaxTypes as $taxType ) {
				$totalImposedTaxAmount += $totalBookingCost * $taxType->rate;
			}

			$totalBookingCostTaxed = $totalBookingCost + $totalImposedTaxAmount;

			// Format the number with correct currency
			$totalBookingCostExcludedTaxedFormatted = clone $solidresCurrency;
			$totalBookingCostExcludedTaxedFormatted->set_value($totalBookingCost);

			// Format the number with correct currency
			$totalBookingCostIncludedTaxedFormatted = clone $solidresCurrency;
			$totalBookingCostIncludedTaxedFormatted->set_value($totalBookingCostTaxed);

			// Calculate discounts, need to take before and after tax into consideration
			$appliedDiscounts = array();
			$totalDiscount = 0;
			if ( defined( 'SR_PLUGIN_DISCOUNT_ENABLED' ) && SR_PLUGIN_DISCOUNT_ENABLED && count($discounts) > 0)
			{
				$reservationData = array(
					'checkin' => $checkin,
					'checkout' => $checkout,
					'discount_pre_tax' => $isDiscountPreTax,
					'stay_length' => $stayLength,
					'scope' => 'roomtype',
					'scope_id' => $roomTypeId,
					'total_reserved_room' => NULL,
					'total_price_tax_excl' => $totalBookingCost,
					'total_price_tax_incl' => $totalBookingCostTaxed,
					'booking_type' => $config['booking_type']
				);

				$solidresDiscount = new SR_Discount_Process($discounts, $reservationData);
				$solidresDiscount->calculate();
				$appliedDiscounts = $solidresDiscount->appliedDiscounts;
				$totalDiscount = $solidresDiscount->totalDiscount;
			}

			$totalBookingCostTaxInclDiscounted = $totalBookingCostTaxed - $totalDiscount;
			$totalBookingCostTaxExclDiscounted = $totalBookingCost - $totalDiscount;

			$totalBookingCostIncludedTaxedDiscountedFormatted = clone $solidresCurrency;
			$totalBookingCostIncludedTaxedDiscountedFormatted->set_value($totalBookingCostTaxInclDiscounted);
			$totalBookingCostExcludedTaxedDiscountedFormatted = clone $solidresCurrency;
			$totalBookingCostExcludedTaxedDiscountedFormatted->set_value($totalBookingCostTaxExclDiscounted);
			$totalDiscountFormatted = clone $solidresCurrency;
			$totalDiscountFormatted->set_value($totalDiscount);
			// End of discount calculation

			$totalSingleSupplementFormatted = clone $solidresCurrency;
			$totalSingleSupplementFormatted->set_value($totalSingleSupplement);
		}
		$response = array(
			'total_price_formatted' => $totalBookingCostIncludedTaxedFormatted,
			'total_price_tax_incl_formatted' => $totalBookingCostIncludedTaxedFormatted,
			'total_price_tax_excl_formatted' => $totalBookingCostExcludedTaxedFormatted,
			'total_price' => $totalBookingCostTaxed,
			'total_price_tax_incl' => $totalBookingCostTaxed,
			'total_price_tax_excl' => $totalBookingCost,
			'total_price_discounted' => $totalBookingCostTaxInclDiscounted,
			'total_price_tax_incl_discounted' => $totalBookingCostTaxInclDiscounted,
			'total_price_tax_excl_discounted' => $totalBookingCostTaxExclDiscounted,
			'total_price_discounted_formatted' => $totalBookingCostIncludedTaxedDiscountedFormatted,
			'total_price_tax_incl_discounted_formatted' => $totalBookingCostIncludedTaxedDiscountedFormatted,
			'total_price_tax_excl_discounted_formatted' => $totalBookingCostExcludedTaxedDiscountedFormatted,
			'total_discount' => $totalDiscount,
			'total_discount_formatted' => $totalDiscountFormatted,
			'applied_discounts' => $appliedDiscounts,
			'tariff_break_down' => $tariffBreakDown,
			'is_applied_coupon' => isset ( $result['is_applied_coupon'] ) ? $result['is_applied_coupon'] : false,
			'type' => isset ( $tariffWithDetails->type ) ? $tariffWithDetails->type : NULL,
			'id' => isset ( $tariffWithDetails->id ) ? $tariffWithDetails->id : NULL,
			'title' => isset ( $tariffWithDetails->title ) ? $tariffWithDetails->title : NULL,
			'description' => isset ( $tariffWithDetails->description ) ? $tariffWithDetails->description : NULL,
			'total_single_supplement' => $totalSingleSupplement,
			'total_single_supplement_formatted' => $totalSingleSupplementFormatted,
			'adjoining_layer' => isset($config['adjoining_layer']) ? $config['adjoining_layer'] : NULL
		);

		return $response;
	}

	/**
	 * Get price of a room type from a list of room type's tariff that matches the conditions:
	 *        Customer group
	 *        Checkin && Checkout date
	 *        Adult number
	 *        Child number & ages
	 *        Min & Max number of nights
	 *
	 * @param   int $roomTypeId
	 * @param   $customerGroupId
	 * @param   $imposedTaxTypes
	 * @param   bool $defaultTariff
	 * @param   bool $dateConstraint @deprecated
	 * @param   string $checkin
	 * @param   string $checkout
	 * @param   SRCurrency $solidresCurrency The currency object
	 * @param   array $coupon An array of coupon information
	 * @param   int $adultNumber Number of adult, default is 0
	 * @param   array $childAges An array of children age, it is associated with the $childNumber
	 * @param   int $stayLength 0 means ignore this condition
	 * @param   array $discounts
	 * @param	int $isDiscountPreTax
	 * @param	array $config An array which holds extra config values for tariff calculation (since v0.9.0)
	 *
	 * @return  array    An array of SRCurrency for Tax and Without Tax
	 */
	public function getPriceLegacy($roomTypeId, $customerGroupId, $imposedTaxTypes, $defaultTariff = false, $dateConstraint = false, $checkin = '', $checkout = '', SR_Currency $solidresCurrency, $coupon = NULL, $adultNumber = 0, $childAges = array(), $stayLength = 0, $discounts, $isDiscountPreTax, $config )
	{
		$srCoupon = new SR_Coupon();
		$solidres_tariff = new SR_Tariff();
		global $wpdb;

		$totalBookingCost = 0;
		$totalSingleSupplement = 0;

		$tariff_query_params = array();
		$tariff_query_params[ 'room_type_id' ] = $roomTypeId;
		$tariff_query_params[ 'customer_group_id' ] = $customerGroupId;
		$tariff_query_params[ 'state' ] = 1;
		$tariff_query_params[ 'default_tariff' ] = $defaultTariff;
		$tariff_query_params[ 'date_constraint' ] = $dateConstraint;
		$tariff_query_params[ 'partial_match' ] = 1;
		$tariff_query_params[ 'checkin' ] = $checkin;
		$tariff_query_params[ 'checkout' ] = $checkout;

		$bookWeekDays = $this->calculateWeekDay($checkin, $checkout);

		$isCouponApplicable = false;
		if (isset($coupon) && is_array($coupon))
		{
			$isCouponApplicable = $srCoupon->isApplicable($coupon['coupon_id'], $roomTypeId);
		}

		// Special check for limit check in to, check checkin date with the first tariff we found.
		$isValid = true;
		if ($dateConstraint)
		{
			// Reset these state because we may override it in other steps
			$tariff_query_params[ 'date_constraint' ] = 1;
			$tariff_query_params[ 'default_tariff' ] = NULL;
			$tariff_query_params[ 'customer_group_id' ] = $customerGroupId;
			$tariff_query_params[ 'bookday' ] = date( 'Y-m-d', strtotime($bookWeekDays[0]) );
			/*if ($config['adjoining_tariffs_mode'] == 1 && $stayLengthCharged > 0)
			{
				$modelTariffs->setState('filter.stay_length', $stayLengthCharged);
			}*/
			$tariff_query = $this->build_tariff_query($tariff_query_params);
			$tariff1 = $wpdb->get_results($tariff_query);
			if (isset($tariff1[0]))
			{
				if (!is_null($tariff1[0]->limit_checkin))
				{
					$theDay1 = new DateTime($bookWeekDays[0]);
					$dayInfo1 = getdate($theDay1->format('U'));
					$limitCheckInToArray = json_decode($tariff1[0]->limit_checkin);
					if (!in_array($dayInfo1['wday'], $limitCheckInToArray))
					{
						$isValid = false;
					}
				}
			}
		}
		// End special check

		$stayCount = 1;
		$tariffBreakDown = array();
		$tempTariffId = 0;
		$tmpKeyWeekDay = NULL;
		$stayLengthLimitArray = array();
		if ($isValid)
		{
			foreach ($bookWeekDays as $bookWeekDay)
			{
				if ($stayCount <= $stayLength)
				{
					$theDay = new DateTime($bookWeekDay);
					$dayInfo = getdate($theDay->format('U'));

					// Find Complex Tariff
					if ($dateConstraint)
					{
						// Reset these state because we may override it in other steps
						$tariff_query_params[ 'date_constraint' ] = 1;
						$tariff_query_params[ 'default_tariff' ] = NULL;
						$tariff_query_params[ 'customer_group_id' ] = $customerGroupId;
						$tariff_query_params[ 'bookday' ] = date( 'Y-m-d', strtotime($bookWeekDay) );
						/*if ($config['adjoining_tariffs_mode'] == 1 && $stayLengthCharged > 0)
						{
							$modelTariffs->setState('filter.stay_length', $stayLengthCharged);
						}*/
						$tariff_query = $this->build_tariff_query($tariff_query_params);
						$tariff = $wpdb->get_results( $tariff_query );
					}
					else // Or find Standard Tariff
					{
						$tariff_query_params[ 'date_constraint' ] = NULL;
						$tariff_query_params[ 'default_tariff' ] = 1;
						$tariff_query_params[ 'customer_group_id' ] = -1;
						$tariff_query = $this->build_tariff_query($tariff_query_params);
						$tariff = $wpdb->get_results( $tariff_query );
					}

					$result = array(
						'total_booking_cost' => 0,
						'tariff_break_down' => array(),
						'is_applied_coupon' => false
					);

					if (!empty($tariff))
					{
						$stayLengthLimitArray[$tariff[$config['adjoining_layer']]->id] = $tariff[$config['adjoining_layer']]->d_min;

						// Then we load the tariff details: price for each week day
						// Caching stuff
						if ($tempTariffId != $tariff[$config['adjoining_layer']]->id)
						{
							$tariffWithDetails = $solidres_tariff->load( $tariff[$config['adjoining_layer']]->id );
							$tempTariffId = $tariff[$config['adjoining_layer']]->id;
						}

						// Deal with Coupon
						if ($isCouponApplicable)
						{
							$result = $this->calculateCostPerDay($tariffWithDetails, $dayInfo, $coupon, $adultNumber, $childAges, $imposedTaxTypes, $config);
						}
						else
						{
							$result = $this->calculateCostPerDay($tariffWithDetails, $dayInfo, NULL, $adultNumber, $childAges, $imposedTaxTypes, $config);
						}

						$totalBookingCost += $result['total_booking_cost'];
						$totalSingleSupplement += $result['single_supplement'];
						$tempKeyWeekDay = key($result['tariff_break_down']);
						$tempSolidresCurrencyCostPerDayGross = clone $solidresCurrency;
						$tempSolidresCurrencyCostPerDayGrossAdults = clone $solidresCurrency;
						$tempSolidresCurrencyCostPerDayGrossChildren = clone $solidresCurrency;
						$tempSolidresCurrencyCostPerDayTax = clone $solidresCurrency;
						$tempSolidresCurrencyCostPerDayNet = clone $solidresCurrency;
						$tempSolidresCurrencyCostPerDayNetAdults = clone $solidresCurrency;
						$tempSolidresCurrencyCostPerDayNetChildren = clone $solidresCurrency;
						$tempSolidresCurrencyCostPerDayGross->set_value($result['tariff_break_down'][$tempKeyWeekDay]['gross']);
						$tempSolidresCurrencyCostPerDayGrossAdults->set_value($result['tariff_break_down'][$tempKeyWeekDay]['gross_adults']);
						$tempSolidresCurrencyCostPerDayGrossChildren->set_value($result['tariff_break_down'][$tempKeyWeekDay]['gross_children']);
						$tempSolidresCurrencyCostPerDayTax->set_value($result['tariff_break_down'][$tempKeyWeekDay]['tax']);
						$tempSolidresCurrencyCostPerDayNet->set_value($result['tariff_break_down'][$tempKeyWeekDay]['net']);
						$tempSolidresCurrencyCostPerDayNetAdults->set_value($result['tariff_break_down'][$tempKeyWeekDay]['net_adults']);
						$tempSolidresCurrencyCostPerDayNetChildren->set_value($result['tariff_break_down'][$tempKeyWeekDay]['net_children']);
						$tariffBreakDown[][$tempKeyWeekDay] = array(
							'gross' => $tempSolidresCurrencyCostPerDayGross,
							'gross_adults' => $tempSolidresCurrencyCostPerDayGrossAdults,
							'gross_children' => $tempSolidresCurrencyCostPerDayGrossChildren,
							'tax' => $tempSolidresCurrencyCostPerDayTax,
							'net' => $tempSolidresCurrencyCostPerDayNet,
							'net_adults' => $tempSolidresCurrencyCostPerDayNetAdults,
							'net_children' => $tempSolidresCurrencyCostPerDayNetChildren,
							'single_supplement' => $result['single_supplement']
						);
					}
				}
				$stayCount ++;
			}
		}


		unset($tempSolidresCurrencyCostPerDayGross);
		unset($tempSolidresCurrencyCostPerDayTax);
		unset($tempSolidresCurrencyCostPerDayNet);
		unset($tempKeyWeekDay);

		$totalBookingCostIncludedTaxedFormatted = NULL;
		$totalBookingCostExcludedTaxedFormatted = NULL;
		$totalBookingCostTaxed = NULL;
		$totalBookingCostTaxInclDiscounted = 0;
		$totalBookingCostTaxExclDiscounted = 0;
		$totalBookingCostIncludedTaxedDiscountedFormatted = NULL;
		$totalBookingCostExcludedTaxedDiscountedFormatted = NULL;
		$totalDiscount = 0;
		$totalDiscountFormatted = NULL;
		$appliedDiscounts = NULL;
		$totalSingleSupplementFormatted = NULL;

		$isBookingPermitted = false;
		$stayLengthLimitArray = array_values($stayLengthLimitArray);
		sort($stayLengthLimitArray);
		switch ($config['adjoining_tariffs_mode'])
		{
			case 0:
				$isBookingPermitted = true;
				break;
			case 1: // Lowest - Use lowest min stay length of any adjoining tariffs
				if ($stayLength >= $stayLengthLimitArray[0])
				{
					$isBookingPermitted = true;
				}
				break;
			case 2: // Highest - Use highest min stay length of any adjoining tariffs
				if ($stayLength >= $stayLengthLimitArray[1])
				{
					$isBookingPermitted = true;
				}
				break;
		}

		if ($totalBookingCost > 0 && $isBookingPermitted)
		{
			// Calculate the imposed tax amount
			$totalImposedTaxAmount = 0;
			foreach ($imposedTaxTypes as $taxType)
			{
				$totalImposedTaxAmount += $totalBookingCost * $taxType->rate;
			}

			$totalBookingCostTaxed = $totalBookingCost + $totalImposedTaxAmount;

			// Format the number with correct currency
			$totalBookingCostExcludedTaxedFormatted = clone $solidresCurrency;
			$totalBookingCostExcludedTaxedFormatted->set_value($totalBookingCost);

			// Format the number with correct currency
			$totalBookingCostIncludedTaxedFormatted = clone $solidresCurrency;
			$totalBookingCostIncludedTaxedFormatted->set_value($totalBookingCostTaxed);

			// Calculate discounts, need to take before and after tax into consideration
			$appliedDiscounts = array();
			$totalDiscount = 0;
			if ( defined('SR_PLUGIN_DISCOUNT_ENABLED') && SR_PLUGIN_DISCOUNT_ENABLED && count($discounts) > 0)
			{
				$reservationData = array(
					'checkin' => $checkin,
					'checkout' => $checkout,
					'discount_pre_tax' => $isDiscountPreTax,
					'stay_length' => $stayLength,
					'scope' => 'roomtype',
					'scope_id' => $roomTypeId,
					'total_reserved_room' => NULL,
					'total_price_tax_excl' => $totalBookingCost,
					'total_price_tax_incl' => $totalBookingCostTaxed,
					'booking_type' => $config['booking_type']
				);

				$solidresDiscount = new SR_Discount_Process($discounts, $reservationData);
				$solidresDiscount->calculate();
				$appliedDiscounts = $solidresDiscount->appliedDiscounts;
				$totalDiscount = $solidresDiscount->totalDiscount;
			}

			$totalBookingCostTaxInclDiscounted = $totalBookingCostTaxed - $totalDiscount;
			$totalBookingCostTaxExclDiscounted = $totalBookingCost - $totalDiscount;

			$totalBookingCostIncludedTaxedDiscountedFormatted = clone $solidresCurrency;
			$totalBookingCostIncludedTaxedDiscountedFormatted->set_value($totalBookingCostTaxInclDiscounted);
			$totalBookingCostExcludedTaxedDiscountedFormatted = clone $solidresCurrency;
			$totalBookingCostExcludedTaxedDiscountedFormatted->set_value($totalBookingCostTaxExclDiscounted);
			$totalDiscountFormatted = clone $solidresCurrency;
			$totalDiscountFormatted->set_value($totalDiscount);
			// End of discount calculation

			$totalSingleSupplementFormatted = clone $solidresCurrency;
			$totalSingleSupplementFormatted->set_value($totalSingleSupplement);
		}

		$joinedDescription = '';
		if (!isset($config['adjoining_tariffs_show_desc']))
		{
			$config['adjoining_tariffs_show_desc'] = 0;
		}
		if ($config['adjoining_tariffs_show_desc'])
		{
			$joinedDescription = implode(' ', array_filter(array($tariff1[0]->description, $tariff[0]->description)));
		}

		$response = array(
			'total_price_formatted' => $totalBookingCostIncludedTaxedFormatted,
			'total_price_tax_incl_formatted' => $totalBookingCostIncludedTaxedFormatted,
			'total_price_tax_excl_formatted' => $totalBookingCostExcludedTaxedFormatted,
			'total_price' => $totalBookingCostTaxed,
			'total_price_tax_incl' => $totalBookingCostTaxed,
			'total_price_tax_excl' => $totalBookingCost,
			'total_price_discounted' => $totalBookingCostTaxInclDiscounted,
			'total_price_tax_incl_discounted' => $totalBookingCostTaxInclDiscounted,
			'total_price_tax_excl_discounted' => $totalBookingCostTaxExclDiscounted,
			'total_price_discounted_formatted' => $totalBookingCostIncludedTaxedDiscountedFormatted,
			'total_price_tax_incl_discounted_formatted' => $totalBookingCostIncludedTaxedDiscountedFormatted,
			'total_price_tax_excl_discounted_formatted' => $totalBookingCostExcludedTaxedDiscountedFormatted,
			'total_discount' => $totalDiscount,
			'total_discount_formatted' => $totalDiscountFormatted,
			'applied_discounts' => $appliedDiscounts,
			'tariff_break_down' => $tariffBreakDown,
			'is_applied_coupon' => isset($result['is_applied_coupon']) ? $result['is_applied_coupon'] : false,
			'type' => isset($tariff[0]->type) ? $tariff[0]->type : NULL,
			'id' => 0 - $config['adjoining_layer'], // special id for joined tariffs case
			'title' => NULL,
			'description' => $joinedDescription ,
			'total_single_supplement' => $totalSingleSupplement,
			'total_single_supplement_formatted' => $totalSingleSupplementFormatted,
			'adjoining_layer' => $config['adjoining_layer']
		);

		return $response;
	}

	/**
	 * Get an array of week days in the period between $from and $to
	 * @param    string   From date
	 * @param    string   To date
	 * @return   array	  An array in format array(0 => 'Y-m-d', 1 => 'Y-m-d')
	 */
	private function calculateWeekDay( $from, $to ) {
		$datetime1 	= new DateTime( $from );
		$interval 	= SR_Utilities::calculate_date_diff( $from, $to );
		$weekDays 	= array();
		$weekDays[] = $datetime1->format( 'Y-m-d' );
		for ( $i = 1; $i <= (int)$interval; $i++ ) {
			$weekDays[] = $datetime1->modify( '+1 day' )->format( 'Y-m-d' );
		}
		return $weekDays;
	}

	/**
	 * Calculate booking cost per day and apply the coupon if possible
	 * @param   array   $tariff   	An array of tariffs for searching
	 * @param   array   $dayInfo 	The date that we need to find tariff for it from above $tariff
	 * @param   array   $coupon 	An array of coupon information
	 * @param   int     $adultNumber Number of adult, only used for tariff Per person per room
	 * @param   array   $childAges   Children ages, it is associated with $childNumber
	 * @param   arrray  $imposedTaxTypes All imposed tax types
	 * @return  array
	 */
	private function calculateCostPerDay( $tariff, $dayInfo, $coupon = NULL, $adultNumber, $childAges, $imposedTaxTypes, $config ) {
		$totalBookingCost = 0;
		$tariffBreakDown = array();
		$costPerDay = 0;
		$costPerDayAdults = 0;
		$costPerDayChildren = 0;
		$isAppliedCoupon = false;
		$deductionAmount = 0;
		$singleSupplementAmount = 0;

		if ( $tariff->type == self::PER_ROOM_PER_NIGHT ) {
			for ( $i = 0, $count = count( $tariff->details['per_room'] ); $i < $count; $i ++ ) {
				if ( $tariff->details['per_room'][$i]->w_day == $dayInfo['wday']) {
					$costPerDay = $tariff->details['per_room'][$i]->price;
					// Calculate single supplement
					if ($config['enable_single_supplement'] && $adultNumber == 1 && count($childAges) == 0)
					{
						$singleSupplementAmount = $config['single_supplement_value'];
						if ($config['single_supplement_is_percent'])
						{
							$singleSupplementAmount = $costPerDay * ($config['single_supplement_value'] / 100);
						}
						$costPerDay += $singleSupplementAmount;
					}
					break; // we found the tariff we need, get out of here
				}
			}
		}
		else if ($tariff->type == self::PER_PERSON_PER_NIGHT) {
			// Calculate cost per day for each adult
			for ($i = 1; $i <= $adultNumber; $i++) {
				$adultIndex = 'adult'.$i;
				for ($t = 0, $count = count($tariff->details[$adultIndex]); $t < $count; $t ++) {
					if ($tariff->details[$adultIndex][$t]->w_day == $dayInfo['wday']) {
						$costPerDay += $tariff->details[$adultIndex][$t]->price;
						$costPerDayAdults += $tariff->details[$adultIndex][$t]->price;
						// Calculate single supplement
						if ($config['enable_single_supplement'] && $adultNumber == 1 && count($childAges) == 0)
						{
							$singleSupplementAmount = $config['single_supplement_value'];
							if ($config['single_supplement_is_percent'])
							{
								$singleSupplementAmount = $costPerDay * ($config['single_supplement_value'] / 100);
							}
							$costPerDay += $singleSupplementAmount;
							$costPerDayAdults += $singleSupplementAmount;
						}
						break; // we found the tariff we need, get out of here
					}
				}
			}

			if ($config['child_room_cost_calc'] == 1) {// calculate per child age range
				// Calculate cost per day for each child, take their ages into consideration
				for ( $i = 0; $i < count( $childAges ); $i++ ) {
					foreach ( $tariff->details as $guestType => $guesTypeTariff ) {
						if ( substr( $guestType, 0, 5 ) == 'adult' ) {
							continue; // skip all adult's tariff
						}

						for ( $t = 0, $count = count( $tariff->details[$guestType] ); $t < $count; $t ++) {
							if (
								$tariff->details[$guestType][$t]->w_day == $dayInfo['wday']
								&&
								( $childAges[$i] >= $tariff->details[$guestType][$t]->from_age && $childAges[$i] <= $tariff->details[$guestType][$t]->to_age )
							)
							{
								$costPerDay += $tariff->details[$guestType][$t]->price;
								$costPerDayChildren += $tariff->details[$guestType][$t]->price;
								break; // found it, get out of here
							}
						}
					}
				}
			} else { // calculate per child quantity
				for ($i = 0; $i < count($childAges); $i++) {
					$guestType = 'child' . ($i + 1);
					for ($t = 0, $count = count($tariff->details[$guestType]); $t < $count; $t ++)
					{
						if ($tariff->details[$guestType][$t]->w_day == $dayInfo['wday'])
						{
							$costPerDay += $tariff->details[$guestType][$t]->price;
							$costPerDayChildren += $tariff->details[$guestType][$t]->price;
							break; // found it, get out of here
						}
					}
				}
			}


		}

		if ( isset ( $coupon ) && is_array( $coupon ) ) {
			if ( $coupon['coupon_is_percent'] == 1) {
				$deductionAmount = $costPerDay * ( $coupon['coupon_amount'] / 100 );
			} else {
				$deductionAmount = $coupon['coupon_amount'];
			}
			$costPerDay -= $deductionAmount;
			$isAppliedCoupon = true;
		}

		// Calculate the imposed tax amount per day
		$totalImposedTaxAmountPerDay = 0;
		$totalImposedTaxAmountPerDayAdults = 0;
		$totalImposedTaxAmountPerDayChildren = 0;
		foreach ( $imposedTaxTypes as $taxType ) {
			$totalImposedTaxAmountPerDay += $costPerDay * $taxType->rate;
			$totalImposedTaxAmountPerDayAdults += $costPerDayAdults * $taxType->rate;
			$totalImposedTaxAmountPerDayChildren += $costPerDayChildren * $taxType->rate;
		}
		$totalBookingCost += $costPerDay;
		$tariffBreakDown[$dayInfo['wday']]['gross'] = $costPerDay;
		$tariffBreakDown[$dayInfo['wday']]['gross_adults'] = $costPerDayAdults;
		$tariffBreakDown[$dayInfo['wday']]['gross_children'] = $costPerDayChildren;
		$tariffBreakDown[$dayInfo['wday']]['deduction'] = $deductionAmount;
		$tariffBreakDown[$dayInfo['wday']]['tax'] = $totalImposedTaxAmountPerDay;
		$tariffBreakDown[$dayInfo['wday']]['net'] = $costPerDay + $totalImposedTaxAmountPerDay;
		$tariffBreakDown[$dayInfo['wday']]['net_adults'] = $costPerDayAdults + $totalImposedTaxAmountPerDayAdults;
		$tariffBreakDown[$dayInfo['wday']]['net_children'] = $costPerDayChildren + $totalImposedTaxAmountPerDayChildren;
		$tariffBreakDown[$dayInfo['wday']]['single_supplement'] = $singleSupplementAmount;

		return array(
			'total_booking_cost' => $totalBookingCost,
			'total_booking_cost_adults' => $costPerDayAdults,
			'total_booking_cost_children' => $costPerDayChildren,
			'tariff_break_down' => $tariffBreakDown,
			'is_applied_coupon' => $isAppliedCoupon,
			'single_supplement' => $singleSupplementAmount
		);
	}

	/**
	 * Get a room type by id
	 *
	 * @param $id
	 * @param $partner_id
	 * @param $output
	 *
	 * @return mixed
	 */
	public function load( $id = 0, $partner_id = 0, $output = OBJECT ) {
		$assets = new SR_Asset();
		if( $partner_id > 0 ) {
			$item = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT rt.*, ra.partner_id as partner_id FROM {$this->wpdb->prefix}sr_room_types rt LEFT JOIN {$this->wpdb->prefix}sr_reservation_assets ra ON rt.reservation_asset_id = ra.id WHERE rt.id = %d AND ra.partner_id = %d", $id, $partner_id ), $output );
			if( ! empty( $item ) ) {
				$tableRA = $assets->load( $item->reservation_asset_id );
			}
		} else {
			$item = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_room_types WHERE id = %d", $id ), $output );
			if( ! empty( $item ) ) {
				$tableRA = $assets->load( $item->reservation_asset_id, $partner_id );
			}
		}
		if ( isset( $item->id ) ) {
			$media = new SR_Media();
			$item->default_tariff = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT p.*, c.currency_code, c.currency_name FROM {$this->wpdb->prefix}sr_tariffs as p LEFT JOIN {$this->wpdb->prefix}sr_currencies as c ON c.id = p.currency_id WHERE room_type_id = %d AND valid_from = '0000-00-00' AND valid_to = '0000-00-00'", empty( $item->id ) ? 0 : (int) $item->id ) );

			if ( isset( $item->default_tariff ) ) {
				$item->default_tariff->details = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT id, tariff_id, price, w_day, guest_type, from_age, to_age FROM {$this->wpdb->prefix}sr_tariff_details WHERE tariff_id = %d ORDER BY w_day ASC", (int) $item->default_tariff->id ) );
			}

			$item->roomList = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT a.id, a.label FROM {$this->wpdb->prefix}sr_rooms a WHERE room_type_id = %d", empty( $item->id ) ? 0 : (int)$item->id  ) );

			// Load media
			$item->media = $media->load_by_room_type_id( $item->id );

			// Load params
			if ( isset( $item->params ) ) {
				$item->params = json_decode( $item->params, true );
			}

			// Load custom fields
			$roomtype_custom_fields_obj = new SR_Custom_Field( array(
				'id'   => (int) $item->id,
				'type' => 'room_type'
			) );

			$roomtype_custom_fields = $roomtype_custom_fields_obj->create_array_group();

			foreach ( $roomtype_custom_fields as $group_name => $fields ) {
				foreach ( $fields as $field ) {
					$item->roomtype_custom_fields[ $roomtype_custom_fields_obj->split_field_name( $field[0] ) ]  = apply_filters( 'solidres_roomtype_customfield', $field[1] );
				}
			}
		}

		// Load currency
		$currencies = new SR_Currency();
		if( isset( $tableRA ) ) {
			$currency = $currencies->load( $tableRA->currency_id );
		}
		if( ! empty( $item ) ) {
			$item->currency = $currency;
		}
		return $item;
	}

	/**
	 * Get a single room type by alias (slug)
	 *
	 * @param $alias
	 *
	 * @return mixed
	 */
	public function load_by_alias( $alias ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_room_types WHERE alias = %s", $alias ) );
	}

	/**
	 * Get a list of room type by asset's id
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function load_by_asset_id ( $id ) {
		return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_room_types
																WHERE reservation_asset_id = %d AND state = 1 ORDER BY ordering ASC", $id ) );
	}

	/**
	 * Get a list of room type by asset's alias (slug)
	 *
	 * @param $alias
	 *
	 * @return mixed
	 */
	public function load_by_asset_alias ( $alias ) {
		return $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_room_types WHERE alias = %s", $alias ) );
	}

	/**
	 * Load all room type's custom fields
	 *
	 * @param $id
	 *
	 * @return string
	 *
	 */
	public function load_custom_fields( $id = 0 ){
		return new SR_Custom_Field( array( 'id' => (int) $id, 'type' => 'room_type' ) );
	}

	/**
	 * Get the min price from a given tariff and show the formatted result
	 *
	 * @param $tariff
	 * @param $solidres_currency
	 * @param $show_tax_incl
	 * @param $imposed_tax_types
	 * @param $asset
	 *
	 * @return string
	 */
	public function get_min_price( $tariff, $solidres_currency, $show_tax_incl, $imposed_tax_types, $asset ) {
		$tariff_suffix = '';
		$min = NULL;
		$stay_length = 0;
		if ( $tariff->type == 0 || $tariff->type == 2 ) :
			$tariff_suffix .= __( '/ room ', 'solidres');
		else :
			$tariff_suffix .= __('/ person ', 'solidres' );
		endif;

		switch ( $tariff->type ) {
			case 0: // rate per room per night
				$min = array_reduce( $tariff->details['per_room'], function( $t1, $t2 ) {
					return $t1->price < $t2->price ? $t1 : $t2;
				}, array_shift( $tariff->details['per_room'] ) );
				$stay_length = 1;
				break;
			case 1: // rate per person per night
				$min = array_reduce( $tariff->details['adult1'], function( $t1, $t2 ) {
					return $t1->price < $t2->price ? $t1 : $t2;
				}, array_shift( $tariff->details['adult1'] ) );
				$stay_length = 1;
				break;
			case 2: // package per room
				$min = $tariff->details['per_room'][0];
				$stay_length = $tariff->d_min;
				break;
			case 3: // package per person
				$min = $tariff->details['adult1'][0];
				$stay_length = $tariff->d_min;
				break;
			default:
				break;

		}

		// Calculate tax amount
		$total_imposed_tax_amount = 0;
		if ( $show_tax_incl ) {
			if ( count( $imposed_tax_types ) > 0 ) {
				foreach ( $imposed_tax_types as $taxType ) {
					$total_imposed_tax_amount += $min->price * $taxType->rate;
				}
			}
		}
		$min_currency = clone $solidres_currency;
		$min_currency->set_value($min->price + $total_imposed_tax_amount);
		if ($asset->booking_type == 0) {
			$tariff_suffix .= sprintf( _n( '/ %s night', '/ %s nights', $stay_length, 'solidres' ), $stay_length );
		} else {
			$tariff_suffix .= sprintf( _n( '/ %s day', '/ %s days', $stay_length, 'solidres' ), $stay_length );
		}

		return '<span class="starting_from">'.__( 'Starting from', 'solidres' ).'</span><span class="min_tariff">' . $min_currency->format() . '</span><span class="tariff_suffix">' . $tariff_suffix . '</span>';
	}

	/**
	 * Load the room type params in JSON format
	 *
	 * @param $params The params string
	 *
	 * @return array|mixed
	 *
	 */
	public function load_params( $params ) {
		return json_decode( (string) $params, true);
	}

	/**
	 * Get room type information to be display in the reservation confirmation screen
	 *
	 * This is intended to be used in the front end
	 *
	 * @return array $ret An array contain room type information
	 */
	public function get_room_type( $asset_id, $booked_room_types, $checkin, $checkout )
	{
		// Construct a simple array of room type ID and its price
		$roomTypePricesMapping = array();
		$options_plugin = get_option( 'solidres_plugin' );

		$solidres_room_type = new SR_Room_Type();
		$currencyId = solidres()->session->get ( 'sr_currency_id' );
		$taxId = solidres()->session->get( 'sr_tax_id' );
		$solidresCurrency = new SR_Currency(0, $currencyId);
		$coupon = solidres()->session->get( 'sr_coupon' );
		$bookingType = solidres()->session->get( 'sr_booking_type' );
		$reservationAssetId = solidres()->session->get( 'sr_asset_id' );
		$isDiscountPreTax = $options_plugin[ 'show_price_with_tax' ];
		$isEditing = solidres()->session->get( 'sr_is_editing', 0 );
		$isDepositRequired = solidres()->session->get( 'sr_deposit_required' );
		$depositByStayLength = solidres()->session->get( 'sr_deposit_by_stay_length' );

		// Get imposed taxes
		$imposedTaxTypes = array();
		if (!empty($taxId))
		{
			$taxModel = new SR_Tax();
			$imposedTaxTypes[] = $taxModel->load( $taxId );
		}

		// Get discount
		$discounts = array();
		if ( defined( 'SR_PLUGIN_DISCOUNT_ENABLED' ) && SR_PLUGIN_DISCOUNT_ENABLED ) {
			$solidres_discount = new SR_Discount();
			$discounts = $solidres_discount->load_discounts( $checkin, $checkout, array(0, 2, 3), 1, $asset_id);
		}

		// Get customer information
		$customerGroupId = NULL;  // Non-registered/Public/Non-loggedin customer
		if ( defined( 'SR_PLUGIN_USER_ENABLED' ) && SR_PLUGIN_USER_ENABLED && is_user_logged_in())
		{
			$current_user = wp_get_current_user();
			$customerGroupId = get_user_meta( $current_user->ID, 'customer_group_id', true );
			$customerGroupId = empty( $customerGroupId ) ? NULL : $customerGroupId ;
		}

		$coupon_is_valid = false;
		if (isset($coupon) && is_array($coupon))
		{
			$solidres_coupon = new SR_Coupon();
			$current_date = strtotime( 'now' );
			$checkin_to_check  = strtotime( $checkin );
			$coupon_is_valid = $solidres_coupon->is_valid($coupon['coupon_code'], $reservationAssetId, $current_date, $checkin_to_check, $customerGroupId);
		}

		$stayLength = (int) SR_Utilities::calculate_date_diff( $checkin, $checkout );
		if ($bookingType == 1) {
			$stayLength ++;
		}

		// Build the config values
		$tariffConfig = array(
			'booking_type' => $bookingType,
			'enable_single_supplement' => false,
			'child_room_cost_calc' => isset($options_tariff[ 'child_room_cost_calc' ]) ? $options_tariff[ 'child_room_cost_calc' ] : 1,
			'adjoining_tariffs_mode' => isset($options_tariff[ 'adjoining_tariffs_mode' ]) ? $options_tariff[ 'adjoining_tariffs_mode' ] : 0
		);

		$totalPriceTaxIncl = 0;
		$totalPriceTaxExcl = 0;
		$totalPriceTaxInclDiscounted = 0; // Include discounted
		$totalPriceTaxExclDiscounted = 0; // Include discounted
		$totalDiscount = 0;
		$totalReservedRoom = 0;
		$totalDepositByStayLength = 0;
		$totalSingleSupplement = 0;
		$ret = array();

		// Get a list of room type based on search conditions
		foreach ($booked_room_types as $roomTypeId => $bookedTariffs )
		{
			$bookedRoomTypeQuantity = 0;

			$r = $solidres_room_type->load( $roomTypeId );

			if (isset($r->params['enable_single_supplement'])
			    &&
			    $r->params['enable_single_supplement'] == 1)
			{
				$tariffConfig['enable_single_supplement'] = true;
				$tariffConfig['single_supplement_value'] = $r->params['single_supplement_value'];
				$tariffConfig['single_supplement_is_percent'] = $r->params['single_supplement_is_percent'];
			}
			else
			{
				$tariffConfig['enable_single_supplement'] = false;
			}

			foreach ($bookedTariffs as $tariffId => $roomTypeRoomDetails )
			{
				$bookedRoomTypeQuantity += count($roomTypeRoomDetails);

				$tariffConfig['adjoining_layer'] = abs($tariffId);

				$ret[$roomTypeId]['name'] = $r->name;
				$ret[$roomTypeId]['description'] = $r->description;
				$ret[$roomTypeId]['occupancy_adult'] = $r->occupancy_adult;
				$ret[$roomTypeId]['occupancy_child'] = $r->occupancy_child;

				// Some data to query the correct tariff
				foreach ($roomTypeRoomDetails as $roomIndex => $roomDetails)
				{
					if ( defined( 'SR_PLUGIN_COMPLEXTARIFF_ENABLED' ) && SR_PLUGIN_COMPLEXTARIFF_ENABLED)
					{
						$cost  = $solidres_room_type->getPrice(
							$roomTypeId,
							$customerGroupId,
							$imposedTaxTypes,
							false,
							true,
							$checkin,
							$checkout,
							$solidresCurrency,
							$coupon,
							$roomDetails['adults_number'],
							(isset($roomDetails['children_number']) ? $roomDetails['children_number'] : 0),
							(isset($roomDetails['children_ages']) ? $roomDetails['children_ages'] : array()),
							$stayLength,
							(isset($tariffId) && $tariffId > 0) ? $tariffId : NULL,
							$discounts,
							$isDiscountPreTax,
							$tariffConfig
						);
					}
					else
					{
						$cost = $solidres_room_type->getPrice(
							$roomTypeId,
							$customerGroupId,
							$imposedTaxTypes,
							true,
							false,
							$checkin,
							$checkout,
							$solidresCurrency,
							$coupon,
							$roomDetails['adults_number'],
							0,
							array(),
							$stayLength,
							$tariffId,
							$discounts,
							$isDiscountPreTax,
							$tariffConfig
						);
					}

					$ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency'] 	= $cost;
					$totalPriceTaxIncl += $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_tax_incl'];
					$totalPriceTaxExcl += $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_tax_excl'];
					$totalPriceTaxInclDiscounted += $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_tax_incl_discounted'];
					$totalPriceTaxExclDiscounted += $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_tax_excl_discounted'];
					$totalDiscount += $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_discount'];
					$totalSingleSupplement += $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_single_supplement'];

					$roomTypePricesMapping[$roomTypeId][$tariffId][$roomIndex] = array(
						'total_price' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price'],
						'total_price_tax_incl' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_tax_incl'],
						'total_price_tax_excl' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_tax_excl'],
						'total_price_discounted' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_discounted'],
						'total_price_tax_incl_discounted' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_tax_incl_discounted'],
						'total_price_tax_excl_discounted' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_price_tax_excl_discounted'],
						'total_discount' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_discount'],
						'total_discount_formatted' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_discount_formatted'],
						'tariff_break_down' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['tariff_break_down'],
						'total_single_supplement' => $ret[$roomTypeId]['rooms'][$tariffId][$roomIndex]['currency']['total_single_supplement']
					);

					if ($isDepositRequired && $depositByStayLength > 0)
					{
						for ($i = 0; $i < $depositByStayLength; $i++)
						{
							if (isset($roomTypePricesMapping[$roomTypeId][$tariffId][$roomIndex]['tariff_break_down'][$i]))
							{
								$mappedWDay = key($roomTypePricesMapping[$roomTypeId][$tariffId][$roomIndex]['tariff_break_down'][$i]);
								$totalDepositByStayLength += $roomTypePricesMapping[$roomTypeId][$tariffId][$roomIndex]['tariff_break_down'][$i][$mappedWDay]['gross']->getValue();
							}
						}
					}
				}

				// Calculate number of available rooms
				$ret[$roomTypeId]['totalAvailableRoom'] = count( $solidres_room_type->getListAvailableRoom($roomTypeId, $checkin, $checkout, $bookingType) );
				$ret[$roomTypeId]['quantity'] = $bookedRoomTypeQuantity;

				// Only allow quantity within quota
				if ( ! $isEditing ) {
					if ($bookedRoomTypeQuantity <= $ret[$roomTypeId]['totalAvailableRoom'])
					{
						$totalReservedRoom += $bookedRoomTypeQuantity;
					}
					else
					{
						return false;
					}
				}
			} // end room type loop
		}

		// Calculate discounts on number of booked rooms, need to take before and after tax into consideration
		$totalDiscountOnNumOfBookedRoom = 0;
		if ( defined( 'SR_PLUGIN_DISCOUNT_ENABLED' ) && SR_PLUGIN_DISCOUNT_ENABLED ) {
			// only query for Discount on number of booked rooms
			$solidres_discount = new SR_Discount();
			$discounts2 = $solidres_discount->load_discounts( $checkin, $checkout, array(1), 1, $reservationAssetId);

			$reservationData = array(
				'checkin' => $checkin,
				'checkout' => $checkout,
				'discount_pre_tax' => $isDiscountPreTax,
				'stay_length' => $stayLength,
				'scope' => 'asset',
				'scope_id' => $reservationAssetId,
				'total_reserved_room' => $totalReservedRoom,
				'total_price_tax_excl' => $totalPriceTaxExcl,
				'total_price_tax_incl' => $totalPriceTaxIncl,
				'booking_type' => $bookingType
			);

			$solidresDiscount = new SR_Discount_Process($discounts2, $reservationData);
			$solidresDiscount->calculate();
			$appliedDiscounts = $solidresDiscount->appliedDiscounts;
			$totalDiscountOnNumOfBookedRoom = $solidresDiscount->totalDiscount;
		}

		// End of discount calculation

		if ($totalDiscountOnNumOfBookedRoom > 0)
		{
			$totalDiscount += $totalDiscountOnNumOfBookedRoom;
		}

		$totalImposedTax = 0;
		foreach ($imposedTaxTypes as $taxType)
		{
			if ($isDiscountPreTax)
			{
				$imposedAmount = $taxType->rate * ($totalPriceTaxExcl - $totalDiscount);
			}
			else
			{
				$imposedAmount = $taxType->rate * ($totalPriceTaxExcl);
			}
			$totalImposedTax += $imposedAmount;
		}

		solidres()->session->set( 'sr_total_reserved_room', $totalReservedRoom );
		solidres()->session->set( 'sr_cost', array(
				'total_price' => $totalPriceTaxIncl,
				'total_price_tax_incl' => $totalPriceTaxIncl,
				'total_price_tax_excl' => $totalPriceTaxExcl,
				'total_price_tax_incl_discounted' => $totalPriceTaxInclDiscounted - $totalDiscountOnNumOfBookedRoom,
				'total_price_tax_excl_discounted' => $totalPriceTaxExclDiscounted - $totalDiscountOnNumOfBookedRoom,
				'total_discount' => $totalDiscount,
				'tax_amount'           => $totalImposedTax,
				'total_single_supplement' => $totalSingleSupplement
		) );

		solidres()->session->set( 'sr_room_type_prices_mapping', $roomTypePricesMapping );
		solidres()->session->set( 'sr_deposit_amount_by_stay_length', $totalDepositByStayLength );

		return $ret;
	}

	/**
	 * Check to see if the general checkin/out match this tariff's valid from and valid to
	 *  We also have to check if the checkin match the allowed checkin days (except standard tariff).
	 *  We also have to check if the general nights number match this tariff's min nights and max nights
	 *
	 *
	 * @param $tariffWithDetails
	 * @param $checkin
	 * @param $checkout
	 * @param $stayLength
	 * @param $checkinDayInfo
	 *
	 * @return bool
	 *
	 */
	private function isValid($tariffWithDetails, $checkin, $checkout, $stayLength, $checkinDayInfo)
	{
		$isValid = false;

		// We have different conditions for standard tariff and complex tariff
		if ($tariffWithDetails->valid_from == '00-00-0000' && $tariffWithDetails->valid_to == '00-00-0000')
		{
			$isValid = true;
		}
		else
		{
			$isValidDayRange = true;

			// First case: this tariff has value for d_min and d_max
			if ($tariffWithDetails->d_min > 0 && $tariffWithDetails->d_max > 0)
			{
				$isValidDayRange = $stayLength >= $tariffWithDetails->d_min && $stayLength <= $tariffWithDetails->d_max;
			}
			elseif ( empty($tariffWithDetails->d_min) && $tariffWithDetails->d_max > 0)
			{
				$isValidDayRange = $stayLength <= $tariffWithDetails->d_max;
			}
			elseif ($tariffWithDetails->d_min > 0 && empty($tariffWithDetails->d_max))
			{
				$isValidDayRange = $stayLength >= $tariffWithDetails->d_min;
			}

			if (
				strtotime($tariffWithDetails->valid_from) <= strtotime($checkin) &&
				strtotime($tariffWithDetails->valid_to)  >= strtotime($checkout) &&
				( in_array($checkinDayInfo['wday'], $tariffWithDetails->limit_checkin)) &&
				$isValidDayRange
			)
			{
				$isValid = true;
			}
		}

		return $isValid;
	}

	public function build_tariff_query($query_params = array()) {
		global $wpdb;
		$tariff_query = "SELECT t.* FROM {$wpdb->prefix}sr_tariffs as t";
		// $tariff_query .= " LEFT JOIN {$wpdb->prefix}sr_customer_groups AS cgroup ON cgroup.id = t.customer_group_id";
		$tariff_query .= " WHERE t.room_type_id = " . (int) $query_params[ 'room_type_id' ] . " AND t.state = 1";

		if ( $query_params[ 'date_constraint' ] ) {
			$tariff_query .= ' AND t.valid_from <= \'' . $query_params[ 'bookday' ] . '\'';
			$tariff_query .= ' AND t.valid_to >= \'' . $query_params[ 'bookday' ] . '\'';
		}

		if ( $query_params[ 'default_tariff' ]) {
			$tariff_query .= " AND t.valid_from = '0000-00-00' AND t.valid_to = '0000-00-00'";
		} else {
			$tariff_query .= " AND t.valid_from != '0000-00-00' AND t.valid_to != '0000-00-00'";
		}

		if ( $query_params[ 'customer_group_id' ] != -1) {
			$tariff_query .= ' AND t.customer_group_id ' . ($query_params[ 'customer_group_id' ] === NULL ? 'IS NULL' : '= ' .(int) $query_params[ 'customer_group_id' ]);
		}

		if ($query_params[ 'partial_match' ] && !empty($query_params[ 'checkin' ]) && !empty($query_params[ 'checkout' ] )) {
			$tariff_query .= " AND t.id NOT IN
		        (SELECT id FROM {$wpdb->prefix}sr_tariffs as t1
		        WHERE t1.valid_from <= '". $query_params[ 'checkin' ] ."' AND t1.valid_to >= '" . $query_params[ 'checkout' ] . "' AND t1.room_type_id = " . (int) $query_params[ 'room_type_id' ] . ")";
		}


		return $tariff_query;
	}

	/**
	 * Retrieve booking type from room type id
	 *
	 * @param $room_type_id
	 *
	 * @return mixed
	 */
	public function get_booking_type($room_type_id)
	{
		return $this->wpdb->get_var(
			$this->wpdb->prepare( "SELECT booking_type FROM {$this->wpdb->prefix}sr_reservation_assets 
										WHERE id = (SELECT reservation_asset_id FROM {$this->wpdb->prefix}sr_room_types WHERE id = %d)", $room_type_id )
		);
	}
}