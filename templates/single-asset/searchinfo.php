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

$tzoffset = get_option( 'timezone_string' );
$tzoffset = $tzoffset == '' ? 'UTC' : $tzoffset;
$timezone = new DateTimeZone( $tzoffset );
$checkinFormatted = new DateTime( $checkin, $timezone);
$checkoutFormatted = new DateTime( $checkout, $timezone);

?>

<div id="availability-search">
	<?php if ( $checkin && $checkout && count($room_types) > 0 ) :

		?>
		<div class="alert alert-info availability-search-info">
			<?php
			if ($asset->roomsOccupancyOptionsAdults == 0 && $asset->roomsOccupancyOptionsChildren == 0) :
				echo sprintf( __( 'We found %s rooms that matched your search from %s to %s.', 'solidres' ),
					$asset->totalAvailableRoom,
					$checkinFormatted->format($date_format) ,
					$checkoutFormatted->format($date_format)
				);
			else :
				if ($asset->totalOccupancyMax >= ($asset->roomsOccupancyOptionsAdults + $asset->roomsOccupancyOptionsChildren)) :
					if ($asset->totalAvailableRoom >= $asset->roomsOccupancyOptionsCount) :
						echo sprintf( __( 'We found %s rooms that matched your search from %s to %s for %s adult(s) and %s child(ren).', 'solidres' ),
							$asset->totalAvailableRoom,
							$checkinFormatted->format($date_format) ,
							$checkoutFormatted->format($date_format),
							$asset->roomsOccupancyOptionsAdults,
							$asset->roomsOccupancyOptionsChildren
						);
					else:
						echo sprintf( __( 'We have less than your number of requested rooms, but our current available rooms (%s) could satisfy your search from %s to %s for %s adult(s) and %s child(ren) if you select a different number of rooms.', 'solidres' ),
							$asset->totalAvailableRoom,
							$checkinFormatted->format($date_format) ,
							$checkoutFormatted->format($date_format),
							$asset->roomsOccupancyOptionsAdults,
							$asset->roomsOccupancyOptionsChildren
						);
					endif;
				else :
					echo sprintf( __( 'Sorry but our rooms are not available for your search from %s to %s for %s adult(s) and %s child(ren).', 'solidres' ),
						$checkinFormatted->format($date_format) ,
						$checkoutFormatted->format($date_format),
						$asset->roomsOccupancyOptionsAdults,
						$asset->roomsOccupancyOptionsChildren
					);

				endif;
			endif;
			?>
			<a class=""
			   href="<?php echo esc_attr( home_url( '/' . $post->post_name . '?startover=1' ) ) ?>"><i
					class="fa fa-refresh"></i> <?php _e( 'Reset', 'solidres' ) ?></a>
		</div>
	<?php endif; ?>

	<form id="sr-checkavailability-form-component"
		  action="<?php echo esc_url( home_url( $post->post_name ) ) ?>"
		  method="GET"
		>
		<input name="id" value="<?php echo $asset->id ?>" type="hidden"/>

		<input type="hidden"
			   name="checkin"
			   value="<?php //echo isset( $checkin ) ? $checkin : $dateCheckIn->add( new DateInterval( 'P' . ( $this->minDaysBookInAdvance ) . 'D' ) )->setTimezone( $this->timezone )->format( 'd-m-Y', true ) ?>"
			/>

		<input type="hidden"
			   name="checkout"
			   value="<?php //echo isset( $checkout ) ? $checkout : $dateCheckOut->add( new DateInterval( 'P' . ( $this->minDaysBookInAdvance + $this->minLengthOfStay ) . 'D' ) )->setTimezone( $this->timezone )->format( 'd-m-Y', true ) ?>"
			/>
		<input type="hidden" name="ts" value=""/>
	</form>
</div>