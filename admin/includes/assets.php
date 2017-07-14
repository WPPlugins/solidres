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

include WP_PLUGIN_DIR . '/solidres/admin/assets/list.php';
include WP_PLUGIN_DIR . '/solidres/admin/assets/edit.php';

include WP_PLUGIN_DIR . '/solidres/admin/categories/list.php';
include WP_PLUGIN_DIR . '/solidres/admin/categories/edit.php';

include WP_PLUGIN_DIR . '/solidres/admin/roomtypes/list.php';
include WP_PLUGIN_DIR . '/solidres/admin/roomtypes/edit.php';