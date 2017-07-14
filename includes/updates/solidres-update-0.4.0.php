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
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservation_room_xref CHANGE tariff_title tariff_title TEXT NULL DEFAULT NULL;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservation_room_xref CHANGE tariff_description tariff_description TEXT NULL DEFAULT NULL;" );
