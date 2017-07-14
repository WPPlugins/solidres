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

<div id="roomtype_room" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Room', 'solidres' ); ?></span></h3>

	<div class="inside">
		<div id="room_girdview" class="room_girdview">
			<div class="add_new_room">
				<input type="button" id="add_new_room" class="button" value="<?php _e( 'Add Room', 'solidres' ); ?>"/>
				<input type="text" size="15" id="room_label" value=""
				       placeholder="<?php _e( 'Enter room label', 'solidres' ); ?>">

				<div class="clr"></div>
			</div>
			<table class="form-table">
				<tbody>
				<?php echo isset( $id ) ? SR_Helper::get_rooms_of_room_type( $id ) : SR_Helper::get_rooms_of_room_type(); ?>
				</tbody>
			</table>
		</div>
	</div>
</div>