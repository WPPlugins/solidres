<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if (!defined('ABSPATH'))
{
	exit;
}

if ( $asset->state != 1 ) :
	return;
endif;

$options_plugin = get_option('solidres_plugin');
$options_tariff = get_option('solidres_tariff');
$options_hub    = get_option('solidres_hub');

$currency             = $solidres_currency->load($asset->currency_id);
$asset_params         = $solidres_asset->load_params($asset->params);

solidres()->session->set( 'sr_asset_params', $asset_params );

if (defined( 'SR_PLUGIN_LOADMODULE_ENABLED' ) && SR_PLUGIN_LOADMODULE_ENABLED ) :
	$numberViewedAssets = isset($options_hub['number_viewed_assets']) ? $options_hub['number_viewed_assets'] : 5;
	$viewedAssets       = array();
	$count              = count($viewedAssets);
	if (!array_key_exists($asset->id, $viewedAssets)) {
		if ($count >= $numberViewedAssets) {
			array_shift($viewedAssets);
		}
		$viewedAssets[$asset->id]['name'] = apply_filters( 'solidres_asset_name', $asset->name );
		$viewedAssets[$asset->id]['slug'] = $asset->alias;
		if (!empty($media)) {
			$viewedAssets[$asset->id]['media'] = $media[0]->img_url;
		}

		$viewedAssets[$asset->id]['location'] = $asset->city;
		$viewedAssets[$asset->id]['rating']   = $asset->rating;
	}

	solidres()->session->set( 'sr_viewed_assets', $viewedAssets );
endif;

wp_localize_script('solidres_site_script', 'solidres_text', array(
	'can_not_remove_coupon'        => __('Can not remove coupon', 'solidres'),
	'select_at_least_one_roomtype' => __('Please select at least one room type to proceed.', 'solidres'),
	'error_child_max_age'          => __('Ages must be between', 'solidres'),
	'and'                          => __('and', 'solidres'),
	'tariff_break_down'            => __('Tariff break down', 'solidres'),
	'sun'                          => __('Sun', 'solidres'),
	'mon'                          => __('Mon', 'solidres'),
	'tue'                          => __('Tue', 'solidres'),
	'wed'                          => __('Wed', 'solidres'),
	'thu'                          => __('Thu', 'solidres'),
	'fri'                          => __('Fri', 'solidres'),
	'sat'                          => __('Sat', 'solidres'),
	'next'                         => __('Next', 'solidres'),
	'back'                         => __('Back', 'solidres'),
	'processing'                   => __('Processing...', 'solidres'),
	'child'                        => __('Child', 'solidres'),
	'child_age_selection_js'       => __('years old', 'solidres'),
	'child_age_selection_1_js'     => __('year old', 'solidres'),
	'only_1_left'                  => __('Last chance! Only 1 room left', 'solidres'),
	'only_2_left'                  => __('Only 2 rooms left', 'solidres'),
	'only_3_left'                  => __('Only 3 rooms left', 'solidres'),
	'only_4_left'                  => __('Only 4 rooms left', 'solidres'),
	'only_5_left'                  => __('Only 5 rooms left', 'solidres'),
	'only_6_left'                  => __('Only 6 rooms left', 'solidres'),
	'only_7_left'                  => __('Only 7 rooms left', 'solidres'),
	'only_8_left'                  => __('Only 8 rooms left', 'solidres'),
	'only_9_left'                  => __('Only 9 rooms left', 'solidres'),
	'only_10_left'                 => __('Only 10 rooms left', 'solidres'),
	'only_11_left'                 => __('Only 11 rooms left', 'solidres'),
	'only_12_left'                 => __('Only 12 rooms left', 'solidres'),
	'only_13_left'                 => __('Only 13 rooms left', 'solidres'),
	'only_14_left'                 => __('Only 14 rooms left', 'solidres'),
	'only_15_left'                 => __('Only 15 rooms left', 'solidres'),
	'only_16_left'                 => __('Only 16 rooms left', 'solidres'),
	'only_17_left'                 => __('Only 17 rooms left', 'solidres'),
	'only_18_left'                 => __('Only 18 rooms left', 'solidres'),
	'only_19_left'                 => __('Only 19 rooms left', 'solidres'),
	'only_20_left'                 => __('Only 20 rooms left', 'solidres'),
	'show_more_info'               => __('More info', 'solidres'),
	'hide_more_info'               => __('Hide info', 'solidres'),
	'availability_calendar_close'  => __('Close calendar', 'solidres'),
	'availability_calendar_view'   => __('View calendar', 'solidres'),
	'username_exists'              => __('Username exists. Please choose another one.', 'solidres'),
	'show_tariffs'                 => __('Tariffs', 'solidres'),
	'hide_tariffs'                 => __('Tariffs', 'solidres'),
));

