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
 * Currency handler class
 *
 * @since 0.3.0
 */
class SR_Currency {
	protected $id = 0;
	protected $activeId;
	protected $code;
	protected $sign;
	protected $name;
	protected $rate;
	protected $value = 0;
	protected $formatOptions = array();
	protected $fromExchangeRate;
	protected $toExchangeRate;

	/**
	 * Currency Constructor
	 *
	 * @param $value
	 * @param $id
	 * @param int $scopeId 0 is Global
	 */
	public function __construct( $value = 0, $id = 0, $scopeId = 0 ) {

		global $wpdb;
		$this->wpdb = $wpdb;

		if ($value > 0) {
			$this->value = $value;
		}

		if ($id > 0) {
			$this->id = $id;
		}

		// Query for global currency display format
		if ($scopeId == 0) {
			$options = get_option('solidres_currency');
			$this->formatOptions['currency_format_pattern'] = $options['currency_format_pattern'] != '' ? $options['currency_format_pattern'] : 1;
			$this->formatOptions['number_decimal_points'] = $options['number_decimal_points'] != '' ? $options['number_decimal_points'] : 2;
			$this->formatOptions['currency_code_symbol'] = $options['currency_code_symbol'] != '' ? $options['currency_code_symbol'] : 'code';
		} else { // Query for reservation asset currency display format

		}

		$this->activeId = isset(solidres()->session) ? solidres()->session->get( 'current_currency_id', 0 ) : 0;

		$this->getCurrencyDetails();

		$this->fromExchangeRate = $this->toExchangeRate = $this->rate;

		// Exchange the value
		if ($this->activeId > 0 && $this->activeId != $this->id)
		{
			$this->fromExchangeRate = $this->rate;
			$this->id = $this->activeId;
			$this->getCurrencyDetails();
			$this->toExchangeRate = $this->rate;
			if ( $this->value > 0 )
			{
				$this->value = $this->value * ($this->toExchangeRate / $this->fromExchangeRate);
				$this->value = round($this->value, $this->formatOptions['number_decimal_points']);
			}
		}
	}

	/**
	 * Update states for listview
	 *
	 * @param $action
	 * @param $currency_id
	 * @param $ids
	 */
	public function update_states( $action, $currency_id, $ids ){
		$states = array(
			'draft' => array( 'state' => 0, 'action' => 'moved', 'title' => 'Draft' ),
			'publish' => array( 'state' => 1, 'action' => 'moved', 'title' => 'Publish' ),
			'trash' => array( 'state' => -2, 'action' => 'moved', 'title' => 'Trash' ),
			'untrash' => array( 'state' => 0, 'action' => 'restored', 'title' => 'Trash' ),
		);

		if ( isset( $action ) && array_key_exists ( $action, $states ) &&  isset( $currency_id ) && $currency_id != null ) {
			foreach ( $ids as $id ) {
				$this->wpdb->update( $this->wpdb->prefix . 'sr_currencies', array( 'state' => $states[$action]['state'] ), array( 'id' => $id ) );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 currency ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			}
			else {
				$message = __( count( $ids ).' currencies ' . $states[$action]['action'] . ' to the ' . $states[$action]['title'], 'solidres' );
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
		$count_tariff = $this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_tariffs WHERE currency_id = $id" );
		$count_reservation = $this->wpdb->get_var( "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_reservation_assets WHERE currency_id = $id" );
		if ( $count_tariff > 0 || $count_reservation > 0 ) {
			return false;
		}
		else {
			$this->wpdb->update( $this->wpdb->prefix.'sr_reservations', array( 'currency_id' => NULL ), array( 'currency_id' => $id ) );
			$this->wpdb->delete( $this->wpdb->prefix.'sr_currencies', array( 'id' => $id ) );
		}
	}

	/**
	 * Get a single currency by id
	 *
	 * @param $id
	 * @param $output
	 *
	 * @return mixed
	 */
	public function load( $id, $output = OBJECT ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_currencies WHERE id = %d", $id ), $output );
	}

	/**
	 * Get a list currencies by state
	 *
	 * @param $state
	 *
	 * @return mixed
	 */
	public function load_by_state( $state ) {
		return $this->wpdb->get_results( "SELECT * FROM {$this->wpdb->prefix}sr_currencies WHERE state = $state" );
	}

	/**
	 * Format the given number
	 *
	 * @return string
	 */
	public function format()
	{
		$prefix = $this->{$this->formatOptions['currency_code_symbol']};
		switch ($this->formatOptions['currency_format_pattern'])
		{
			case 1:  // X0,000.00
			default:
				$formatted = $prefix . number_format($this->value, $this->formatOptions['number_decimal_points'] );
				break;
			case 2: // 0 000,00X
				$formatted = number_format($this->value, $this->formatOptions['number_decimal_points'], ',', ' ' ) . $prefix;
				break;
			case 3: // X0.000,00
				$formatted = $prefix . number_format($this->value, $this->formatOptions['number_decimal_points'], ',', '.' );
				break;
			case 4: // 0,000.00X
				$formatted = number_format($this->value, $this->formatOptions['number_decimal_points'], '.', ',' ) . $prefix;
				break;
			case 5: // 0 000.00X
				$formatted = number_format($this->value, $this->formatOptions['number_decimal_points'], '.', ' ' ) . $prefix;
				break;
			case 6:  // X 0,000.00
				$formatted = $prefix . ' ' . number_format($this->value, $this->formatOptions['number_decimal_points'] );
				break;
			case 7: // 0 000,00 X
				$formatted = number_format($this->value, $this->formatOptions['number_decimal_points'], ',', ' ' ) . ' ' . $prefix;
				break;
			case 8: // X 0.000,00
				$formatted = $prefix . ' ' . number_format($this->value, $this->formatOptions['number_decimal_points'], ',', '.' );
				break;
			case 9: // 0,000.00 X
				$formatted = number_format($this->value, $this->formatOptions['number_decimal_points'], '.', ',' ) . ' ' . $prefix;
				break;
			case 10: // 0 000.00 X
				$formatted = number_format($this->value, $this->formatOptions['number_decimal_points'], '.', ' ' ) . ' ' .$prefix;
				break;
		}

		return $formatted;
	}

	public function set_value( $value )
	{
		$this->value = $value;
		if ( $this->toExchangeRate > 0 ) {
			$this->value = $this->value * ($this->toExchangeRate / $this->fromExchangeRate);
		}
		$this->value = round( $this->value, $this->formatOptions['number_decimal_points']);
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setCode($code)
	{
		$this->code = $code;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function setActiveId($activeId)
	{
		$this->activeId = $activeId;
	}

	public function getActiveId()
	{
		return $this->activeId;
	}

	public function setRate($rate)
	{
		$this->rate = $rate;
	}

	public function getRate()
	{
		return $this->rate;
	}

	public function setSign($sign)
	{
		$this->sign = $sign;
	}

	public function getSign()
	{
		return $this->sign;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setFormatOptions($formatOptions)
	{
		$this->formatOptions = $formatOptions;
	}

	public function getFormatOptions()
	{
		return $this->formatOptions;
	}

	/**
	 * Query for currency details
	 *
	 * @return Object
	 */
	public function getCurrencyDetails()
	{
		global $wpdb;
		$details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_currencies WHERE id = %d", $this->id ), ARRAY_A );
		$this->id = $details['id'];
		$this->code = $details['currency_code'];
		$this->sign = $details['sign'];
		$this->name = $details['currency_name'];
		$this->rate = $details['exchange_rate'];
	}
}