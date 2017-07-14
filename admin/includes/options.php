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

include WP_PLUGIN_DIR . '/solidres/admin/currencies/list.php';
include WP_PLUGIN_DIR . '/solidres/admin/currencies/edit.php';

include WP_PLUGIN_DIR . '/solidres/admin/countries/list.php';
include WP_PLUGIN_DIR . '/solidres/admin/countries/edit.php';

include WP_PLUGIN_DIR . '/solidres/admin/states/list.php';
include WP_PLUGIN_DIR . '/solidres/admin/states/edit.php';

include WP_PLUGIN_DIR . '/solidres/admin/taxes/list.php';
include WP_PLUGIN_DIR . '/solidres/admin/taxes/edit.php';

include WP_PLUGIN_DIR . '/solidres/admin/limitbookings/limit-bookings.php';
include WP_PLUGIN_DIR . '/solidres/admin/employees/employees.php';
include WP_PLUGIN_DIR . '/solidres/admin/facilities/facilities.php';
include WP_PLUGIN_DIR . '/solidres/admin/themes/themes.php';
include WP_PLUGIN_DIR . '/solidres/admin/options/options.php';
include WP_PLUGIN_DIR . '/solidres/admin/systems/systems.php';