$asset = apply_filters('sr_asset', $asset);

solidres()->session->set( 'sr_currency_id', $currency->id );
solidres()->session->set( 'sr_currency_code', $currency->currency_code );
solidres()->session->set( 'sr_deposit_required', $asset->deposit_required );
solidres()->session->set( 'sr_deposit_is_percentage', $asset->deposit_is_percentage );
solidres()->session->set( 'sr_deposit_amount', $asset->deposit_amount );
solidres()->session->set( 'sr_deposit_by_stay_length', $asset->deposit_by_stay_length );
solidres()->session->set( 'sr_deposit_include_extra_cost', $asset->deposit_include_extra_cost );
solidres()->session->set( 'sr_tax_id', $asset->tax_id );
solidres()->session->set( 'sr_booking_type', $asset->booking_type );
solidres()->session->set( 'sr_is_guest_making_reservation', true );

$date_format = get_option( 'date_format', 'd-m-Y' );
$imposed_tax_types = array();
if ( ! empty( $asset->tax_id ) ) {
	$solidres_tax = new SR_Tax;
	$imposed_tax_types[] = $solidres_tax->load( $asset->tax_id );
}

// Get customer information
$customer_group_id = null;
if ( defined( 'SR_PLUGIN_USER_ENABLED' ) && SR_PLUGIN_USER_ENABLED && is_user_logged_in() ) {
	$current_user = wp_get_current_user();
	$customer_group_id = get_user_meta( $current_user->ID, 'customer_group_id', true );
	$customer_group_id = empty($customer_group_id) ? NULL : $customer_group_id;
}

$selectedTariffs = solidres()->session->get( 'sr_current_selected_tariffs', array() );
$selectedRoomTypes = solidres()->session->get( 'sr_room', array() );

$solidres_currency = new SR_Currency( 0, $asset->currency_id );
$checkin = isset( $_GET['checkin'] ) ? $_GET['checkin'] : '';
$checkout = isset( $_GET['checkout'] ) ? $_GET['checkout'] : '';

$msg = '';

$enableAdjoiningTariffs = isset($options_tariff[ 'enable_adjoining_tariffs' ]) ? $options_tariff[ 'enable_adjoining_tariffs' ] : 1;
$adjoiningTariffShowDesc = isset($options_tariff[ 'adjoining_tariffs_show_desc' ]) ? $options_tariff[ 'adjoining_tariffs_show_desc' ] : 0;

if ( ! empty ( $checkin ) && ! empty( $checkout ) ) :

	$conditions                             = array();
	$conditions['min_days_book_in_advance'] = isset( $options_plugin['min_days_book_in_advance'] ) ? $options_plugin['min_days_book_in_advance'] : '';
	$conditions['max_days_book_in_advance'] = isset( $options_plugin['max_days_book_in_advance'] ) ? $options_plugin['max_days_book_in_advance'] : '';
	$conditions['min_length_of_stay']       = isset( $options_plugin['min_length_of_stay'] ) ? $options_plugin['min_length_of_stay'] : '';
	$showPriceWithTax                       = isset( $options_plugin['show_price_with_tax'] ) ? $options_plugin['show_price_with_tax'] : '';

	solidres()->session->set( 'sr_checkin', $checkin );
	solidres()->session->set( 'sr_checkout',  $checkout );

	$stay_length = SR_Utilities::calculate_date_diff($checkin, $checkout);
	if ($asset->booking_type == 1) {
		$stay_length ++;
	}

	try {
		$isCheckInCheckOutValid = $solidres_reservation->isCheckInCheckOutValid( $checkin, $checkout, $conditions );
	} catch ( Exception $e ) {
		switch ( $e->getCode() ) {
			case 50001:
				$msg = __( $e->getMessage(), 'solidres' );
				break;
			case 50002:
				$msg = sprintf( __( $e->getMessage(), 'solidres' ), $conditions['min_length_of_stay'] );
				break;
			case 50003:
				$msg = sprintf( __( $e->getMessage(), 'solidres' ), $conditions['min_days_book_in_advance'] );
				break;
			case 50004:
				$msg = sprintf( __( $e->getMessage(), 'solidres' ), $conditions['max_days_book_in_advance'] );
				break;
		}
	}

	$tariffs = solidres()->session->get( 'sr_current_selected_tariffs' );
