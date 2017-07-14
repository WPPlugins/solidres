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
$wpdb->query( "
CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sr_sessions(
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		session_key CHAR(64) NOT NULL,
		session_value LONGTEXT NOT NULL,
		session_expiry BIGINT(20) NOT NULL,
		UNIQUE KEY id (id),
  		PRIMARY KEY  (session_key))
	ENGINE = InnoDB;
" );
