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

?>

<div id="asset_room_type" class="postbox">
	<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Categories</span><span class="toggle-indicator" aria-hidden="true"></span></button>
	<h3 class="hndle"><span><?php _e( 'Room type', 'solidres' ); ?></span></h3>

	<div class="inside nopadding">
		<table class="widefat striped">
			<tbody>
			<?php echo isset( $id ) ? SR_Helper::get_room_type_asset( $id ) : SR_Helper::get_room_type_asset(); ?>
			</tbody>
		</table>
	</div>
</div>