endif;
$is_fresh = empty( $checkin ) && empty( $checkout );
$coupon = NULL;
// Init the number of selected adult
$adult = 1;
$child = 0;
$asset->totalOccupancyMax = 0;
$asset->totalOccupancyAdult = 0;
$asset->totalOccupancyChildren = 0;
$asset->totalAvailableRoom = 0;

$rooms_occupancy_options              = isset( $_GET[ 'room_opt' ]) ? $_GET[ 'room_opt' ] : array();
solidres()->session->set( 'sr_room_opt', $rooms_occupancy_options );
$asset->roomsOccupancyOptionsAdults   = 0;
$asset->roomsOccupancyOptionsChildren = 0;
$asset->roomsOccupancyOptionsCount    = count($rooms_occupancy_options);
foreach ($rooms_occupancy_options as $roomOccupancyOptions)
{
	$asset->roomsOccupancyOptionsAdults += $roomOccupancyOptions['adults'];
	$asset->roomsOccupancyOptionsChildren += $roomOccupancyOptions['children'];
}

$room_types = $solidres_room_type->load_by_asset_id( $asset->id );
$count = 1;
foreach ( $room_types as $room_type_idx => $room_type ) :
	$room_type->rowCSSClass = ( $count % 2 ) ? ' even' : ' odd';
	$room_type->rowCSSClass .= $room_type->featured == 1 ? ' featured' : '';
	$currentSelectedRoomNumberPerTariff = array();
	$room_type->media = $solidres_media->load_by_room_type_id( $room_type->id );

	// For each room type, we load all relevant tariffs for front end user selection
	// When complex tariff plugin is not enabled, load standard tariff
	$standard_tariff   = null;
	$room_type->tariffs = array();
	if (!defined('SR_PLUGIN_COMPLEXTARIFF_ENABLED') || !SR_PLUGIN_COMPLEXTARIFF_ENABLED)  {
		$standard_tariff = $solidres_tariff->load_by_room_type_id( $room_type->id );
		$room_type_standard_tariff = null;
		if ( isset( $standard_tariff[0]->id ) ) {
			$room_type->tariffs[] = $solidres_tariff->load( $standard_tariff[0]->id );
		}
	} else {  // When complex tariff plugin is enabled
		$complex_tariffs = NULL;
		$complex_tariffs = $solidres_tariff->load_by_room_type_id( $room_type->id, false, OBJECT, $checkin, $checkout, 1, $customer_group_id );
		foreach ( $complex_tariffs as $complex_tariff ) {

			if ( !empty( $complex_tariff->limit_checkin)) {
				if ( !empty($checkin) && !empty($checkout)) {
					$limit_checkin_array = $complex_tariff->limit_checkin;
					$checkinDate          = new DateTime($checkin);
					$dayInfo              = getdate($checkinDate->format('U'));

					// If the current check in date does not match the allowed check in dates, we ignore this tariff
					if (!in_array($dayInfo['wday'], $limit_checkin_array)) {
						continue;
					}
				}
			}

			if (!empty($rooms_occupancy_options)) {
				$is_valid_people_range    = true;
				$people_range_match_count = count($rooms_occupancy_options);

				foreach ($rooms_occupancy_options as $room_occupancy_options) {
					$total_people_requested = $room_occupancy_options['adults'] + $room_occupancy_options['children'];

					if ($complex_tariff->p_min > 0 && $complex_tariff->p_max > 0) {
						$is_valid_people_range = $total_people_requested >= $complex_tariff->p_min && $total_people_requested <= $complex_tariff->p_max;
					} elseif (empty($complex_tariff->p_min) && $complex_tariff->p_max > 0) {
						$is_valid_people_range = $total_people_requested <= $complex_tariff->p_max;
					} elseif ($complex_tariff->p_min > 0 && empty($complex_tariff->p_max)) {
						$is_valid_people_range = $total_people_requested >= $complex_tariff->p_min;
					}

					if (!$is_valid_people_range) {
						$people_range_match_count--;
					}
				}

				if ($people_range_match_count == 0) {
					continue;
				}
			}

			$room_type->tariffs[] = $solidres_tariff->load( $complex_tariff->id, false );
		}
	}

	if (!empty($checkin) && !empty($checkout))
	{
		$solidres_roomtype = new SR_Room_Type();
		$room_typeObj = $solidres_roomtype->load( $room_type->id );

		// Check for number of available rooms first, if no rooms found, we should skip this room type
		$listAvailableRoom = $solidres_roomtype->getListAvailableRoom($room_type->id, $checkin, $checkout, $asset->booking_type);
		$room_type->totalAvailableRoom = is_array($listAvailableRoom) ? count($listAvailableRoom) : 0 ;

		// Check for limit booking, if all rooms are locked, we can remove this room type without checking further
		// This is for performance purpose
		if ($room_type->totalAvailableRoom == 0)
		{
			unset($room_types[$room_type_idx]);
			continue;
		}

		// Build the config values
		$tariffConfig = array(
			'booking_type' => $asset->booking_type,
			'adjoining_tariffs_mode' => isset($options_tariff[ 'adjoining_tariffs_mode' ]) ? $options_tariff[ 'adjoining_tariffs_mode' ] : 0,
			'child_room_cost_calc' => isset($options_tariff[ 'child_room_cost_calc' ]) ? $options_tariff[ 'child_room_cost_calc' ] : 1,
			'adjoining_tariffs_show_desc' => $adjoiningTariffShowDesc
		);
		if (isset($room_typeObj->params['enable_single_supplement'])
		    &&
		    $room_typeObj->params['enable_single_supplement'] == 1)
		{
			$tariffConfig['enable_single_supplement'] = true;
			$tariffConfig['single_supplement_value'] = $room_typeObj->params['single_supplement_value'];
			$tariffConfig['single_supplement_is_percent'] = $room_typeObj->params['single_supplement_is_percent'];
		}
		else
		{
			$tariffConfig['enable_single_supplement'] = false;
		}

		// Get discount
		$discounts = array();
		$isDiscountPreTax = isset( $options_plugin[ 'discount_pre_tax' ] ) ? $options_plugin[ 'discount_pre_tax' ] : 0;
		if (defined( 'SR_PLUGIN_DISCOUNT_ENABLED' ) && SR_PLUGIN_DISCOUNT_ENABLED)
		{
			$solidres_discount = new SR_Discount();
			$discounts = $solidres_discount->load_discounts( $checkin, $checkout, array(1), 1, $asset->id);
		}

		// Holds all available tariffs (filtered) that takes checkin/checkout into calculation to be showed in front end
		$availableTariffs = array();
		$room_type->availableTariffs = array();
		if (defined( 'SR_PLUGIN_COMPLEXTARIFF_ENABLED' ) && SR_PLUGIN_COMPLEXTARIFF_ENABLED)
		{
			if (!empty($room_type->tariffs))
			{
				foreach ($room_type->tariffs as $filteredComplexTariff)
				{
					$availableTariffs[] = $solidres_roomtype->getPrice($room_type->id, $customer_group_id, $imposed_tax_types, false, true, $checkin, $checkout, $solidres_currency, $coupon, $adult, $child, array(), $stay_length, $filteredComplexTariff->id, $discounts, $isDiscountPreTax, $tariffConfig);
				}
			}
			/*else
			{*/
			if ($enableAdjoiningTariffs)
			{
				$isApplicableAdjoiningTariffs = SR_Utilities::isApplicableForAdjoiningTariffs($room_type->id, $checkin, $checkout);

				$tariffAdjoiningLayer = 0;
				$isApplicableAdjoiningTariffs2 = array();
				while (count($isApplicableAdjoiningTariffs) == 2)
				{
					$isApplicableAdjoiningTariffs2 = array_merge($isApplicableAdjoiningTariffs, $isApplicableAdjoiningTariffs2);
					$tariffConfig['adjoining_layer'] = $tariffAdjoiningLayer;
					$availableTariffs[] = $solidres_roomtype->getPrice($room_type->id, $customer_group_id, $imposed_tax_types, false, true, $checkin, $checkout, $solidres_currency, $coupon, $adult, $child, array(), $stay_length, NULL, $discounts, $isDiscountPreTax, $tariffConfig);
					$isApplicableAdjoiningTariffs = SR_Utilities::isApplicableForAdjoiningTariffs($room_type->id, $checkin, $checkout, $isApplicableAdjoiningTariffs2);
					if (empty($isApplicableAdjoiningTariffs))
					{
						break;
					}
					$tariffAdjoiningLayer ++;
				}
			}
			/*}*/
		}
		else
		{
			$availableTariffs[] = $solidres_roomtype->getPrice($room_type->id, $customer_group_id, $imposed_tax_types, true, false, $checkin, $checkout, $solidres_currency, $coupon, 0, 0, array(), $stay_length, $room_type->tariffs[0]->id, $discounts, $isDiscountPreTax, $tariffConfig);
		}

		foreach ($availableTariffs as $availableTariff)
		{
			$id = $availableTariff['id'];
			if ($showPriceWithTax)
			{
				$room_type->availableTariffs[$id]['val'] = $availableTariff['total_price_tax_incl_discounted_formatted'];
			}
			else
			{
				$room_type->availableTariffs[$id]['val'] = $availableTariff['total_price_tax_excl_discounted_formatted'];
			}
			$room_type->availableTariffs[$id]['tariffTaxIncl'] = $availableTariff['total_price_tax_incl_discounted_formatted'];
			$room_type->availableTariffs[$id]['tariffTaxExcl'] = $availableTariff['total_price_tax_excl_discounted_formatted'];
			$room_type->availableTariffs[$id]['tariffIsAppliedCoupon'] = $availableTariff['is_applied_coupon'];
			$room_type->availableTariffs[$id]['tariffType'] = $availableTariff['type']; // Per room per night or Per person per night
			$room_type->availableTariffs[$id]['tariffBreakDown'] = $availableTariff['tariff_break_down'];
			// Useful for looping with Hub
			$room_type->availableTariffs[$id]['tariffTitle'] = $availableTariff['title'];
			$room_type->availableTariffs[$id]['tariffDescription'] = $availableTariff['description'];
			// For adjoining cases
			$room_type->availableTariffs[$id]['tariffAdjoiningLayer'] = $availableTariff['adjoining_layer'];
		}

		if ($room_type->occupancy_max > 0)
		{
			$asset->totalOccupancyMax += $room_type->occupancy_max * $room_type->totalAvailableRoom;
		}
		else
		{
			$asset->totalOccupancyMax += ($room_type->occupancy_adult + $room_type->occupancy_child) * $room_type->totalAvailableRoom;
		}

		$tariffsForFilter = array();
		if (is_array($room_type->availableTariffs))
		{
			foreach ($room_type->availableTariffs as $tariffId => $tariffInfo)
			{
				if (is_null($tariffInfo['val']))
				{
					continue;
				}
				$tariffsForFilter[$tariffId] = $tariffInfo['val']->getValue();
			}
		}

		// Remove tariffs that has the same price
		$tariffsForFilter = array_unique($tariffsForFilter);
		foreach ($room_type->availableTariffs as $tariffId => $tariffInfo)
		{
			$uniqueTariffIds = array_keys($tariffsForFilter);
			if (!in_array($tariffId, $uniqueTariffIds))
			{
				unset($room_type->availableTariffs[$tariffId]);
			}
		}


		// Take overlapping mode into consideration
		$overlappingTariffsMode = isset($options_tariff[ 'overlapping_tariffs_mode' ]) ? $options_tariff[ 'overlapping_tariffs_mode' ] : 0;
		$tariffsForFilterOverlapping = $tariffsForFilter;
		asort($tariffsForFilterOverlapping); // from lowest to highest
		$lowestTariffId = NULL;
		$highestTariffId = NULL;
		switch ($overlappingTariffsMode)
		{
			case 0:
				break;
			case 1: // Lowest
				$tariffsForFilterOverlappingKeys = array_keys($tariffsForFilterOverlapping);
				$lowestTariffId = current($tariffsForFilterOverlappingKeys);
				SR_Utilities::removeArrayElementsExcept($room_type->availableTariffs, $lowestTariffId);
				break;
			case 2: // Highest
				$tariffsForFilterOverlappingKeys = array_keys($tariffsForFilterOverlapping);
				$highestTariffId = end($tariffsForFilterOverlappingKeys);
				SR_Utilities::removeArrayElementsExcept( $room_type->availableTariffs, $highestTariffId);
				break;
		}


		if (defined( 'SR_PLUGIN_HUB_ENABLED' ) && SR_PLUGIN_HUB_ENABLED)
		{
			$origin = $this->getState('origin');
			if ($origin == 'hubsearch')
			{
				if (empty($tariffsForFilter))
				{
					unset($room_types[$room_type_idx]);
					continue;
				}
			}

			if (!empty($tariffsForFilter))
			{
				$filterConditions = array(
					'tariffs_for_filter' => $tariffsForFilter
				);

				$filteringResults = $dispatcher->trigger('onReservationAssetFilterRoomType', array(
					'com_solidres.reservationasset',
					$asset,
					$this->getState(),
					$filterConditions
				));

				$qualifiedTariffs = array();
				$room_typeMatched = true;

				foreach ($filteringResults as $result)
				{
					if (!is_array($result))
					{
						continue;
					}

					$qualifiedTariffs = $result;

					if (count($qualifiedTariffs) <= 0) // No qualified tariffs
					{
						$room_typeMatched = false;
						continue;
					}
				}

				if (!$room_typeMatched)
				{
					unset($room_types[$room_type_idx]);
					continue;
				}
				else // This room type is matched but we have to check if all tariffs are matched or just some matched?
				{
					if (!empty($qualifiedTariffs) && count($qualifiedTariffs) != count($room_type->availableTariffs))
					{
						foreach ($room_type->availableTariffs as $k => $v)
						{
							if (!isset($qualifiedTariffs[$k]))
							{
								unset($room_type->availableTariffs[$k]);
							}
						}
					}
				}
			}
		} // End logic of Hub's filtering

		// If this room type has no available tariffs, it is equal to no availability therefore don't count
		// this room type's rooms
		if (!empty($room_type->availableTariffs)) {
			$asset->totalAvailableRoom += $room_type->totalAvailableRoom;
		} else {
			if ( isset($asset_params['show_unavailable_roomtype']) && $asset_params['show_unavailable_roomtype'] == 0 ) {
				unset($room_types[$room_type_idx]);
				continue;
			}
		}
	}
