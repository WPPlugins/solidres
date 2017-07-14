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
global $wpdb;

$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservation_assets ADD deposit_include_extra_cost TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 AFTER deposit_by_stay_length;" );