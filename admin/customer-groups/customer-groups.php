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

function sr_customer_groups() { ?>
	<div id="sr_panel_right" class="sr_list_view">
		<?php
		$message_update = __( 'This feature allows your guest to register an account at your website while making reservation. When a guest has an account at your website, you can manage them in backend, create tariffs specified for them. In addition, with an account the reservation will be faster because many guest\'s info will be auto-filled.', 'solidres' );
		$message_trash  = __( '<strong>Notice:</strong> Solidres Users plugin is not installed. <a target="blank" href="https://www.solidres.com/subscribe/levels">Become a subscriber and download it now.</a>', 'solidres' );
		SR_Helper::show_message( $message_update );
		SR_Helper::show_message( $message_trash, 'trashed' );
		?>
	</div>
<?php
}