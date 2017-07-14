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

$is_guest_making_reservation = solidres()->session->get( 'sr_is_guest_making_reservation' );

?>
<form
	id="sr-reservation-form-confirmation"
	class=""
	action="<?php echo get_site_url() . '/' . get_page_uri( $display_data['page_id'] ) ?>"
	method="POST">

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row button-row button-row-top">
		<div class="<?php echo SR_UI_GRID_COL_8 ?> eight columns">
			<div class="inner">
				<?php if ( $is_guest_making_reservation ) : ?>
				<p><?php _e( 'Please review the your reservation details and click on the Finish button to complete your reservation. A confirmation email will be sent to your given email address.', 'solidres' ) ?></p>
				<?php endif ?>
			</div>
		</div>
		<div class="<?php echo SR_UI_GRID_COL_4 ?> four columns">
			<div class="inner">
				<div class="btn-group">
					<button type="button" class="btn btn-default reservation-navigate-back" data-step="confirmation"
					        data-prevstep="guestinfo">
						<i class="fa fa-arrow-left"></i> <?php _e( 'Back', 'solidres' ) ?>
					</button>
					<button <?php echo $is_guest_making_reservation ? 'disabled' : '' ?> data-step="confirmation" type="submit" class="btn btn-default btn-success">
						<i class="fa fa-check"></i> <?php _e( 'Finish', 'solidres' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
		<div class="<?php echo SR_UI_GRID_COL_12 ?> twelve columns">
			<div class="inner">
				<div id="reservation-confirmation-box">
					<?php if ( $is_guest_making_reservation ) : ?>
					<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
						<div class="<?php echo SR_UI_GRID_COL_6 ?> six columns">
							<strong>
								<?php
								$checkin = new DateTime( $display_data['checkin'], $display_data['timezone'] );
								echo __( 'Checkin:', 'solidres' ) . ' ' . $checkin->format( $display_data['dateFormat'] ); ?>
							</strong>
						</div>
						<div class="<?php echo SR_UI_GRID_COL_6 ?>">
							<strong>
								<?php echo __( 'Your full name:', 'solidres' ) . ' ' . $display_data['reservation_details_guest']['customer_firstname'] . ' ' . $display_data['reservation_details_guest']['customer_lastname']; ?>
							</strong>
						</div>
					</div>
					<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
						<div class="<?php echo SR_UI_GRID_COL_6 ?> six columns">
							<strong>
								<?php
								$checkout = new DateTime( $display_data['checkout'], $display_data['timezone'] );
								echo __( 'Checkout:', 'solidres' ) . ' ' . $checkout->format( $display_data['dateFormat'] ); ?>
							</strong>
						</div>
						<div class="<?php echo SR_UI_GRID_COL_6 ?> six columns">
							<strong>
								<?php echo __( 'Your email:', 'solidres' ) . ' ' . $display_data['reservation_details_guest']['customer_email']; ?>
							</strong>
						</div>
					</div>
					<?php endif ?>

					<table class="table table-bordered">
						<tbody>
						<?php
						// Room cost
						foreach ( $display_data['roomTypes'] as $roomTypeId => $roomTypeDetails ) :
							foreach ( $roomTypeDetails['rooms'] as $tariffId => $roomDetails ) :
								foreach ( $roomDetails as $roomIndex => $cost ) :

									$roomInfo = $display_data['reservation_details_room']['room_types'][ $roomTypeId ][ $tariffId ][ $roomIndex ];
									if (isset($roomInfo['extras'])) :
										$roomInfoExtras = $roomInfo['extras'];
										if ( isset( $roomInfoExtras ) && is_array( $roomInfoExtras ) ) :
											foreach ( $roomInfoExtras as $extraItemKey => $extraItemDetails ) :
												$extraList[ $roomTypeId ][ $tariffId ][ $roomIndex ]['extras'][ $extraItemKey ]['room_type_name'] = $roomTypeDetails['name'];
												$extraList[ $roomTypeId ][ $tariffId ][ $roomIndex ]['extras'][ $extraItemKey ]['name']           = $extraItemDetails['name'];
												$extraList[ $roomTypeId ][ $tariffId ][ $roomIndex ]['extras'][ $extraItemKey ]['quantity']       = $extraItemDetails['quantity'];
												$extraList[ $roomTypeId ][ $tariffId ][ $roomIndex ]['extras'][ $extraItemKey ]['currency']       = clone $display_data['currency'];
												$extraList[ $roomTypeId ][ $tariffId ][ $roomIndex ]['extras'][ $extraItemKey ]['currency']->set_value( $extraItemDetails['total_extra_cost_tax_excl'] );
												$extraList[ $roomTypeId ][ $tariffId ][ $roomIndex ]['extras'][ $extraItemKey ]['currency_tax']   = clone $display_data['currency'];
												$extraList[ $roomTypeId ][ $tariffId ][ $roomIndex ]['extras'][ $extraItemKey ]['currency_tax']->set_value( $extraItemDetails['total_extra_cost_tax_incl'] - $extraItemDetails['total_extra_cost_tax_excl'] );
											endforeach;
										endif;
									endif;
									?>
									<tr>
										<td>
											<?php echo __( 'Room', 'solidres' ) . ': ' . apply_filters( 'solidres_roomtype_name', $roomTypeDetails["name"] ) ?>
											<a href="javascript:void(0)" class="toggle_room_confirmation"
											   data-target="<?php echo $roomTypeId ?>_<?php echo $tariffId ?>_<?php echo $roomIndex ?>">
												<?php _e( 'Details', 'solidres' ); ?>
											</a>
											<p><?php echo ! empty( $cost['currency']['title'] ) ? '(' . apply_filters( 'solidres_tariff_title', $cost['currency']['title'] ) . ')' : '' ?></p>
											<ul id="rc_<?php echo $roomTypeId ?>_<?php echo $tariffId ?>_<?php echo $roomIndex ?>"
											    style="display: none">
												<li><?php echo __( 'Guest name:', 'solidres' ) . ' ' . $roomInfo['guest_fullname']?></li>
												<li><?php echo __( 'Adult number:', 'solidres' ) . ' ' . $roomInfo['adults_number']?></li>
												<li><?php echo __( 'Child number:', 'solidres' ) . ' ' . ( ! empty( $roomInfo['children_number'] ) ? $roomInfo['children_number'] : 0 )?></li>
											</ul>
										</td>
										<td>
											<?php
											if ( 0 == $display_data[ 'booking_type' ]) :
												printf( _n( '%d night', '%d nights', $display_data['stay_length'], 'solidres' ), $display_data['stay_length'] );
											else :
												printf( _n( '%d day', '%d days', $display_data['stay_length'] + 1, 'solidres' ), $display_data['stay_length'] + 1 );
											endif;
											?>
										</td>
										<td class="sr-align-right">
											<?php if (!$is_guest_making_reservation) : ?>
												<div class="input-prepend">
													<span class="add-on"><?php echo __( 'Price', 'solidres' ) . '(' . $cost['currency']['total_price_tax_excl_formatted']->getCode() . ')' ?></span>
													<input type="text"
													       class="total_price_tax_excl_single_line"
													       value="<?php echo $cost['currency']['total_price_tax_excl_formatted']->getValue(); ?>"
													       name="srform[override_cost][room_types][<?php echo $roomTypeId ?>][<?php echo $tariffId ?>][<?php echo $roomIndex ?>][total_price_tax_excl]" />
												</div>
												<div class="input-prepend">
													<span class="add-on"><?php echo __( 'Tax', 'solidres' ) . '(' . $cost['currency']['total_price_tax_excl_formatted']->getCode() . ')' ?></span>
													<input type="text" class="room_price_tax_amount_single_line" value="<?php echo $cost['currency']['total_price_tax_incl_formatted']->getValue() - $cost['currency']['total_price_tax_excl_formatted']->getValue(); ?>" name="srform[override_cost][room_types][<?php echo $roomTypeId ?>][<?php echo $tariffId ?>][<?php echo $roomIndex ?>][tax_amount]" />
												</div>
											<?php else : ?>
												<?php echo $cost['currency']['total_price_tax_excl_formatted']->format() ?>
											<?php endif ?>
										</td>
									</tr>
								<?php
								endforeach;
							endforeach;
						endforeach;

						// Total room cost
						$totalRoomCost = new SR_Currency( $display_data['cost']['total_price_tax_excl'], $display_data['currency_id'] );
						?>
						<tr class="nobordered first">
							<td colspan="2" class="sr-align-right">
								<?php _e( 'Total room cost (exclude taxes)', 'solidres' ) ?>
							</td>
							<td class="sr-align-right">
								<?php if (!$is_guest_making_reservation) : ?>
									<span class="add-on"><?php echo $totalRoomCost->getCode() ?></span>
									<span class="total_price_tax_excl grand_total_sub"><?php echo $totalRoomCost->getValue() ?></span>
								<?php else : ?>
									<?php echo $totalRoomCost->format() ?>
								<?php endif ?>
							</td>
						</tr>

						<?php
						// In case of pre tax discount
						if ($display_data['cost']['total_discount'] > 0 && $display_data['isDiscountPreTax']) :
							$totalDiscount = new SR_Currency($display_data['cost']['total_discount'], $display_data['currency_id']);
							?>
							<tr class="nobordered">
								<td colspan="2" class="sr-align-right">
									<?php _e( 'Total discount', 'solidres' ) ?>
								</td>
								<td class="sr-align-right noleftborder">
									<?php if (!$is_guest_making_reservation) : ?>
									<div class="input-prepend">
										<span class="add-on"><?php echo $totalDiscount->getCode() ?></span>
										<input type="text" value="<?php echo '-' . $totalDiscount->getValue() ?>" name="srform[total_discount]" />
									</div>
									<?php else : ?>
										<?php echo '-' . $totalDiscount->format() ?>
									<?php endif ?>
								</td>
							</tr>
							<?php
						endif;

						// Imposed taxes
						$taxItem = new SR_Currency( $display_data['cost']['tax_amount'], $display_data['currency_id'] );
						?>
						<tr class="nobordered">
							<td colspan="2" class="sr-align-right">
								<?php _e( 'Total room tax', 'solidres' ) ?>
							</td>
							<td class="sr-align-right noleftborder">
								<?php if (!$is_guest_making_reservation) : ?>
									<span class="add-on"><?php echo $taxItem->getCode() ?></span>
									<span class="tax_amount grand_total_sub"><?php echo $taxItem->getValue() ?></span>
								<?php else : ?>
									<?php echo $taxItem->format() ?>
								<?php endif ?>
							</td>
						</tr>
						<?php

						// In case of after tax discount
						if ( $display_data['cost']['total_discount'] > 0 && ! $display_data['isDiscountPreTax'] ) :
							$totalDiscount = new SR_Currency( $display_data['cost']['total_discount'], $display_data['currency_id'] );
							?>
							<tr class="nobordered">
								<td colspan="2" class="sr-align-right">
									<?php _e( 'Total discount', 'solidres' ) ?>
								</td>
								<td class="sr-align-right noleftborder">
									<?php if (!$is_guest_making_reservation) : ?>
									<div class="input-prepend">
										<span class="add-on"><?php echo $totalDiscount->getCode() ?></span>
										<input type="text" value="<?php echo '-' . $totalDiscount->getValue() ?>" name="srform[total_discount]" />
									</div>
									<?php else : ?>
										<?php echo '-' . $totalDiscount->format(); ?>
									<?php endif ?>
								</td>
							</tr>
						<?php
						endif;

						// Per room extra list
						if ( ! empty( $extraList ) ) :
							foreach ( $extraList as $extraRoomTypeId => $extraRoomTypeTariffs ) :
								foreach ( $extraRoomTypeTariffs as $extraTariffId => $extraRooms ) :
									foreach ( $extraRooms as $extraRoomIndex => $extraRoomExtras ) :
										foreach ( $extraRoomExtras as $extraRoomExtraKey => $extraRoomExtraDetails ) :
											foreach ( $extraRoomExtraDetails as $extraRoomExtraId => $extraRoomExtraIdDetails ) :
												?>
												<tr class="extracost_confirmation" style="display: none">
													<td>
														<p>
															<?php echo __( 'Extra:', 'solidres' ) . ' ' . apply_filters( 'solidres_extra_name', $extraRoomExtraIdDetails['name'] ) ?>
														</p>

														<p>
															<?php echo __( 'Room:', 'solidres' ) . ' ' . apply_filters( 'solidres_roomtype_name', $extraRoomExtraIdDetails['room_type_name'] )?>
														</p>
													</td>
													<td>
														<?php echo $extraRoomExtraIdDetails['quantity'] ?>
													</td>
													<td class="sr-align-right ">
														<?php if (!$is_guest_making_reservation) : ?>
														<div class="input-prepend">
															<span class="add-on"><?php echo __( 'Price', 'solidres' ) . '(' . $extraRoomExtraIdDetails['currency']->getCode() . ')' ?></span>
															<input class="extra_price_single_line" type="text" value="<?php echo $extraRoomExtraIdDetails['currency']->getValue() ?>" name="srform[override_cost][room_types][<?php echo $extraRoomTypeId ?>][<?php echo $extraTariffId ?>][<?php echo $extraRoomIndex ?>][extras][<?php echo $extraRoomExtraId ?>][price]" />
														</div>
														<div class="input-prepend">
															<span class="add-on"><?php echo __( 'Tax', 'solidres' ) . '(' . $extraRoomExtraIdDetails['currency_tax']->getCode() . ')' ?></span>
															<input class="extra_tax_single_line" type="text" value="<?php echo $extraRoomExtraIdDetails['currency_tax']->getValue() ?>" name="srform[override_cost][room_types][<?php echo $extraRoomTypeId ?>][<?php echo $extraTariffId ?>][<?php echo $extraRoomIndex ?>][extras][<?php echo $extraRoomExtraId ?>][tax_amount]" />
														</div>
														<?php else : ?>
															<?php echo $extraRoomExtraIdDetails['currency']->format() ?>
														<?php endif ?>
													</td>
												</tr>
											<?php
											endforeach;
										endforeach;
									endforeach;
								endforeach;
							endforeach;
						endif;

						// Per booking extra list
						$perBookingExtraList = isset( $display_data['reservation_details_guest']['extras'] ) ? $display_data['reservation_details_guest']['extras'] : array();

						foreach ( $perBookingExtraList as $perBookingExtraId => $perBookingExtraDetails ) :
							?>
							<tr class="extracost_confirmation" style="display: none">
								<td>
									<p>
										<?php echo __( 'Extra:', 'solidres' ) . ' ' . apply_filters( 'solidres_extra_name', $perBookingExtraDetails['name'] ) ?>
									</p>

									<p>
										<?php _e( 'Per booking', 'solidres' ); ?>
									</p>
								</td>
								<td>
									<?php echo $perBookingExtraDetails['quantity'] ?>
								</td>
								<td class="sr-align-right ">
									<?php
									$perBookingExtraCurrency = clone $display_data['currency'];
									$perBookingExtraCurrency->set_value( $perBookingExtraDetails['total_extra_cost_tax_excl'] );
									$perBookingExtraCurrencyTax = clone $display_data['currency'];
									$perBookingExtraCurrencyTax->set_value( $perBookingExtraDetails['total_extra_cost_tax_incl'] - $perBookingExtraDetails['total_extra_cost_tax_excl'] );
									?>
									<?php if (!$is_guest_making_reservation) : ?>
									<div class="input-prepend">
										<span class="add-on"><?php echo __( 'Price', 'solidres' ) . '(' . $perBookingExtraCurrency->getCode() . ')' ?></span>
										<input class="extra_price_single_line" type="text" value="<?php echo $perBookingExtraCurrency->getValue() ?>" name="srform[override_cost][extras_per_booking][<?php echo $perBookingExtraId ?>][price]" />
									</div>
									<div class="input-prepend">
										<span class="add-on"><?php echo __( 'Tax', 'solidres' ) . '(' . $perBookingExtraCurrencyTax->getCode() . ')' ?></span>
										<input class="extra_tax_single_line" type="text" value="<?php echo $perBookingExtraCurrencyTax->getValue() ?>" name="srform[override_cost][extras_per_booking][<?php echo $perBookingExtraId ?>][tax_amount]" />
									</div>
									<?php else : ?>
										<?php echo $perBookingExtraCurrency->format() ?>
									<?php endif ?>
								</td>
							</tr>
						<?php
						endforeach;

						// Extra cost
						$totalExtraCostTaxExcl   = new SR_Currency( $display_data['totalRoomTypeExtraCostTaxExcl'], $display_data['currency_id'] );
						$totalExtraCostTaxAmount = new SR_Currency( $display_data['totalRoomTypeExtraCostTaxIncl'] - $display_data['totalRoomTypeExtraCostTaxExcl'], $display_data['currency_id'] );
						if ( $totalExtraCostTaxExcl->getValue() > 0 ) : ?>
							<tr class="nobordered extracost_row">
								<td colspan="2" class="sr-align-right">
									<a href="javascript:void(0)" class="toggle_extracost_confirmation">
										<?php _e( 'Total extra cost (exclude taxes)', 'solidres' ) ?>
									</a>
								</td>
								<td id="total-extra-cost" class="sr-align-right noleftborder">
									<?php if (!$is_guest_making_reservation) : ?>
									<span class="add-on"><?php echo $totalExtraCostTaxExcl->getCode() ?></span>
									<span class="total_extra_price grand_total_sub"><?php echo $totalExtraCostTaxExcl->getValue() ?></span>
									<?php else : ?>
										<?php echo $totalExtraCostTaxExcl->format() ?>
									<?php endif ?>
								</td>
							</tr>
							<tr class="nobordered">
								<td colspan="2" class="sr-align-right">
									<?php _e( 'Total extra tax', 'solidres' ) ?>
								</td>
								<td id="total-extra-cost" class="sr-align-right noleftborder">
									<?php if (!$is_guest_making_reservation) : ?>
										<span class="add-on"><?php echo $totalExtraCostTaxAmount->getCode() ?></span>
										<span class="total_extra_tax grand_total_sub"><?php echo $totalExtraCostTaxAmount->getValue() ?></span>
									<?php else : ?>
										<?php echo $totalExtraCostTaxAmount->format() ?>
									<?php endif ?>
								</td>
							</tr>

						<?php endif;
						// Grand total cost
						if ( $display_data[ 'isDiscountPreTax' ]) :
							$grandTotal = new SR_Currency( $display_data['cost']['total_price_tax_excl_discounted'] + $display_data['cost']['tax_amount'] + $display_data['totalRoomTypeExtraCostTaxIncl'], $display_data[ 'currency_id' ] );
						else :
							$grandTotal = new SR_Currency( $display_data['cost']['total_price_tax_excl'] + $display_data['cost']['tax_amount'] - $display_data['cost']['total_discount'] + $display_data['totalRoomTypeExtraCostTaxIncl'], $display_data['currency_id'] );
						endif;

						?>
						<tr class="nobordered">
							<td colspan="2" class="sr-align-right">
								<strong><?php _e( 'Grand Total', 'solidres' ) ?></strong>
							</td>
							<td class="sr-align-right gra noleftborder">
								<?php if (!$is_guest_making_reservation) : ?>
									<span class="add-on"><?php echo $totalExtraCostTaxExcl->getCode() ?></span>
									<span class="grand_total"><?php echo $grandTotal->getValue() ?></span>
								<?php else : ?>
								<strong><?php echo $grandTotal->format() ?></strong>
								<?php endif ?>
							</td>
						</tr>

						<?php
						// Deposit amount, if enabled
						$isDepositRequired = $display_data['deposit_required'];

						if ( $isDepositRequired ) :
							$depositAmountTypeIsPercentage    = $display_data['deposit_is_percentage'];
							$depositIncludeExtraCost = $display_data['deposit_include_extra_cost'];
							if ( solidres()->session->get( 'sr_deposit_amount_by_stay_length' ) <= 0 ) :
								$depositAmount = $display_data['deposit_amount'];
								$depositTotal  = $depositAmount;
								if ( $depositAmountTypeIsPercentage ) :
									$depositTotal = $display_data['cost']['total_price_tax_excl_discounted'] + $display_data['cost']['tax_amount'];
									if ($depositIncludeExtraCost) :
										$depositTotal += $display_data['totalRoomTypeExtraCostTaxIncl'];
									endif;
									$depositTotal = $depositTotal * ( $depositAmount / 100 );
								endif;
							else :
								$depositTotal = solidres()->session->get( 'sr_deposit_amount_by_stay_length' );
							endif;
							$depositTotalAmount = new SR_Currency( $depositTotal, $display_data['currency_id'] );
							?>
							<tr class="nobordered">
								<td colspan="2" class="sr-align-right">
									<strong><?php _e( 'Deposit amount', 'solidres' ) ?></strong>
								</td>
								<td class="sr-align-right gra noleftborder">
									<?php if (!$is_guest_making_reservation) : ?>
										<div class="input-prepend">
											<span class="add-on"><?php echo $depositTotalAmount->getCode() ?></span>
											<input type="text" value="<?php echo $depositTotalAmount->getValue() ?>" name="srform[override_cost][deposit_amount]" />
										</div>
									<?php else : ?>
									<strong><?php echo $depositTotalAmount->format() ?></strong>
									<?php endif ?>
								</td>
							</tr>
							<?php
							solidres()->session->set( 'sr_deposit', array( 'deposit_amount' => $depositTotal ) );
						endif;

						// Terms and conditions
						if ( $is_guest_making_reservation ) :
						$bookingConditionsLink = get_permalink( $display_data['asset_params']['termsofuse'] );
						$privacyPolicyLink     = get_permalink( $display_data['asset_params']['privacypolicy'] );
						?>
						<tr class="nobordered">
							<td colspan="3">
								<p>
									<input type="checkbox" id="termsandconditions" data-target="finalbutton"/>
									<?php _e( 'I agree with ', 'solidres' ) ?>
									<a target="_blank"
									   href="<?php echo $bookingConditionsLink ?>"><?php _e( 'Booking conditions', 'solidres' ) ?></a> <?php _e( 'and', 'solidres' ) ?>
									<a target="_blank"
									   href="<?php echo $privacyPolicyLink ?>"><?php _e( 'Privacy Policy', 'solidres' ) ?></a>
								</p>
							</td>
						</tr>
						<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
			<input type="hidden" name="id" value="<?php echo $display_data['assetId'] ?>"/>
			<input type="hidden" name="task" value="save_reservation"/>
		</div>
	</div>

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row button-row button-row-bottom">
		<div class="<?php echo SR_UI_GRID_COL_8 ?> eight columns">
			<div class="inner">
				<?php if ( $is_guest_making_reservation ) : ?>
				<p><?php _e( 'Please review the your reservation details and click on the Finish button to complete your reservation. A confirmation email will be sent to your given email address.', 'solidres' ) ?></p>
				<?php endif ?>
			</div>
		</div>
		<div class="<?php echo SR_UI_GRID_COL_4 ?> four columns">
			<div class="inner">
				<div class="btn-group">
					<button type="button" class="btn btn-default reservation-navigate-back" data-step="confirmation"
					        data-prevstep="guestinfo">
						<i class="fa fa-arrow-left"></i> <?php _e( 'Back', 'solidres' ) ?>
					</button>
					<button <?php echo $is_guest_making_reservation ? 'disabled' : '' ?> data-step="confirmation" type="submit"
							class="btn btn-default btn-success">
						<i class="fa fa-check"></i> <?php _e( 'Finish', 'solidres' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</form>