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
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_room_types ADD occupancy_max TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER ordering;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_tariffs CHANGE title title TEXT NULL DEFAULT NULL ;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_tariffs CHANGE description description TEXT NULL DEFAULT NULL;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_tariffs ADD state TINYINT(3) NOT NULL DEFAULT 1;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD customer_mobilephone VARCHAR(45) NOT NULL AFTER customer_phonenumber ;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD payment_data TEXT NULL AFTER  payment_status ;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_extras ADD price_adult DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT  '0.00' AFTER price;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_extras ADD price_child DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT  '0.00' AFTER price_adult;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD discount_pre_tax TINYINT(3) UNSIGNED NULL DEFAULT NULL;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD tax_amount DECIMAL(12,2) UNSIGNED NOT NULL;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservation_assets ADD booking_type TINYINT(3) UNSIGNED NOT NULL DEFAULT 0;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD booking_type TINYINT(3) UNSIGNED NOT NULL DEFAULT 0;" );
$wpdb->query( "DROP TABLE {$wpdb->prefix}sr_media;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservation_assets ADD deposit_by_stay_length INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER deposit_amount;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD total_single_supplement DECIMAL(12,2) UNSIGNED NULL ;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD token VARCHAR(40) NULL DEFAULT NULL ;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD origin VARCHAR(255) NULL DEFAULT NULL ;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservations ADD accessed_date DATETIME NULL DEFAULT NULL AFTER origin;" );
$wpdb->query( "ALTER TABLE {$wpdb->prefix}sr_reservation_assets CHANGE approved approved TINYINT(1) UNSIGNED NULL DEFAULT NULL;" );
$wpdb->query( "UPDATE {$wpdb->prefix}sr_reservation_assets SET approved = NULL WHERE approved = 0;" );
