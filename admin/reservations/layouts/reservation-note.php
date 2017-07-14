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

$get_reservation_notes = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_reservation_notes WHERE reservation_id = %d", $id ) );
?>
<div id="reservation_note" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Reservation Note', 'solidres' ); ?></span></h3>

	<div class="inside">
		<?php if ( ! current_user_can( 'solidres_user' ) ) { ?>
			<div class="reservation_note">
				<form id="reservationnote_form" action="" method="POST">
					<textarea rows="5" name="text" class="srform_textarea reservation_note_text"
					          placeholder="<?php _e( 'Type your message here', 'solidres' ); ?>"></textarea>

					<div class="clr"></div>
					<label class="notify_checkbox"><input type="checkbox" name="notify_customer" id="notify_customer"
					                                      value=""><?php _e( 'Notify customer via email', 'solidres' ); ?>
					</label><br/>
					<label class="visible_in_frontend_checkbox"><input type="checkbox" name="visible_in_frontend"
					                                                   id="visible_in_frontend"
					                                                   value=""><?php _e( 'Display in frontend', 'solidres' ); ?>
					</label>

					<div class="clr"></div>
					<div class="processing nodisplay"></div>
					<input name="reservation_id" type="hidden" value="<?php echo $_GET['id']; ?>" id="reservation_id"/>
					<button type="submit" name="submit_note_btn"
					        class="srform_button button button-primary button-large submit_note_btn"><?php _e( 'Submit', 'solidres' ); ?></button>
				</form>
			</div>
		<?php } ?>
		<div class="reservation_note_group">
			<?php foreach ( $get_reservation_notes as $reservation_note ) {
				$created_name = get_the_author_meta( 'display_name', $reservation_note->created_by );
				?>
				<div class="reservation_note_item">
					<p class="info"><?php echo $reservation_note->created_date; ?> by <?php echo $created_name; ?></p>
					<?php if ( ! current_user_can( 'solidres_user' ) ) { ?>
						<p><?php _e( 'Notify customer via email:', 'solidres' ); ?> <?php echo $reservation_note->notify_customer == 1 ? 'Yes' : 'No'; ?>
							| <?php _e( 'Display in frontend:', 'solidres' ); ?> <?php echo $reservation_note->visible_in_frontend == 1 ? 'Yes' : 'No'; ?></p>
					<?php } ?>
					<p><?php echo $reservation_note->text; ?></p>
				</div>
			<?php }
			?>
		</div>
		<div class="clr"></div>
	</div>
</div>