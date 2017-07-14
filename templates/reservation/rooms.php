<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Joomla
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//$isFrontEnd = JFactory::getApplication()->isSite();

$widthList = array(
	1 => 'one',
	2 => 'two',
	3 => 'three',
	4 => 'four',
	5 => 'five',
	6 => 'six',
	7 => 'seven',
	8 => 'eight',
	9 => 'nine',
	10 => 'ten',
	11 => 'eleven',
	12 => 'twelve'
);

?>

<form enctype="multipart/form-data"
	  id="sr-reservation-form-room"
	  class="sr-reservation-form"
	  action=""
	  method="POST">
	<?php
	foreach ( $display_data['room_types'] as $roomType ) :
		?>
		<h3>
			<span class="label label-info"><?php echo $roomType->occupancy_max > 0 ? $roomType->occupancy_max : (int)$roomType->occupancy_adult + (int)$roomType->occupancy_child ?><i class="dashicons dashicons-admin-users"></i></span> <?php echo apply_filters( 'solidres_roomtype_name', $roomType->name ) ?>
		</h3>
		<?php if ( ! empty( $roomType->rooms ) ) :
			$itemPerRow     = 2;
			$spanNum        = 12 / (int) $itemPerRow;
			$totalRoomCount = count( $roomType->rooms );
			for ( $count = 0; $count <= $totalRoomCount; $count++ ) :
				if ( $count % $itemPerRow == 0 && $count == 0 ) :
					echo '<div class="sr_row">';
				elseif ( $count % $itemPerRow == 0 && $count != $totalRoomCount ) :
					echo '</div><div class="sr_row">';
				elseif ( $count == $totalRoomCount ) :
					echo '</div>';
				endif;

				if ($count < $totalRoomCount) :
					$currentRoomIndex = NULL;
					$arrayHolder = 'xtariffidx';
					$room = $roomType->rooms[$count];
					if (isset($display_data['current_reservation_data']->reserved_room_details[$room->id])) :
						$currentRoomIndex = (array) $display_data['current_reservation_data']->reserved_room_details[$room->id];
						$arrayHolder = $currentRoomIndex['tariff_id'];
					endif;
					$identity = $roomType->id . '_' . (isset($currentRoomIndex['tariff_id']) ? $currentRoomIndex['tariff_id'] : $arrayHolder ) . '_' . $room->id;

					$checked  = '';
					$disabled = !$room->isAvailable && !$room->isReservedForThisReservation ? 'disabled' : '';

					if ( ! $room->isAvailable || $room->isReservedForThisReservation) :
						$checked = 'checked';
					endif;

					// Html for adult selection
					$htmlAdultSelection = '';
					$htmlAdultSelection .= '<option value="">' . __( 'Adult', 'solidres' ) . '</option>';

					for ( $j = 1; $j <= $roomType->occupancy_adult; $j ++ ) :
						$selected = '';
						if ( isset( $currentRoomIndex['adults_number'] ) ) :
							$selected = $currentRoomIndex['adults_number'] == $j ? 'selected' : '';
						else :
							if ( $j == 1 ) :
								$selected = 'selected';
							endif;
						endif;
						$htmlAdultSelection .= '<option ' . $selected . ' value="' . $j . '">' . sprintf( _n( '1 adult', '%s adults', $j, 'solidres' ), $j ) . '</option>';
					endfor;

					// Html for children selection
					$htmlChildSelection = '';
					$htmlChildrenAges   = '';
					if ( ! isset( $roomType->params['show_child_option'] ) ) :
						$roomType->params['show_child_option'] = 1;
					endif;

					// Only show child option if it is enabled and the child quantity > 0
					if ( $roomType->params['show_child_option'] == 1 && $roomType->occupancy_child > 0 ) :
						$htmlChildSelection .= '';
						$htmlChildSelection .= '<option value="">' . __( 'Child', 'solidres' ) . '</option>';

						for ( $j = 1; $j <= $roomType->occupancy_child; $j ++ ) :
							if ( isset( $currentRoomIndex['children_number'] ) ) :
								$selected = $currentRoomIndex['children_number'] == $j ? 'selected' : '';
							endif;
							$htmlChildSelection .= '
								<option ' . $selected . ' value="' . $j . '">' . sprintf( _n( '1 child', '%s children', $j, 'solidres' ), $j ) . '</option>
							';
						endfor;

						// Html for children ages
						// Restructure to match front end
						if (is_array($currentRoomIndex['other_info'])) :
							foreach ($currentRoomIndex['other_info'] as $info) :
								if (substr($info->key, 0, 5) == 'child') :
									$currentRoomIndex['children_ages'][] = $info->value;
								endif;
							endforeach;
						endif;

						if ( isset( $currentRoomIndex['children_ages'] ) ) :
							for ( $j = 0; $j < count( $currentRoomIndex['children_ages'] ); $j ++ ) :
								$htmlChildrenAges .= '
				<li>
					' . __( 'Child', 'solidres' ) . ' ' . ( $j + 1 ) . '
					<select name="srform[room_types][' . $roomType->id . ']['.$arrayHolder.'][' . $room->id . '][children_ages][]"
						data-raid="' . $display_data['raid'] . '"
						data-roomtypeid="' . $roomType->id . '"
						data-roomid="' . $room->id . '"
						class="twelve columns child_age_' . $roomType->id . '_'.$arrayHolder.'_' . $room->id . '_' . $j . ' trigger_tariff_calculating"
						required
					>';
								$htmlChildrenAges .= '<option value=""></option>';
								for ( $age = 1; $age <= $display_data['childMaxAge']; $age ++ ) :
									$selectedAge = '';
									if ( $age == $currentRoomIndex['children_ages'][ $j ] ) :
										$selectedAge = 'selected';
									endif;
									$htmlChildrenAges .= '<option ' . $selectedAge . ' value="' . $age . '">' . sprintf( _n( '1 year old', '%s years old', $age, 'solidres' ), $age ) . '</option>';
								endfor;

								$htmlChildrenAges .= '
					</select>
				</li>';
							endfor;
						endif;
					endif;
				?>
				<div class="<?php echo $widthList[$spanNum] ?> columns" id="room<?php echo $room->id ?>">
						<dl class="room_selection_wrapper">
							<dt>
								<label class="checkbox">
									<input type="checkbox"
										   value="<?php echo $room->id ?>"
										   class="reservation_room_select"
										   name="srform[reservation_room_select][]" <?php echo $checked ?> <?php echo $disabled ?> />
									<span class="label <?php echo $room->isReservedForThisReservation ? 'label-success' : '' ?>">
										<?php echo $room->label ?>
									</span>
								</label>
								<table class="table table-condensed table-bordered twelve columns"
								       style="<?php echo $room->isReservedForThisReservation ? '' : 'display: none;' ?>">
									<tbody>
										<tr>
											<td>
												<?php _e( 'Current', 'solidres' ) ?>
											</td>
											<td class="sr-align-right">
												<?php
												if ($room->isReservedForThisReservation) :
													$tmpCurrency = clone $display_data['currency'];
													$tmpCurrency->set_value($currentRoomIndex['room_price_tax_incl']);
													echo $tmpCurrency->format();
												else :
													echo 0;
												endif;
												?>
											</td>
										</tr>
										<tr>
											<td>
												<?php _e( 'New', 'solidres' ) ?>
											</td>
											<td class="sr-align-right">
												<a href="javascript:void(0)"
												   class="toggle_breakdown tariff_breakdown_<?php echo $room->id ?>"
												   data-target="<?php echo $roomType->id . '_'.$arrayHolder.'_' . $room->id ?>"
												   style="display: none"
													>
													<?php _e( 'Details', 'solidres' ) ?>
												</a>
												<span
													class="tariff_<?php echo $roomType->id . '_'.$arrayHolder.'_' . $room->id ?> tariff_breakdown_<?php echo $room->id ?>"
													style=""
													>
													0
												</span>
											</td>
										</tr>
									</tbody>
								</table>
								<span style="display: none"
									  class="breakdown"
									id="breakdown_<?php echo $roomType->id . '_'.$arrayHolder.'_' . $room->id ?>">

								</span>
							</dt>
							<dd class="room_selection_details" id="room_selection_details_<?php echo $room->id ?>"
								style="<?php echo $room->isReservedForThisReservation ? '' : 'display: none;' ?>">
								<div class="sr_row">
									<div class="six columns">
										<select
											name="srform[ignore]"
											data-roomid="<?php echo $room->id ?>"
											class="twelve columns tariff_selection" <?php echo $room->isReservedForThisReservation ? '' : 'disabled' ?>
											<?php echo $room->isReservedForThisReservation ? '' : 'required' ?>
										>
											<option value=""><?php _e( 'Choose a tariff', 'solidres' ) ?></option>
											<?php
											foreach ( $roomType->availableTariffs as $tariffKey => $tariffInfo ) :
												$selected_tariff = '';
												if (isset($currentRoomIndex['tariff_id']) && $tariffKey == $currentRoomIndex['tariff_id']) :
													//$selected_tariff = 'selected';
												endif;
												?>
												<option data-adjoininglayer="<?php echo $tariffInfo['tariffAdjoiningLayer'] ?>"
													<?php echo $selected_tariff ?>
													    value="<?php echo $tariffKey ?>"
												>
													<?php echo empty( $tariffInfo['tariffTitle'] ) ? __( "Standard rate", 'solidres' ) : apply_filters( 'solidres_tariff_title', $tariffInfo['tariffTitle'] ) ?>
												</option>
											<?php endforeach ?>
										</select>
									</div>
									<div class="six columns">
										<input type="text"
										       name="srform[room_types][<?php echo $roomType->id ?>][<?php echo $arrayHolder ?>][<?php echo $room->id ?>][guest_fullname]"
										       class="twelve columns guest_fullname" placeholder="<?php _e( "Guest name", 'solidres' ) ?>"
										       value="<?php echo $currentRoomIndex['guest_fullname'] ?>"
											<?php echo $room->isReservedForThisReservation ? '' : 'disabled' ?>
										/>
									</div>
								</div>

								<div class="sr_row">
									<div class="six columns">
										<select
											data-roomtypeid="<?php echo $roomType->id ?>"
											data-tariffid="<?php echo isset($currentRoomIndex['tariff_id']) ? $currentRoomIndex['tariff_id'] : ''?>"
											data-adjoininglayer=""
											data-roomid="<?php echo $room->id ?>"
											data-max="<?php echo $roomType->occupancy_max ?>"
											name="srform[room_types][<?php echo $roomType->id ?>][<?php echo $arrayHolder ?>][<?php echo $room->id ?>][adults_number]"
											required
											data-identity="<?php echo $identity ?>"
											class="twelve columns adults_number occupancy_max_constraint occupancy_max_constraint_<?php echo $room->id ?>_<?php echo $arrayHolder ?>_<?php echo $roomType->id ?> occupancy_adult_<?php echo $roomType->id . '_' . $arrayHolder . '_' . $room->id ?> trigger_tariff_calculating"
											<?php echo $room->isReservedForThisReservation ? '' : 'disabled' ?>
										>
											<?php echo $htmlAdultSelection ?>
										</select>
									</div>
									<div class="six columns">
										<?php if ( $roomType->params['show_child_option'] == 1 && $roomType->occupancy_child > 0 ) : ?>
											<select
												data-roomtypeid="<?php echo $roomType->id ?>"
												data-tariffid="<?php echo isset($currentRoomIndex['tariff_id']) ? $currentRoomIndex['tariff_id'] : ''?>"
												data-adjoininglayer=""
												data-roomid="<?php echo $room->id ?>"
												data-max="<?php echo $roomType->occupancy_max ?>"
												data-identity="<?php echo $identity ?>"
												name="srform[room_types][<?php echo $roomType->id ?>][<?php echo $arrayHolder ?>][<?php echo $room->id ?>][children_number]"
												class="twelve columns children_number occupancy_max_constraint occupancy_max_constraint_<?php echo $room->id ?>_<?php echo $arrayHolder ?>_<?php echo $roomType->id ?> reservation-form-child-quantity trigger_tariff_calculating occupancy_child_<?php echo $roomType->id . '_' . $arrayHolder . '_' . $room->id ?>"
												<?php echo $room->isReservedForThisReservation ? '' : 'disabled' ?>
											>
												<?php echo $htmlChildSelection ?>
											</select>
											<div
												class="twelve columns child-age-details <?php echo(empty($htmlChildrenAges) ? 'nodisplay' : '') ?>">
												<p><?php _e( 'Age of child(ren) at checkout', 'solidres' ) ?></p>
												<ul class="unstyled"><?php echo $htmlChildrenAges ?></ul>
											</div>
										<?php endif ?>
									</div>
								</div>

								<div class="sr_row">
									<div class="twelve columns">
										<ul class="unstyled">
											<?php
											foreach ( $roomType->extras as $extra ) :
												$extraInputCommonName = 'srform[room_types][' . $roomType->id . ']['.$arrayHolder.'][' . $room->id . '][extras][' . $extra->id . ']';
												$checked              = '';
												$disabledCheckbox     = '';
												$disabledSelect       = 'disabled="disabled"';
												$alreadySelected      = false;
												$canBeEnabled		  = true;
												if ( isset( $currentRoomIndex['extras'] ) ) :
													$alreadySelected = array_key_exists( $extra->id, (array) $currentRoomIndex['extras'] );
												endif;

												if ( $extra->mandatory == 1 || $alreadySelected ) :
													$checked = 'checked="checked"';
												endif;

												if ( $extra->mandatory == 1 ) :
													$disabledCheckbox = ''; // dont force mandatory for admin
													$canBeEnabled = false;
													//$disabledSelect   = ''; // dont force mandatory for admin
												endif;

												if ( $alreadySelected) :
													$disabledSelect = '';
												endif;
												?>
												<li class="extras_row_roomtypeform">
													<input <?php echo $checked ?> <?php echo $disabledCheckbox ?>
														type="checkbox" class="<?php echo $canBeEnabled ? '' : 'no_enable'  ?>"
														data-target="extra_<?php echo $arrayHolder ?>_<?php echo $room->id ?>_<?php echo $extra->id ?>"/>
													<?php if ( $extra->mandatory == 1 ) : ?>
														<input type="hidden"
														       name="<?php echo $extraInputCommonName ?>[quantity]"
														       value="1" <?php echo $disabledCheckbox ?>
														       class="<?php echo $canBeEnabled ? '' : 'no_enable'  ?>"
														       disabled
														/>
													<?php endif ?>

													<select
														class="extra_<?php echo $arrayHolder ?>_<?php echo $room->id ?>_<?php echo $extra->id ?>"
														name="<?php echo $extraInputCommonName ?>[quantity]"
														<?php echo $disabledSelect ?>
													>
														<?php
														for ( $quantitySelection = 1; $quantitySelection <= $extra->max_quantity; $quantitySelection ++ ) :
															$checked = '';
															if ( isset( $currentRoomIndex['extras'][ $extra->id ]['quantity'] ) ) :
																$checked = ( $currentRoomIndex['extras'][ $extra->id ]['quantity'] == $quantitySelection ) ? 'selected' : '';
															endif;
															?>
															<option <?php echo $checked ?>
																value="<?php echo $quantitySelection ?>"><?php echo $quantitySelection ?></option>
															<?php
														endfor;
														?>
													</select>
													<span>
														<?php echo apply_filters( 'solidres_extra_name', $extra->name ) ?>
														<a href="javascript:void(0)"
														   class="toggle_extra_details"
														   data-target="extra_details_<?php echo $arrayHolder ?>_<?php echo $room->id ?>_<?php echo $extra->id ?>">
															<?php _e( 'Details', 'solidres' ) ?>
														</a>
													</span>
													<span class="extra_details"
													      id="extra_details_<?php echo $arrayHolder ?>_<?php echo $room->id ?>_<?php echo $extra->id ?>"
													      style="display: none">
														<?php if ( $extra->charge_type == 3 || $extra->charge_type == 5 || $extra->charge_type == 6 ) : ?>
															<span>
																<?php echo __( 'For adult', 'solidres' ) . ': ' . $extra->currencyAdult->format() .' (' . __(SR_Extra::$charge_types[ $extra->charge_type ], 'solidres' ) .')' ?>
															</span>
															<span>
																<?php echo __( 'For child', 'solidres' ) . ': ' . $extra->currencyChild->format() .' (' . __(SR_Extra::$charge_types[ $extra->charge_type ], 'solidres' ) .')' ?>
															</span>
														<?php else:  ?>
															<span>
																<?php echo __( 'Price', 'solidres' ) . ': ' . $extra->currency->format() .' (' . __(SR_Extra::$charge_types[ $extra->charge_type ], 'solidres' ) .')' ?>
															</span>
														<?php endif; ?>

														<span>
															<?php echo apply_filters( 'solidres_extra_desc', $extra->description )?>
														</span>
													</span>
												</li>
												<?php
											endforeach;
											?>
										</ul>
									</div>
								</div>

							</dd>
						</dl>
				</div>
				<?php
				endif;
			endfor;
		endif; ?>
	<?php endforeach; ?>

	<div class="sr_row button-row button-row-bottom">
		<div class="nine columns">
			&nbsp;
		</div>
		<div class="three columns">
			<div class="inner">
				<div class="btn-group">
					<button data-step="room" type="submit" class="btn btn-success">
						<i class="dashicons dashicons-arrow-right-alt2"></i> <?php _e( 'Next', 'solidres' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="srform[next_step]" value="guestinfo"/>
	<input type="hidden" name="srform[raid]" value="<?php echo $display_data['raid'] ?>" />
	<input type="hidden" name="srform[is_guest_making_reservation]" value="<?php echo $display_data['is_guest_making_reservation'] ?>" />
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'process-reservation' ) ?>" />
	<input type="hidden" name="action" value="solidres_reservation_process" />
	<input type="hidden" name="step" value="room" />

</form>