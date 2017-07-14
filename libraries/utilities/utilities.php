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
 * Utilities handler class
 * @package 	Solidres
 * @subpackage	Utilities
 */
class SR_Utilities {
	public static function translateDayWeekName( $inputs ) {
		$dayMapping = array(
			'0' => __( 'Sun', 'solidres' ),
			'1' => __( 'Mon', 'solidres' ),
			'2' => __( 'Tue', 'solidres' ),
			'3' => __( 'Wed', 'solidres' ),
			'4' => __( 'Thu', 'solidres' ),
			'5' => __( 'Fri', 'solidres' ),
			'6' => __( 'Sat', 'solidres' )
		);
		foreach ( $inputs as $input ) {
			$input->w_day_name = $dayMapping[$input->w_day];
		}
		return $inputs;
	}

	public static function translateText( $text ) {
		if ( strpos( $text, '{lang' ) !== false ) {
			$text = self::filterText( $text );
		}
		return $text;
	}

	public static function getTariffDetailsScaffoldings( $config = array() ) {
		$scaffoldings = array();
		// If this is package per person or package per room
		if ( $config['type'] == 2 || $config['type'] == 3 ) {
			$scaffoldings[0] = new stdClass();
			$scaffoldings[0]->id = null;
			$scaffoldings[0]->tariff_id = $config['tariff_id'];
			$scaffoldings[0]->price = null;
			$scaffoldings[0]->w_day = 8;
			$scaffoldings[0]->guest_type = $config['guest_type'];
			$scaffoldings[0]->from_age = null;
			$scaffoldings[0]->to_age = null;
		}
		else // For normal complex tariff
		{
			for ( $i = 0; $i < 7; $i++ ) {
				$scaffoldings[$i] = new stdClass();
				$scaffoldings[$i]->id = null;
				$scaffoldings[$i]->tariff_id = $config['tariff_id'];
				$scaffoldings[$i]->price = null;
				$scaffoldings[$i]->w_day = $i;
				$scaffoldings[$i]->guest_type = $config['guest_type'];
				$scaffoldings[$i]->from_age = null;
				$scaffoldings[$i]->to_age = null;
			}
		}
		return $scaffoldings;
	}

	/* Translate custom field by using language tag. Author: isApp.it Team */
	public static function getLagnCode() {
		$lang_codes = JLanguageHelper::getLanguages('lang_code');
		$lang_code 	= $lang_codes[JFactory::getLanguage()->getTag()]->sef;
		return $lang_code;
	}

	/* Translate custom field by using language tag. Author: isApp.it Team */
	public static function filterText( $text ) {
		if ( strpos( $text, '{lang' ) === false ) return $text;
		$lang_code = self::getLagnCode();
		$regex = "#{lang ".$lang_code."}(.*?){\/lang}#is";
		$text = preg_replace($regex,'$1', $text);
		$regex = "#{lang [^}]+}.*?{\/lang}#is";
		$text = preg_replace($regex,'', $text);
		return $text;
	}

	/**
	 * This simple function return a correct javascript date format pattern based on php date format pattern
	 **/
	public static function convert_date_format_pattern( $input ){
		$mapping = array(
			'd-m-Y' => 'dd-mm-yy',
			'd/m/Y' => 'dd/mm/yy',
			'd M Y' => 'dd M yy',
			'd F Y' => 'dd MM yy',
			'D, d M Y' => 'D, dd M yy',
			'l, d F Y' => 'DD, dd MM yy',
			'Y-m-d' => 'yy-mm-dd',
			'm-d-Y' => 'mm-dd-yy',
			'm/d/Y' => 'mm/dd/yy',
			'M d, Y' => 'M dd, yy',
			'F d, Y' => 'MM dd, yy',
			'D, M d, Y' => 'D, M dd, yy',
			'l, F d, Y' => 'DD, MM dd, yy',
			'F j, Y' => 'MM d, yy',
			'j. F Y' => 'd. MM yy'
		);

		if ( isset( $mapping[$input] ) ) {
			return $mapping[$input];
		} else {
			return $mapping['d-m-Y'];
		}
	}

	public static function calculate_date_diff($from, $to, $format = '%a')
	{
		$datetime1 = new DateTime($from);
		$datetime2 = new DateTime($to);

		$interval = $datetime1->diff($datetime2);

		return $interval->format($format);
	}

	public static function isApplicableForAdjoiningTariffs($roomTypeId, $checkIn, $checkOut, $excludes = array())
	{
		global $wpdb;

		$result = array();

		$query = '
				(SELECT DISTINCT t1.id
				FROM ' . ($wpdb->prefix . 'sr_tariffs') . ' AS t1
				WHERE t1.valid_to >= \''. $checkIn. '\' AND t1.valid_to <= \'' . $checkOut . '\'
				AND t1.valid_from <= \'' . $checkIn . '\' AND t1.state = 1 AND t1.room_type_id = ' . (int) $roomTypeId . '
				'. (!empty($excludes) ? 'AND t1.id NOT IN (' . implode(',', $excludes) . ')' : '' ). '
				LIMIT 1)
				UNION ALL
				(SELECT DISTINCT t2.id
				FROM ' . ($wpdb->prefix . 'sr_tariffs') . ' AS t2
				WHERE t2.valid_from <= \'' . $checkOut . '\' AND t2.valid_from >= \'' . $checkIn . '\'
				AND t2.valid_to >= \''. $checkOut . '\' AND t2.state = 1 AND t2.room_type_id = ' . (int) $roomTypeId . '
				'. (!empty($excludes) ? 'AND t2.id NOT IN (' . implode(',', $excludes) . ')' : '' ). '
				LIMIT 1)
				';

		$tariffIds = $wpdb->get_results( $query );

		if (count($tariffIds) == 2)
		{
			$query = 'SELECT datediff(t2.valid_from, t1.valid_to)
					FROM ' . ($wpdb->prefix . 'sr_tariffs') . ' AS t1, ' . ($wpdb->prefix . 'sr_tariffs') . ' AS t2
					WHERE t1.id = ' . (int) $tariffIds[0]->id . ' AND t2.id = ' . (int) $tariffIds[1]->id;

			if ($wpdb->get_var( $query ) == 1)
			{
				$result = array($tariffIds[0]->id, $tariffIds[1]->id);
			}
		}

		return $result;
	}

	public static function removeArrayElementsExcept(&$array, $keyToRemain)
	{
		foreach ($array as $key => $val)
		{
			if ($key != $keyToRemain)
			{
				unset($array[$key]);
			}
		}
	}
}