endforeach;
unset($room_type); // Clear the reference

$solidres_country = new SR_Country;
$country = $solidres_country->load( $asset->country_id );
$asset_custom_fields = new SR_Custom_Field( array( 'id' => (int) $asset->id, 'type' => 'asset' ) );
$custom_fields = $asset_custom_fields->create_array_group();

?>

<div id="solidres" class="<?php echo SR_UI ?>">
	<div class="reservation_asset_item clearfix">
		<?php
		if ($asset_params['only_show_reservation_form'] == 0) :
			$media = $solidres_media->load_by_asset_id($asset->id);
			require( 'header.php' );
		endif;

		if ($options_plugin['show_login_box'] == 1) : ?>
		<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
			<div class="<?php echo SR_UI_GRID_COL_12 ?>">
			<?php
			if (is_user_logged_in()) :
				$current_user = wp_get_current_user();
				solidres_get_template( 'reservationasset/userinfo.php', array('current_user' => $current_user), '', WP_PLUGIN_DIR . '/solidres-user/templates/' );
			else :
				solidres_get_template( 'reservationasset/login.php', array('return_url' => base64_encode( get_site_url() . '/' . solidres()->session->get( 'sr_wp_page_id' ) ) ), '', WP_PLUGIN_DIR . '/solidres-user/templates/' );
			endif;
			?>
			</div>
		</div>
		<?php endif; ?>

		<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
			<div class="<?php echo SR_UI_GRID_COL_12 ?>">
				<?php require( 'roomtype.php' ); ?>
			</div>
		</div>

		<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
			<div class="<?php echo SR_UI_GRID_COL_12 ?>">
				<?php require( 'information.php' ); ?>
			</div>
		</div>

		<?php require( 'map.php' ); ?>

	</div>
</div>

<script>
	jQuery(function ($) {
		isAtLeastOnRoomTypeSelected();
	});
</script>