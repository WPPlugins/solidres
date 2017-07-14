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

<div id="asset_extra" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Extra', 'solidres' ); ?></span></h3>

	<div class="inside nopadding">
		<table class="widefat striped">
			<tbody>
			<?php echo ( isset( $id ) && isset( $sr_form_data->currency_id ) ) ? SR_Helper::get_extra_asset( $id, $sr_form_data->currency_id ) : SR_Helper::get_extra_asset(); ?>
			</tbody>
		</table>
	</div>
</div>