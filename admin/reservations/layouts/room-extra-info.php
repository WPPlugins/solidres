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

$get_reservation_room_xref = $wpdb->get_results( $wpdb->prepare( "SELECT x.*, rtype.id as room_type_id, rtype.name as room_type_name, room.label as room_label FROM {$wpdb->prefix}sr_reservation_room_xref as x INNER JOIN {$wpdb->prefix}sr_rooms as room ON room.id = x.room_id INNER JOIN {$wpdb->prefix}sr_room_types rtype ON rtype.id = room.room_type_id WHERE reservation_id = %d", $id ) );

?>

<div id="reservation_room_extra_info" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Room/Extra info', 'solidres' ); ?></span></h3>

	<div class="inside reservation-detail-box">
		<?php if ( ! empty( $get_reservation_room_xref ) ) : ?>
			<table class="form-table extra_rooms_listview">
				<tbody>
				<tr class="sr_table_bordered">
					<th><?php _e( 'Room type name', 'solidres' ); ?></th>
					<th><?php _e( 'Room number', 'solidres' ); ?></th>
					<th><?php _e( 'Room occupancy', 'solidres' ); ?></th>
					<th><?php _e( 'Room cost (tax incl)', 'solidres' ); ?></th>
					<th><?php _e( 'Extras', 'solidres' ); ?></th>
				</tr>
				<?php foreach ( $get_reservation_room_xref as $reservation_room ) :
					$room_price_tax_incl = clone $baseCurrency;
					$room_price_tax_incl->set_value( isset ( $reservation_room->room_price_tax_incl ) ? $reservation_room->room_price_tax_incl : 0 );
					$get_extras_of_room = $wpdb->get_results( $wpdb->prepare( "SELECT x.*, extra.id, extra.name as extra_name FROM {$wpdb->prefix}sr_reservation_room_extra_xref as x INNER JOIN {$wpdb->prefix}sr_extras as extra ON extra.id = x.extra_id WHERE reservation_id = %d AND room_id = %d", $id, $reservation_room->room_id ) );
					$reservation_room->other_info = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_reservation_room_details WHERE reservation_room_id = %d", $reservation_room->id ) );
					?>
					<tr>
						<td>
							<?php
							$roomTypeLink = 'admin.php?page=sr-room-types&action=edit&id=' . $reservation_room->room_type_id;
							echo '<a href="' . $roomTypeLink . '">' . apply_filters( 'solidres_roomtype_name', $reservation_room->room_type_name ) . '</a>';
							?>
						</td>
						<td>
							<p><?php echo $reservation_room->room_label; ?></p>

							<p><?php _e( 'Guest fullname:', 'solidres' ); ?> <?php echo $reservation_room->guest_fullname; ?></p>

							<p>
								<?php
								if (is_array($reservation_room->other_info)) :
									foreach ($reservation_room->other_info as $info) :
										if (substr($info->key, 0, 7) == 'smoking') :
											echo __( 'Smoking', 'solidres' ) . ': ' . ($info->value == '' ? __( 'No preferences', 'solidres') : ($info->value == 1 ? __( 'Yes', 'solidres' ): __( 'No', 'solidres' ) )  ) ;
										endif;
									endforeach;
								endif
								?>
							</p>
						</td>
						<td>
							<p><?php _e( 'Adult number:', 'solidres' ); ?> <?php echo $reservation_room->adults_number; ?></p>
							<p><?php _e( 'Child number:', 'solidres' ); ?> <?php echo $reservation_room->children_number; ?></p>
							<?php
							if (is_array($reservation_room->other_info)) :
								foreach ($reservation_room->other_info as $info) :
									echo '<ul class="unstyled">';
									if (substr($info->key, 0, 5) == 'child') :
										echo '<li>' . __( 'Child ' . substr($info->key, 5), 'solidres') . ': ' . sprintf( _n( '%d year old', '%d years old', 'solidres', $info->value), $info->value) .'</li>';
									endif;
									echo '</ul>';
								endforeach;
							endif;
							?>
						</td>
						<td><?php echo $room_price_tax_incl->format(); ?></td>
						<td rowspan="2">
							<?php if ( ! empty( $get_extras_of_room ) ) : ?>
								<table class="form-table room_extra_table">
									<thead>
									<tr>
										<th><?php _e( 'Name', 'solidres' ); ?></th>
										<th><?php _e( 'Quantity', 'solidres' ); ?></th>
										<th><?php _e( 'Price', 'solidres' ); ?></th>
									</tr>
									</thead>
									<tbody>
									<?php foreach ( $get_extras_of_room as $get_extra_of_room ) :
										$extra_price = clone $baseCurrency;
										$extra_price->set_value( isset ( $get_extra_of_room->extra_price ) ? $get_extra_of_room->extra_price : 0 );
										?>
										<tr>
											<td><?php echo '<a href="admin.php?page=sr-extras&action=edit&id=' . $get_extra_of_room->extra_id . '">' . $get_extra_of_room->extra_name . '</a>' ?></td>
											<td><?php echo $get_extra_of_room->extra_quantity; ?></td>
											<td><?php echo $extra_price->format(); ?></td>
										</tr>
									<?php endforeach ?>
									</tbody>
								</table>
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
				</tbody>
			</table>
		<?php else :
			$message = __( 'No room available', 'solidres' );
			SR_Helper::show_message( $message, 'trashed' );
		endif ?>
	</div>
</div>