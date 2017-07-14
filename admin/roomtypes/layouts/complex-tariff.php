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
} ?>

<div id="roomtype_complex_tariff" class="postbox closed open">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Complex tariff', 'solidres' ); ?></span></h3>

	<div class="inside">
		<?php
		if (defined('SR_PLUGIN_COMPLEXTARIFF_ENABLED') && SR_PLUGIN_COMPLEXTARIFF_ENABLED) :
		?>
			<div class="alert alert-info">
				<?php _e('When plugin Complex Tariff is installed, Solidres will only use it to search for available rooms and tariffs. Standard tariff will be ignored.', 'solidres') ?>
			</div>

			<iframe class="tariff-wrapper" src="admin.php?page=sr-complextariff&id=<?php echo $id ?>&currency_id=<?php echo $sr_form_data->currency->id ?>&reservation_asset_id=<?php echo $sr_form_data->reservation_asset_id ?>#tariffs">
			</iframe>
		<?php
		else :
			$message_update = __( 'This feature allows you to configure more flexible tariff, more info can be found <a href="http://www.solidres.com/features-highlights#feature-complextariff" target="_blank">here.</a>', 'solidres' );
			$message_trash  = __( '<strong>Notice:</strong> Complex Tariff and User are not installed or enabled. <a target="blank" href="https://www.solidres.com/subscribe/levels">Become a subscriber and download it now.</a>', 'solidres' );
			SR_Helper::show_message( $message_update );
			SR_Helper::show_message( $message_trash, 'trashed' );
		endif;
		?>
	</div>
</div>