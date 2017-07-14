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

<div id="reservation_other_infomation" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Other infomartion', 'solidres' ); ?></span></h3>

	<?php
	$extras = $reservations->load_extras( $id );
	if ( isset( $extras ) && !empty( $extras ) ) :
	?>
	<div class="inside reservation-detail-box">
		<?php
			echo '
						<table class="form-table reservation_other_info">
							<thead class="sr_table_bordered">
								<th>' . __( 'Name', 'solidres' ) . '</th>
								<th>' . __( 'Quantity', 'solidres' ) . '</th>
								<th>' . __( 'Price', 'solidres' ) . '</th>
							</thead>
							<tbody>
											';
			foreach ( $extras as $extra ) :
				echo '<tr>';
				?>
				<td><?php echo apply_filters( 'solidres_extra_name', $extra->extra_name ) ?></td>
				<td><?php echo $extra->extra_quantity ?></td>
				<td>
					<?php
					$extraPriceCurrencyPerBooking = clone $baseCurrency;
					$extraPriceCurrencyPerBooking->set_value( $extra->extra_price );
					echo $extraPriceCurrencyPerBooking->format();
					?>
				</td>
				<?php
				echo '</tr>';
			endforeach;
			echo '
							</tbody>
						</table>';
			?>
	</div>
	<?php endif; ?>
</div>