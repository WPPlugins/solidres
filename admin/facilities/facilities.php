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

function sr_facilities_inactive() { ?>
	<div id="sr_panel_right" class="sr_list_view">
		<?php
		$message = __( '<strong>Notice:</strong> please install and activate <b>Solidres Hub</b> plugin at <a target="blank" href="' . admin_url() . 'plugins.php"><strong>Plugins manager</strong></a>. ', 'solidres' );
		SR_Helper::show_message( $message, 'trashed' );
		?>
	</div>
<?php }