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
 * Reservation Asset handler class
 * @package 	Solidres
 * @subpackage	Reservation Asset
 * @since 		0.1.0
 */
class SR_Asset {
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
	 * @param $asset_id
	 * @param $ids
	 */
	public function update_states( $action, $asset_id, $ids ) {
		$states = array(
			'draft' => array( 'state' => 0, 'action' => 'moved', 'title' => 'Draft' ),
			'publish' => array( 'state' => 1, 'action' => 'moved', 'title' => 'Publish' ),
			'trash' => array( 'state' => -2, 'action' => 'moved', 'title' => 'Trash' ),
			'untrash' => array( 'state' => 0, 'action' => 'restored', 'title' => 'Trash' ),
		);

		if ( isset( $action ) && array_key_exists( $action, $states ) &&  isset( $asset_id ) && $asset_id != null ) {
			foreach ( $ids as $id ) {
				$this->wpdb->update($this->wpdb->prefix . 'sr_reservation_assets', array('state' => $states[$action]['state'] ), array( 'id' => $id ) );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 asset ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			} else {
				$message = __( count( $ids ).' assets ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
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

		$count_room_type = $this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_room_types WHERE reservation_asset_id = $id" );

		if ( $count_room_type > 0 ) {
			return false;
		}

		// Take care of Reservation
		$this->wpdb->update( $this->wpdb->prefix.'sr_reservations', array( 'reservation_asset_id' => NULL ), array( 'reservation_asset_id' => $id ) );

		// Take care of media, if it has any, remove all of them
		$this->wpdb->delete( $this->wpdb->prefix.'sr_media_reservation_assets_xref', array( 'reservation_asset_id' => $id ) );

		// Take care of Extra
		$solidres_extra = new SR_Extra();
		$extras = $this->wpdb->get_results( 'SELECT id FROM ' . $this->wpdb->prefix . 'sr_extras WHERE reservation_asset_id = ' . $id);
		foreach ($extras as $extra) {
			$solidres_extra->delete( $extra->id );
		}

		// Take care of Coupon
		$solidres_coupon = new SR_Coupon();
		$coupons = $this->wpdb->get_results( 'SELECT id FROM ' . $this->wpdb->prefix . 'sr_coupons WHERE reservation_asset_id = ' . $id);
		foreach ($coupons as $coupon) {
			$solidres_coupon->delete( $coupon->id );
		}

		// Take care of Custom Fields
		$this->wpdb->delete( $this->wpdb->prefix.'sr_reservation_asset_fields', array( 'reservation_asset_id' => $id ) );

		// Take care of hub theme and hub facilities
		if ( defined( 'SR_PLUGIN_HUB_ENABLED' ) && SR_PLUGIN_HUB_ENABLED) {
			// Take care of Themes
			$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_asset_theme_xref', array( 'reservation_asset_id' => $id ) );

			// Take care of Facilities
			$this->wpdb->delete( $this->wpdb->prefix . 'sr_facility_reservation_asset_xref', array( 'reservation_asset_id' => $id ) );
		}

		// Take care of Limit Booking
		if ( defined( 'SR_PLUGIN_LIMITBOOKING_ENABLED' ) && SR_PLUGIN_LIMITBOOKING_ENABLED) {
			$solidres_limitbooking = new SR_Limit_Booking();
			$limitbookings = $this->wpdb->get_results( 'SELECT id FROM ' . $this->wpdb->prefix . 'sr_limit_bookings WHERE reservation_asset_id = ' . $id);
			foreach ($limitbookings as $limitbooking) {
				$solidres_limitbooking->delete( $limitbooking->id );
			}
		}

		$result = $this->wpdb->delete( $this->wpdb->prefix.'sr_reservation_assets', array( 'id' => $id ) );

		do_action('sr_after_delete', array($id), $result, 'solidres.delete.asset');
	}

	/**
	 * Get a single asset by id
	 *
	 * @param $id
	 * @param $partner_id
	 * @param $output
	 *
	 * @return mixed
	 */
	public function load( $id = 0, $partner_id = 0, $output = OBJECT ) {
		if ( $partner_id > 0 ){
			$result = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservation_assets WHERE id = %d AND partner_id = %d", $id, $partner_id ), $output );
		} else {
			$result = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservation_assets WHERE id = %d", $id ), $output );
		}
		return $result;
	}

	public function load_all( $state = 1, $partner_id = 0, $output = OBJECT ) {
		if ( $partner_id > 0 ){
			$result = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservation_assets WHERE state = %d AND partner_id = %d", $state, $partner_id ), $output );
		} else {
			$result = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservation_assets WHERE state = %d", $state ), $output );
		}
		return $result;
	}

	public function count_by_alias( $alias, $output = OBJECT ) {
		$result = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT count(*) FROM {$this->wpdb->prefix}sr_reservation_assets WHERE alias = %s", $alias ) );

		return $result;
	}

	/**
	 * Get a single asset by default
	 *
	 * @param $default
	 *
	 * @return mixed
	 */
	public function load_by_default( $default ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservation_assets WHERE `default` = %d", $default ) );
	}

	public function load_params( $params ) {
		return json_decode( $params, true );
	}

	/**
	 * Get a single asset by reservation id
	 *
	 * @param $reservation_id
	 *
	 * @return mixed
	 */
	/*public function load_by_reservation_id( $reservation_id ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservation_assets WHERE re = %s", $alias ) );
	}*/
	/**
	 * Get a single asset by alias (slug)
	 *
	 * @param $alias
	 *
	 * @return mixed
	 */
	public function load_by_alias( $alias ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservation_assets WHERE alias = %s", $alias ) );
	}

	/**
	 * Get the availability calendar
	 *
	 * The number of months to be displayed in configured in component's options
	 *
	 * @param $roomtypeid
	 *
	 * @return string
	 */
	public function get_availability_calendar( $roomtypeid ) {
		$weekStartDay = ( get_option( 'start_of_week', 1 ) == 1 ) ? 'monday' : 'sunday' ;
		$html = '';
		$html .= '<span class="legend-busy"></span> '.__( 'Not available', 'solidres' );
		$options = get_option( 'solidres_plugin' );
		$period = empty( $options['availability_calendar_month_number'] ) ? 6 : $options['availability_calendar_month_number'];
		$calendar = new SR_Calendar( array( 'start_day' => $weekStartDay ) );
		for ( $i = 0; $i < $period; $i++ ) {
			if ( $i % 3 == 0 && $i == 0 ) {
				$html .= '<div class="' . SR_UI_GRID_CONTAINER . '">';
			}
			else if ( $i % 3 == 0 ) {
				$html .= '</div><div class="' . SR_UI_GRID_CONTAINER . '">';
			}
			$year = date( 'Y', strtotime( 'first day of this month +' . $i . ' month' ) );
			$month = date( 'n', strtotime( 'first day of this month +' . $i . ' month' ) );
			$html .= '<div class="' . SR_UI_GRID_COL_4 . '">' . $calendar->generate( $year, $month, $roomtypeid ) . '</div>';
		}
		return $html;
	}

	/**
	 * Get check in out form
	 *
	 * @param $tariff_id
	 * @param $roomtypeId
	 * @param $assetId
	 * @param $itemId
	 *
	 * @return string
	 */
	public function get_check_in_out_form($tariff_id, $roomtypeId, $assetId, $itemId ) {
		$solidresConfig = get_option( 'solidres_plugin' );
		$solidres_tariff = new SR_Tariff();
		$tariff = $solidres_tariff->load( $tariff_id );

		$tzoffset                              = get_option( 'timezone_string' );
		$tzoffset                              = $tzoffset == '' ? 'UTC' : $tzoffset;
		$timezone                              = new DateTimeZone( $tzoffset );
		$checkin                               = solidres()->session->get( 'sr_checkin' );
		$checkout                              = solidres()->session->get( 'sr_checkout' );
		$datePickerMonthNum                    = empty( $solidresConfig['datepicker_month_number'] ) ? 3 : $solidresConfig['datepicker_month_number'];
		$weekStartDay                          = empty( $solidresConfig['week_start_day'] ) ? 1 : $solidresConfig['week_start_day'];
		$currentSelectedTariffs                = solidres()->session->get( 'current_selected_tariffs' );
		$currentSelectedTariffs[$roomtypeId][] = $tariff_id;
		$dateFormat                            = get_option( 'date_format',  'd-m-Y'  );
		$jsDateFormat                          = SR_Utilities::convert_date_format_pattern( $dateFormat );
		$bookingType                          = solidres()->session->get( 'sr_booking_type' );

		$display_data = array(
			'tariff' => $tariff,
			'assetId' => $assetId,
			'roomTypeId' => $roomtypeId,
			'checkin' => $checkin,
			'checkout' => $checkout,
			'minDaysBookInAdvance' => isset( $solidresConfig['min_days_book_in_advance'] ) ? $solidresConfig['min_days_book_in_advance'] : 0,
			'maxDaysBookInAdvance' => isset( $solidresConfig['max_days_book_in_advance'] ) ? $solidresConfig['max_days_book_in_advance'] : 0,
			'minLengthOfStay' => isset( $solidresConfig['min_length_of_stay'] ) ? $solidresConfig['min_length_of_stay'] : 1,
			'timezone' => $timezone,
			'itemId' => $itemId,
			'datePickerMonthNum' => $datePickerMonthNum,
			'weekStartDay' => $weekStartDay,
			'dateFormat' => $dateFormat, // default format d-m-y
			'jsDateFormat' => $jsDateFormat,
			'bookingType' => $bookingType
		);


		$html = '';
		$path = WP_PLUGIN_DIR . '/solidres/templates/reservation/checkinoutform.php';
		if ( file_exists( $path ) ) {
			ob_start();
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}

		return $html;
	}

	/**
	 * Get the html output according to the room type quantity selection
	 *
	 * This output contains room specific form like adults and children's quantity (including children's ages) as well
	 * as some other information like room preferences like smoking and room's extra items
	 *
	 * @param $asset_id
	 * @param $room_type_id
	 * @param $tariff_id
	 * @param $quantity
	 *
	 * @return string
	 */
	public function get_room_type_form( $asset_id, $room_type_id, $tariff_id, $quantity, $adjoining_layer )
	{
		$solidres_options = get_option( 'solidres_plugin' );
		$show_price_with_tax = $solidres_options['show_price_with_tax'];
		$solidres_extra = new SR_Extra;
		$extras = $solidres_extra->load_by_room_type_id( $room_type_id, 1, $show_price_with_tax );
		$solidres_room_type = new SR_Room_Type();
		$room_type = $solidres_room_type->load( $room_type_id );
		$reservation_details_room = solidres()->session->get( 'sr_room' );
		$child_max_age = $solidres_options['child_max_age_limit'];
		$solidres_tariff = new SR_Tariff();
		$tariff = $solidres_tariff->load($tariff_id);

		$display_data = array(
			'assetId' => $asset_id,
			'roomTypeId' => $room_type_id,
			'tariffId' => $tariff_id,
			'quantity' => $quantity,
			'roomType' => $room_type,
			'reservation_details_room' => $reservation_details_room,
			'extras' => $extras,
			'childMaxAge' => $child_max_age,
			'tariff' => $tariff,
			'adjoiningLayer' => $adjoining_layer
		);

		$html = '';
		$path = WP_PLUGIN_DIR . '/solidres/templates/reservation/roomtypeform.php';
		if ( file_exists( $path ) ) {
			ob_start();
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}

		return $html;
	}

	/**
	 * Calculate tariff
	 *
	 * @param $data
	 * @return array
	 */
	public function calculate_tariff( $data ) {
		$solidres_currency  = new SR_Currency( 0, $data['currency_id'] );
		$solidres_room_type = new SR_Room_Type();
		$options_tariff = get_option( 'solidres_tariff' );
		$day_mapping = array(
			'0' => __( 'Sun', 'solidres' ),
			'1' => __( 'Mon', 'solidres' ),
			'2' => __( 'Tue', 'solidres' ),
			'3' => __( 'Wed', 'solidres' ),
			'4' => __( 'Thu', 'solidres' ),
			'5' => __( 'Fri', 'solidres' ),
			'6' => __( 'Sat', 'solidres' )
		);
		$imposed_tax_types = array();
		if ( ! empty( $data['tax_id'] ) ) {
			$solidres_tax = new SR_Tax;
			$imposed_tax_types[] = $solidres_tax->load( $data['tax_id'] );
		}

		// Get discount
		$discounts = array();
		if (defined( 'SR_PLUGIN_DISCOUNT_ENABLED' ) && SR_PLUGIN_DISCOUNT_ENABLED) {
			$solidres_discount = new SR_Discount();
			$discounts = $solidres_discount->load_discounts( $data[ 'checkin' ], $data[ 'checkout' ], array(0, 2, 3), 1, $data['asset_id']);
		}

		$coupon_is_valid = false;
		if (isset($data[ 'coupon' ]) && is_array($data[ 'coupon' ]))
		{
			$solidres_coupon = new SR_Coupon();
			$current_date = strtotime( 'now' );
			$checkin_to_check  = strtotime( $data['checkin'] );
			$current_user = wp_get_current_user();
			$customer_group_id = $solidres_coupon->get_customer_group_id( $current_user->ID );
			$coupon_is_valid = $solidres_coupon->is_valid($data[ 'coupon' ]['coupon_code'], $data[ 'asset_id' ], $current_date, $checkin_to_check, $customer_group_id);
		}

		$stay_length = (int) SR_Utilities::calculate_date_diff( $data['checkin'], $data['checkout'] );
		$booking_type = solidres()->session->get( 'sr_booking_type' );
		if ($booking_type == 1) {
			$stay_length ++;
		}

		// Build the config values
		$tariff_config = array(
			'booking_type' => $booking_type,
			'adjoining_tariffs_mode' => isset($options_tariff[ 'adjoining_tariffs_mode' ]) ? $options_tariff[ 'adjoining_tariffs_mode' ] : 0,
			'child_room_cost_calc' => isset($options_tariff[ 'child_room_cost_calc' ]) ? $options_tariff[ 'child_room_cost_calc' ] : 1,
			'adjoining_layer' => $data[ 'adjoining_layer' ]
		);

		// Calculate single supplement
		$room_type = $solidres_room_type->load( $data['room_type_id'] );
		if (isset($room_type->params['enable_single_supplement'])
		    &&
		    $room_type->params['enable_single_supplement'] == 1)
		{
			$tariff_config['enable_single_supplement'] = true;
			$tariff_config['single_supplement_value'] = $room_type->params['single_supplement_value'];
			$tariff_config['single_supplement_is_percent'] = $room_type->params['single_supplement_is_percent'];
		}
		else
		{
			$tariff_config['enable_single_supplement'] = false;
		}

		$child_ages = array();
		for ( $i = 0; $i < $data['child_number']; $i ++ ) {
			$child_ages[] = $_GET[ 'child_age_' . $data['room_type_id'] . '_' . $data['tariff_id'] . '_' . $data['room_index'] . '_' . $i ];
		}

		// Search for complex tariff first, if no complex tariff found, we will search for Standard Tariff
		if ( defined( 'SR_PLUGIN_COMPLEXTARIFF_ENABLED' ) && SR_PLUGIN_COMPLEXTARIFF_ENABLED ) {
			$tariff = $solidres_room_type->getPrice( $data['room_type_id'], $data['customer_group_id'], $imposed_tax_types, false, true, $data['checkin'], $data['checkout'], $solidres_currency, $coupon_is_valid ? $data['coupon'] : NULL, $data['adult_number'], $data['child_number'], isset($child_ages) ? $child_ages : array(), $stay_length, ( isset( $data['tariff_id'] ) && $data['tariff_id'] > 0 ? $data['tariff_id'] : null ), $discounts, $data[ 'discount_pre_tax' ], $tariff_config );
		} else {
			$tariff = $solidres_room_type->getPrice( $data['room_type_id'], $data['customer_group_id'], $imposed_tax_types, true, false, $data['checkin'], $data['checkout'], $solidres_currency, $coupon_is_valid ? $data['coupon'] : NULL, $data['adult_number'], 0, array(), $stay_length, $data['tariff_id'], $discounts, $data[ 'discount_pre_tax' ], $tariff_config );
		}

		$shown_tariff = $tariff['total_price_tax_excl_discounted_formatted'];
		$shownTariffBeforeDiscounted = $tariff['total_price_tax_excl_formatted'];
		if ($data['show_price_with_tax'])
		{
			$shown_tariff = $tariff['total_price_tax_incl_discounted_formatted'];
			$shownTariffBeforeDiscounted = $tariff['total_price_tax_incl_formatted'];
		}

		// Prepare tariff break down, since JSON is not able to handle PHP object correctly, we should prepare a simple array
		$tariffBreakDown = array();
		$tariffBreakDownHtml = '';
		if ($tariff['type'] == 0)
		{
			$tariffBreakDown = array();
			$tariffBreakDownHtml = '';
			$tempKeyWeekDay = NULL;
			$totalBreakDown = count($tariff['tariff_break_down']);
			for ($key = 0; $key <= $totalBreakDown; $key ++)
			{
				if ($key % 6 == 0 && $key == 0) :
					$tariffBreakDownHtml .= '<div class="' . SR_UI_GRID_CONTAINER . ' sr_row breakdown-row">';
				elseif ($key % 6 == 0 && $key != $totalBreakDown) :
					$tariffBreakDownHtml .= '</div><div class="' . SR_UI_GRID_CONTAINER . ' sr_row breakdown-row">';
				elseif ($key == $totalBreakDown) :
					$tariffBreakDownHtml .= '</div>';
				endif;

				if ($key < $totalBreakDown) {
					$priceOfDayDetails = $tariff['tariff_break_down'][$key];
					$tempKeyWeekDay = key($priceOfDayDetails);
					$tariffBreakDownHtml .= '<div class="' . SR_UI_GRID_COL_2 .' two columns"><p class="breakdown-wday">'.
					                        $day_mapping[$tempKeyWeekDay].
					                        '</p><span class="'.$data['tariff_breakdown_net_or_gross'].'">'.
					                        $priceOfDayDetails[$tempKeyWeekDay][$data['tariff_breakdown_net_or_gross']]->format().
					                        '</span></div>';
					$tariffBreakDown[][$tempKeyWeekDay] = array('wday' => $tempKeyWeekDay, 'priceOfDay' => $priceOfDayDetails[$tempKeyWeekDay]['gross']->format());
				}
			}

			$tariffBreakDownHtml .= '<table class="table table-bordered">';
			$tariffBreakDownHtml .= '<tr>';
			$tariffBreakDownHtml .= '<td>' . __( 'Room cost', 'solidres' ) . '</td><td class="sr-align-right"> ';
			if ($tariff['total_single_supplement'] > 0)
			{
				$shownTariffBeforeDiscounted->set_value($shownTariffBeforeDiscounted->getValue() - $tariff['total_single_supplement'] );
				$tariffBreakDownHtml .= $shownTariffBeforeDiscounted->format() ;
			}
			else
			{
				$tariffBreakDownHtml .= $shownTariffBeforeDiscounted->format() ;
			}
			$tariffBreakDownHtml .= '</td>';
			$tariffBreakDownHtml .= '</tr>';

			if ($tariff['total_single_supplement'] > 0)
			{
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( 'Room single supplement', 'solidres' ) . '</td><td class="sr-align-right">' . $tariff['total_single_supplement_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
			}

			if ($tariff['total_discount'] > 0)
			{
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( "Room discounted amount", "solidres" ) . '</td><td class="sr-align-right">' . $tariff['total_discount_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( "Room cost after discounted", "solidres" ) . '</td><td class="sr-align-right">' . $tariff['total_price_tax_'.($data['show_price_with_tax'] == 1 ? 'incl' : 'excl' ).'_discounted_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
			}


			$tariffBreakDownHtml .= '</table>';
		}
		else if ($tariff['type'] == 1)
		{
			$tariffBreakDown = array();
			$tariffBreakDownHtml = '';
			$tempKeyWeekDay = NULL;
			$totalBreakDown = count($tariff['tariff_break_down']);
			for ($key = 0; $key <= $totalBreakDown; $key ++) {
				if ($key % 6 == 0 && $key == 0) :
					$tariffBreakDownHtml .= '<div class="' . SR_UI_GRID_CONTAINER . ' sr_row breakdown-row">';
				elseif ($key % 6 == 0 && $key != $totalBreakDown) :
					$tariffBreakDownHtml .= '</div><div class="' . SR_UI_GRID_CONTAINER . ' sr_row breakdown-row">';
				elseif ($key == $totalBreakDown) :
					$tariffBreakDownHtml .= '</div>';
				endif;

				if ($key < $totalBreakDown)
				{
					$priceOfDayDetails = $tariff['tariff_break_down'][$key];
					$tempKeyWeekDay = key($priceOfDayDetails);
					$tariffBreakDownHtml .= '<div class="' . SR_UI_GRID_COL_2 . ' two columns"><p class="breakdown-wday">'
					                        .$day_mapping[$tempKeyWeekDay].
					                        '</p>
										<p class="breakdown-adult">' . __( 'Adult', 'solidres' ). '</p>
										<span class="'.$data['tariff_breakdown_net_or_gross'].'">'.$priceOfDayDetails[$tempKeyWeekDay][$data['tariff_breakdown_net_or_gross'] . '_adults']->format().'</span>
										<p class="breakdown-child">' . __( 'Child', 'solidres' ). '</p>
										<span class="'.$data['tariff_breakdown_net_or_gross'].'">'.$priceOfDayDetails[$tempKeyWeekDay][$data['tariff_breakdown_net_or_gross'] . '_children']->format().'</span></div>';
											$tariffBreakDown[][$tempKeyWeekDay] = array('wday' => $tempKeyWeekDay, 'priceOfDay' => $priceOfDayDetails[$tempKeyWeekDay]['gross']->format());
				}

			}

			$tariffBreakDownHtml .= '<table class="table table-bordered">';
			$tariffBreakDownHtml .= '<tr>';
			$tariffBreakDownHtml .= '<td>' . __( 'Room cost', 'solidres' ) . '</td><td class="sr-align-right"> ';
			if ($tariff['total_single_supplement'] > 0)
			{
				$shownTariffBeforeDiscounted->set_value($shownTariffBeforeDiscounted->getValue() - $tariff['total_single_supplement'] );
				$tariffBreakDownHtml .= $shownTariffBeforeDiscounted->format() ;
			}
			else
			{
				$tariffBreakDownHtml .= $shownTariffBeforeDiscounted->format() ;
			}
			$tariffBreakDownHtml .= '</td>';
			$tariffBreakDownHtml .= '</tr>';

			if ($tariff['total_single_supplement'] > 0)
			{
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( 'Room single supplement', 'solidres' ) . '</td><td class="sr-align-right">' . $tariff['total_single_supplement_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
			}

			if ($tariff['total_discount'] > 0)
			{
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( "Room discounted amount", "solidres" ) . '</td><td class="sr-align-right">' . $tariff['total_discount_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( "Room cost after discounted", "solidres" ) . '</td><td class="sr-align-right">' . $tariff['total_price_tax_'.($data['show_price_with_tax'] == 1 ? 'incl' : 'excl' ).'_discounted_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
			}
			$tariffBreakDownHtml .= '</table>';
		}
		else if ($tariff['type'] == 2)
		{
			$tariffBreakDown = array();
			$tariffBreakDownHtml = '';
			$tempKeyWeekDay = NULL;
			$totalBreakDown = count($tariff['tariff_break_down']);
			for ($key = 0; $key <= $totalBreakDown; $key ++)
			{
				if ($key % 6 == 0 && $key == 0) :
					$tariffBreakDownHtml .= '<div class="' . SR_UI_GRID_CONTAINER . ' sr_row breakdown-row">';
				elseif ($key % 6 == 0 && $key != $totalBreakDown) :
					$tariffBreakDownHtml .= '</div><div class="' . SR_UI_GRID_CONTAINER . ' sr_row breakdown-row">';
				elseif ($key == $totalBreakDown) :
					$tariffBreakDownHtml .= '</div>';
				endif;

				if ($key < $totalBreakDown)
				{
					$priceOfDayDetails = $tariff['tariff_break_down'][$key];
					$tempKeyWeekDay = key($priceOfDayDetails);
					$tariffBreakDownHtml .= '<div class="' . SR_UI_GRID_COL_2 . ' two columns"><p class="breakdown-wday">'.
					                        ( isset($day_mapping[$tempKeyWeekDay]) ? $day_mapping[$tempKeyWeekDay] : '').
					                        '</p><span class="'.$data['tariff_breakdown_net_or_gross'].'">'.
					                        $priceOfDayDetails[$tempKeyWeekDay][$data['tariff_breakdown_net_or_gross']]->format().
					                        '</span></div>';
					$tariffBreakDown[][$tempKeyWeekDay] = array('wday' => $tempKeyWeekDay, 'priceOfDay' => $priceOfDayDetails[$tempKeyWeekDay]['gross']->format());
				}
			}

			$tariffBreakDownHtml .= '<table class="table table-bordered">';
			$tariffBreakDownHtml .= '<tr>';
			$tariffBreakDownHtml .= '<td>' . __( 'Room cost', 'solidres' ) . '</td><td class="sr-align-right"> ';
			if ($tariff['total_single_supplement'] > 0)
			{
				$shownTariffBeforeDiscounted->set_value($shownTariffBeforeDiscounted->getValue() - $tariff['total_single_supplement'] );
				$tariffBreakDownHtml .= $shownTariffBeforeDiscounted->format() ;
			}
			else
			{
				$tariffBreakDownHtml .= $shownTariffBeforeDiscounted->format() ;
			}
			$tariffBreakDownHtml .= '</td>';
			$tariffBreakDownHtml .= '</tr>';

			if ($tariff['total_single_supplement'] > 0)
			{
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( 'Room single supplement', 'solidres' ) . '</td><td class="sr-align-right">' . $tariff['total_single_supplement_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
			}

			if ($tariff['total_discount'] > 0)
			{
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( "Room discounted amount", "solidres" ) . '</td><td class="sr-align-right">' . $tariff['total_discount_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( "Room cost after discounted", "solidres" ) . '</td><td class="sr-align-right">' . $tariff['total_price_tax_'.($data['show_price_with_tax'] == 1 ? 'incl' : 'excl' ).'_discounted_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
			}
			$tariffBreakDownHtml .= '</table>';
		}
		else if ($tariff['type'] == 3)
		{
			$tariffBreakDown = array();
			$tariffBreakDownHtml = '';
			$tempKeyWeekDay = NULL;
			$totalBreakDown = count($tariff['tariff_break_down']);
			for ($key = 0; $key <= $totalBreakDown; $key ++)
			{
				if ($key % 6 == 0 && $key == 0) :
					$tariffBreakDownHtml .= '<div class="' . SR_UI_GRID_CONTAINER . ' sr_row breakdown-row">';
				elseif ($key % 6 == 0 && $key != $totalBreakDown) :
					$tariffBreakDownHtml .= '</div><div class="' . SR_UI_GRID_CONTAINER . ' sr_row breakdown-row">';
				elseif ($key == $totalBreakDown) :
					$tariffBreakDownHtml .= '</div>';
				endif;

				if ($key < $totalBreakDown)
				{
					$priceOfDayDetails = $tariff['tariff_break_down'][$key];
					$tempKeyWeekDay = key($priceOfDayDetails);
					$tariffBreakDownHtml .= '<div class="' . SR_UI_GRID_COL_2 . ' two columns"><p class="breakdown-wday">'
					                        .(isset($day_mapping[$tempKeyWeekDay]) ? $day_mapping[$tempKeyWeekDay] : '').
					                        '</p>
							<p class="breakdown-adult">' . __( 'Adult', 'solidres' ). '</p>
							<span class="'.$data['tariff_breakdown_net_or_gross'].'">'.$priceOfDayDetails[$tempKeyWeekDay][$data['tariff_breakdown_net_or_gross'] . '_adults']->format().'</span>
							<p class="breakdown-child">' . __( 'Child', 'solidres' ). '</p>
							<span class="'.$data['tariff_breakdown_net_or_gross'].'">'.$priceOfDayDetails[$tempKeyWeekDay][$data['tariff_breakdown_net_or_gross'] . '_children']->format().'</span></div>';
								$tariffBreakDown[][$tempKeyWeekDay] = array('wday' => $tempKeyWeekDay, 'priceOfDay' => $priceOfDayDetails[$tempKeyWeekDay]['gross']->format());
				}
			}

			$tariffBreakDownHtml .= '<table class="table table-bordered">';
			$tariffBreakDownHtml .= '<tr>';
			$tariffBreakDownHtml .= '<td>' . __( 'Room cost', 'solidres' ) . '</td><td class="sr-align-right"> ';
			if ($tariff['total_single_supplement'] > 0)
			{
				$shownTariffBeforeDiscounted->set_value($shownTariffBeforeDiscounted->getValue() - $tariff['total_single_supplement'] );
				$tariffBreakDownHtml .= $shownTariffBeforeDiscounted->format() ;
			}
			else
			{
				$tariffBreakDownHtml .= $shownTariffBeforeDiscounted->format() ;
			}
			$tariffBreakDownHtml .= '</td>';
			$tariffBreakDownHtml .= '</tr>';

			if ($tariff['total_single_supplement'] > 0)
			{
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( 'Room single supplement', 'solidres' ) . '</td><td class="sr-align-right">' . $tariff['total_single_supplement_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
			}

			if ($tariff['total_discount'] > 0)
			{
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( "Room discounted amount", "solidres" ) . '</td><td class="sr-align-right">' . $tariff['total_discount_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
				$tariffBreakDownHtml .= '<tr>';
				$tariffBreakDownHtml .= '<td>' . __( "Room cost after discounted", "solidres" ) . '</td><td class="sr-align-right">' . $tariff['total_price_tax_'.($data['show_price_with_tax'] == 1 ? 'incl' : 'excl' ).'_discounted_formatted']->format() . '</td>';
				$tariffBreakDownHtml .= '</tr>';
			}
			$tariffBreakDownHtml .= '</table>';
		}

		return array(
			'room_index' => $data['room_index'],
			'room_index_tariff' => array(
				'id'        => ! empty( $shown_tariff ) ? $shown_tariff->getId() : null,
				'activeId'  => ! empty( $shown_tariff ) ? $shown_tariff->getActiveId() : null,
				'code'      => ! empty( $shown_tariff ) ? $shown_tariff->getCode() : null,
				'sign'      => ! empty( $shown_tariff ) ? $shown_tariff->getSign() : null,
				'name'      => ! empty( $shown_tariff ) ? $shown_tariff->getName() : null,
				'rate'      => ! empty( $shown_tariff ) ? $shown_tariff->getRate() : null,
				'value'     => ! empty( $shown_tariff ) ? $shown_tariff->getValue() : null,
				'formatted' => ! empty( $shown_tariff ) ? $shown_tariff->format() : null
			),
			'room_index_tariff_breakdown' => $tariffBreakDown,
			'room_index_tariff_breakdown_html' => $tariffBreakDownHtml,
		);
	}


	/**
	 * Prepares the document like adding meta tags/site name per ReservationAsset
	 * @return void
	 */
	protected function _prepareDocument() {
		if ( $this->item->name ) {
			$this->document->setTitle( $this->item->name );
		}

		if ( $this->item->metadesc ) {
			$this->document->setDescription( $this->item->metadesc );
		}

		if ($this->item->metakey) {
			$this->document->setMetadata( 'keywords', $this->item->metakey );
		}

		if ( $this->item->metadata ) {
			foreach ( $this->item->metadata as $k => $v ) {
				if ( $v ) {
					$this->document->setMetadata( $k, $v );
				}
			}
		}
	}

	/**
	 * Load all asset's custom fields
	 *
	 * @param $id
	 *
	 * @return string
	 *
	 */
	public function load_custom_fields( $id = 0 ) {
		return new SR_Custom_Field( array( 'id' => (int) $id, 'type' => 'asset' ) );
	}
}