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

$room_type_id = $display_data['roomTypeId'];
$room_type    = $display_data['roomType'];
for ( $i = 0; $i < $display_data['quantity']; $i ++ ) :
	$current_room_index = null;
	if ( isset( $display_data['reservation_details_room']['room_types'][ $room_type_id ][ $display_data['tariffId'] ][ $i ] ) ) :
		$current_room_index = $display_data['reservation_details_room']['room_types'][ $room_type_id ][ $display_data['tariffId'] ][ $i ];
	endif;
	$identity = $room_type->id . '_' . $display_data['tariffId'] . '_' . $i;

	// Html for adult selection
	if ( ! isset( $display_data['roomType']->params['show_adult_option'] ) ) :
		$display_data['roomType']->params['show_adult_option'] = 1;
	endif;
	if ($display_data['roomType']->params['show_adult_option'] == 1) :
		$html_adult_selection = '';
		//$html_adult_selection .= '<option value="">' . __( 'Adult', 'solidres' ) . '</option>';

		for ( $j = 1; $j <= $display_data['roomType']->occupancy_adult; $j ++ ) :
			$selected = '';
			if ( isset( $current_room_index['adults_number'] ) ) :
				$selected = $current_room_index['adults_number'] == $j ? 'selected' : '';
			else :
				if ( $j == 1 ) :
					$selected = 'selected';
				endif;
			endif;
			$html_adult_selection .= '<option ' . $selected . ' value="' . $j . '">' . sprintf( _n( '1 adult', '%s adults', $j, 'solidres' ), $j ) . '</option>';
		endfor;
	endif;

	// Html for children selection
	$html_child_selection = '';
	$html_children_ages   = '';
	if ( ! isset( $display_data['roomType']->params['show_child_option'] ) ) :
		$display_data['roomType']->params['show_child_option'] = 1;
	endif;

	// Only show child option if it is enabled and the child quantity > 0
	if ( $display_data['roomType']->params['show_child_option'] == 1 && $display_data['roomType']->occupancy_child > 0 ) :
		$html_child_selection .= '<option value="">' . __( 'Child', 'solidres' ) . '</option>';
		$selected2 = '';
		for ( $j = 1; $j <= $display_data['roomType']->occupancy_child; $j ++ ) :
			if ( isset( $current_room_index['children_number'] ) ) :
				$selected2 = $current_room_index['children_number'] == $j ? 'selected' : '';
			endif;
			$html_child_selection .= '
				<option ' . $selected2 . ' value="' . $j . '">' . sprintf( _n( '1 child', '%s children', $j, 'solidres' ), $j ). '</option>
			';
		endfor;

		// Html for children ages
		if ( isset( $current_room_index['children_ages'] ) ) :
			for ($j = 0; $j < count( $current_room_index['children_ages'] ); $j ++ ) :
				$html_children_ages .= '
					<li>
						' . __( 'Child', 'solidres' ) . ' ' . ( $j + 1 ) . '
						<select name="srform[room_types][' . $room_type_id . '][' . $display_data['tariffId'] . '][' . $i . '][children_ages][]"
							data-raid="' . $display_data['assetId'] . '"
							data-roomtypeid="' . $room_type_id . '"
							data-tariffid="' . $display_data['tariffId'] . '"
							data-roomindex="' . $i . '"
							class="'. SR_UI_GRID_COL_6 .' child_age_' . $room_type_id . '_' . $display_data['tariffId'] . '_' . $i . '_' . $j . ' trigger_tariff_calculating"
							required
						>';
				$html_children_ages .= '<option value=""></option>';
				for ( $age = 1; $age <= $display_data['childMaxAge']; $age ++ ) :
					$selectedAge = '';
					if ( $age == $current_room_index['children_ages'][ $j ] ) :
						$selectedAge = 'selected';
					endif;
					$html_children_ages .= '<option ' . $selectedAge . ' value="' . $age . '">' . sprintf( _n( '1 year old', '%s years old', $age, 'solidres' ), $age ) . '</option>';
				endfor;

				$html_children_ages .= '
						</select>
					</li>';
			endfor;
		endif;
	endif;

	// Smoking
	$html_smoking_option = '';
	if ( ! isset( $display_data['roomType']->params['show_smoking_option'] ) ) :
		$display_data['roomType']->params['show_smoking_option'] = 1;
	endif;

	if ( $display_data['roomType']->params['show_smoking_option'] == 1 ) :
		$selected_non_smoking = '';
		$selected_smoking     = '';
		if ( isset( $current_room_index['preferences']['smoking'] ) ) :
			if ( $current_room_index['preferences']['smoking'] == 0 ) :
				$selected_non_smoking = 'selected';
			else :
				$selected_smoking = 'selected';
			endif;
		endif;
		$html_smoking_option = '
			<select class="form-control input-block-level" name="srform[room_types][' . $room_type_id . '][' . $display_data['tariffId'] . '][' . $i . '][preferences][smoking]">
				<option value="">' . __( 'Select your smoking options', 'solidres' ) . '</option>
				<option ' . $selected_non_smoking . ' value="0">' . __( 'Non smoking room', 'solidres' ) . '</option>
				<option ' . $selected_smoking . ' value="1">' . __( 'Smoking room', 'solidres' ) . '</option>
			</select>
		';
	endif;
	?>
	<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
		<div class="<?php echo SR_UI_GRID_COL_10 ?> <?php echo SR_UI_GRID_OFFSET_2 ?>">
			<div class="<?php echo SR_UI_GRID_CONTAINER ?> room_index_form_heading">
				<div class="<?php echo SR_UI_GRID_COL_12 ?>">
					<div class="inner">
						<h4><?php _e( 'Room', 'solidres' ) . ' ' . ( $i + 1 ) ?>: <span
								class="tariff_<?php echo $room_type_id . '_' . $display_data['tariffId'] . '_' . $i ?>">0</span>
							<a href="javascript:void(0)"
							   class="toggle_breakdown"
							   data-target="<?php echo $room_type_id . '_' . $display_data['tariffId'] . '_' . $i ?>">
								<?php _e( 'Details', 'solidres') ?>
							</a>
							<span style="display: none" class="breakdown" id="breakdown_<?php echo $room_type_id . '_' . $display_data['tariffId'] . '_' . $i ?>">

							</span>
						</h4>
					</div>
				</div>
			</div>
			<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
				<div class="<?php echo SR_UI_GRID_COL_5 ?>">
					<div class="<?php echo SR_UI_GRID_CONTAINER ?> occupancy-selection">
						<div class="inner">
							<?php if ($display_data['roomType']->params['show_adult_option'] == 1) : ?>
							<select
								data-raid="<?php echo $display_data['assetId'] ?>"
								data-roomtypeid="<?php echo $room_type_id ?>"
								data-tariffid="<?php echo $display_data['tariffId'] ?>"
								data-adjoininglayer="<?php echo $display_data['adjoiningLayer'] ?>"
								data-roomindex="<?php echo $i ?>"
								data-max="<?php echo isset($display_data['tariff']->p_max) && $display_data['tariff']->p_max > 0 ? $display_data['tariff']->p_max : $display_data['roomType']->occupancy_max ?>"
								data-min="<?php echo isset($display_data['tariff']->p_min) && $display_data['tariff']->p_min > 0 ? $display_data['tariff']->p_min : 0 ?>"
								name="srform[room_types][<?php echo $room_type_id ?>][<?php echo $display_data['tariffId'] ?>][<?php echo $i ?>][adults_number]"
								required
								data-identity="<?php echo $identity ?>"
								class="<?php echo SR_UI_GRID_COL_6 ?> adults_number occupancy_max_constraint occupancy_max_constraint_<?php echo $i ?>_<?php echo $display_data['tariffId'] ?>_<?php echo $room_type_id ?> occupancy_adult_<?php echo $room_type_id . '_' . $display_data['tariffId'] . '_' . $i ?> trigger_tariff_calculating">
								<?php echo $html_adult_selection ?>
							</select>
							<?php else: ?>
							<input type="hidden"
							       data-raid="<?php echo $display_data['assetId'] ?>"
							       data-roomtypeid="<?php echo $room_type_id ?>"
							       data-tariffid="<?php echo $display_data['tariffId'] ?>"
							       data-adjoininglayer="<?php echo $display_data['adjoiningLayer'] ?>"
							       data-roomindex="<?php echo $i ?>"
							       data-max="<?php echo isset($display_data['tariff']->p_max) && $display_data['tariff']->p_max > 0 ? $display_data['tariff']->p_max : $display_data['roomType']->occupancy_max ?>"
							       data-min="<?php echo isset($display_data['tariff']->p_min) && $display_data['tariff']->p_min > 0 ? $display_data['tariff']->p_min : 0 ?>"
							       name="srform[room_types][<?php echo $room_type_id ?>][<?php echo $display_data['tariffId'] ?>][<?php echo $i ?>][adults_number]"
							       class="<?php echo SR_UI_GRID_COL_6 ?> adults_number occupancy_max_constraint occupancy_max_constraint_<?php echo $i ?>_<?php echo $display_data['tariffId'] ?>_<?php echo $room_type_id ?> occupancy_adult_<?php echo $room_type_id . '_' . $display_data['tariffId'] . '_' . $i ?> trigger_tariff_calculating"
							       value="1"
							       data-identity="<?php echo $identity ?>"
							/>
							<?php endif ?>
							<?php if ( $display_data['roomType']->params['show_child_option'] == 1 && $display_data['roomType']->occupancy_child > 0 ) : ?>
								<select
									data-raid="<?php echo $display_data['assetId'] ?>"
									data-roomtypeid="<?php echo $room_type_id ?>"
									data-roomindex="<?php echo $i ?>"
									data-max="<?php echo isset($display_data['tariff']->p_max) && $display_data['tariff']->p_max > 0 ? $display_data['tariff']->p_max : $display_data['roomType']->occupancy_max ?>"
									data-min="<?php echo isset($display_data['tariff']->p_min) && $display_data['tariff']->p_min > 0 ? $display_data['tariff']->p_min : 0 ?>"
									data-tariffid="<?php echo $display_data['tariffId'] ?>"
									data-adjoininglayer="<?php echo $display_data['adjoiningLayer'] ?>"
									data-identity="<?php echo $identity ?>"
									name="srform[room_types][<?php echo $room_type_id ?>][<?php echo $display_data['tariffId'] ?>][<?php echo $i ?>][children_number]"
									class="<?php echo SR_UI_GRID_COL_6 ?> children_number occupancy_max_constraint occupancy_max_constraint_<?php echo $i ?>_<?php echo $display_data['tariffId'] ?>_<?php echo $room_type_id ?> reservation-form-child-quantity trigger_tariff_calculating occupancy_child_<?php echo $room_type_id . '_' . $display_data['tariffId'] . '_' . $i ?>">
									<?php echo $html_child_selection ?>
								</select>
							<?php endif ?>
							<div class="alert alert-warning" id="error_<?php echo $i ?>_<?php echo $display_data['tariffId'] ?>_<?php echo $room_type_id ?>" style="display: none">
								<?php echo sprintf( __( 'This room type requires at least %d people and maximum %d people.', 'solidres' ), $display_data['tariff']->p_min, $display_data['tariff']->p_max); ?>
							</div>
							<div
								class="<?php echo SR_UI_GRID_COL_12 ?> child-age-details <?php echo( empty( $html_children_ages ) ? 'nodisplay' : '' ) ?>">
								<p><?php _e( 'Age of child(ren) at checkout', 'solidres' ) ?></p>
								<ul class="unstyled list-unstyled"><?php echo $html_children_ages ?></ul>
							</div>
						</div>
					</div>
				</div>

				<div class="<?php echo SR_UI_GRID_COL_7 ?>">
					<div class="inner">
						<?php
						if(!isset($display_data['roomType']->params['show_guest_name_field'])):
							$display_data['roomType']->params['show_guest_name_field'] = 1;
						endif;
						if ($display_data['roomType']->params['show_guest_name_field'] == 1) : ?>
						<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
							<div class="<?php echo SR_UI_GRID_COL_12 ?>">
								<input
									name="srform[room_types][<?php echo $room_type_id ?>][<?php echo $display_data['tariffId'] ?>][<?php echo $i ?>][guest_fullname]"
									required
									type="text"
									value="<?php echo( isset( $current_room_index['guest_fullname'] ) ? $current_room_index['guest_fullname'] : '' ) ?>"
									class="form-control input-block-level"
									placeholder="<?php _e( 'Guest name', 'solidres' ) ?>"/>
							</div>
						</div>
						<?php endif; ?>

						<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
							<div class="<?php echo SR_UI_GRID_COL_12 ?>">
								<?php echo $html_smoking_option ?>
							</div>
						</div>

						<?php
						foreach ( $display_data['extras'] as $extra ) :
							$extra_input_common_name = 'srform[room_types][' . $room_type_id . '][' . $display_data['tariffId'] . '][' . $i . '][extras][' . $extra->id . ']';
							$checked                 = '';
							$disabled_checkbox       = '';
							$disabled_select         = 'disabled="disabled"';
							$already_selected        = false;
							if ( isset( $current_room_index['extras'] ) ) :
								$already_selected = array_key_exists( $extra->id, (array) $current_room_index['extras'] );
							endif;

							if ( $extra->mandatory == 1 || $already_selected ) :
								$checked = 'checked="checked"';
							endif;

							if ( $extra->mandatory == 1 ) :
								$disabled_checkbox = 'disabled="disabled"';
								$disabled_select   = 'disabled="disabled"';
							endif;

							if ( $already_selected && $extra->mandatory == 0 ) :
								$disabled_select = '';
							endif;
							?>
							<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
								<div class="<?php echo SR_UI_GRID_COL_12 ?> extras_row_roomtypeform">
									<input <?php echo $checked ?> <?php echo $disabled_checkbox ?> type="checkbox"
									                                                               data-target="extra_<?php echo $display_data['tariffId'] ?>_<?php echo $i ?>_<?php echo $extra->id ?>"/>
									<?php if ( $extra->mandatory == 1 ) : ?>
										<input type="hidden" name="<?php echo $extra_input_common_name ?>[quantity]"
											   value="1"/>
									<?php endif ?>

									<select
										class="<?php echo SR_UI_GRID_COL_2 ?> extra_<?php echo $display_data['tariffId'] ?>_<?php echo $i ?>_<?php echo $extra->id ?>"
										name="<?php echo $extra_input_common_name ?>[quantity]"
										<?php echo $disabled_select ?>>
										<?php
										for ($quantity_selection = 1; $quantity_selection <= $extra->max_quantity; $quantity_selection ++ ) :
											$checked = '';
											if ( isset( $current_room_index['extras'][ $extra->id ]['quantity'] ) ) :
												$checked = ( $current_room_index['extras'][ $extra->id ]['quantity'] == $quantity_selection ) ? 'selected' : '';
											endif;
											?>
											<option <?php echo $checked ?>
												value="<?php echo $quantity_selection ?>"><?php echo $quantity_selection ?></option>
										<?php
										endfor;
										?>
									</select>
									<span>
										<?php echo apply_filters( 'solidres_extra_name', $extra->name ) ?>
										<a href="javascript:void(0)"
										   class="toggle_extra_details"
										   data-target="extra_details_<?php echo $display_data['tariffId'] ?>_<?php echo $i ?>_<?php echo $extra->id ?>">
											<?php _e( 'Details', 'solidres' ) ?>
										</a>
									</span>
									<span class="extra_details" id="extra_details_<?php echo $display_data['tariffId'] ?>_<?php echo $i ?>_<?php echo $extra->id ?>" style="display: none">
										<?php if ($extra->charge_type == 3 || $extra->charge_type == 5 || $extra->charge_type == 6) : ?>
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
											<?php echo apply_filters( 'solidres_extra_desc', $extra->description ) ?>
										</span>
									</span>
								</div>
							</div>
						<?php
						endforeach;
						?>
					</div>
				</div>
			</div>
			<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
				<div class="<?php echo SR_UI_GRID_COL_7 ?> <?php echo SR_UI_GRID_OFFSET_5 ?>">
					<button data-step="room" type="submit" class="btn <?php echo SR_UI_GRID_COL_10 ?> btn-success btn-block">
						<i class="fa fa-arrow-right"></i>
						<?php _e( 'Next', 'solidres' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>
<?php
endfor;