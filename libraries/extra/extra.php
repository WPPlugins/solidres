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
 * Extra handler class
 *
 * @package 	Solidres
 * @subpackage	Extra
 */
class SR_Extra extends SR_Extra_Base {
	/**
	 * The database object
	 * @var object
	 */
	public function __construct( $extra_details = array() ) {

		parent::__construct( $extra_details );
	}

	public function calculate_extra_cost()
	{
		$total_extra_cost_tax_incl = 0;
		$total_extra_cost_tax_excl = 0;
		$quantity = 1;
		if (isset($this->quantity))
		{
			$quantity = $this->quantity;
		}

		switch ($this->charge_type)
		{
			case 1: // Per booking
			case 0: // Per room or Per booking
			default:
				$total_extra_cost_tax_incl += $this->price_tax_incl * $quantity;
				$total_extra_cost_tax_excl += $this->price_tax_excl * $quantity;
		}

		return array(
			'total_extra_cost_tax_incl' => $total_extra_cost_tax_incl,
			'total_extra_cost_tax_excl' => $total_extra_cost_tax_excl
		);
	}
}