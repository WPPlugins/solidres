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

$maxRooms = $asset_params['max_room_number'];
$maxAdults = $asset_params['max_adult_number'];
$maxChildren = $asset_params['max_child_number'];

$tzoffset = get_option( 'timezone_string' );
$tzoffset = $tzoffset == '' ? 'UTC' : $tzoffset;
$timezone = new DateTimeZone( $tzoffset );
$minDaysBookInAdvance = isset( $options_plugin['min_days_book_in_advance'] ) ? $options_plugin['min_days_book_in_advance'] : 0;
$maxDaysBookInAdvance = isset( $options_plugin['max_days_book_in_advance'] ) ? $options_plugin['max_days_book_in_advance'] : 0;
$minLengthOfStay = isset( $options_plugin['min_length_of_stay'] ) ? $options_plugin['min_length_of_stay'] : 1;
$datePickerMonthNum = ! empty( $options_plugin['datepicker_month_number'] ) ? $options_plugin['datepicker_month_number'] : 1;
$weekStartDay = ! empty( $options_plugin['week_start_day'] ) ? $options_plugin['week_start_day'] : 1;
$dateFormat = get_option( 'date_format', 'd-m-Y' );

$dateCheckIn = new DateTime();
if (!isset($checkin)) :
	$dateCheckIn->add(new DateInterval('P'.($minDaysBookInAdvance).'D'))->setTimezone($timezone);
endif;
$dateCheckOut = new DateTime();
if (!isset($checkout)) :
	$dateCheckOut->add(new DateInterval('P'.($minDaysBookInAdvance + $minLengthOfStay).'D'))->setTimezone($timezone);
endif;

$jsDateFormat = SR_Utilities::convert_date_format_pattern( $dateFormat );

$defaultCheckinDate = '';
$defaultCheckoutDate = '';
if( isset( $checkin ) ) {
	$checkin_module = new DateTime( $checkin, $timezone );
	$checkout_module = new DateTime( $checkout, $timezone );
	$defaultCheckinDate = $checkin_module->format('Y-m-d');
	$defaultCheckoutDate = $checkout_module->format('Y-m-d');
}

if (!empty($defaultCheckinDate)) :
	$defaultCheckinDateArray = explode('-', $defaultCheckinDate);
	$defaultCheckinDateArray[1] -= 1;
endif;

if (!empty($defaultCheckoutDate)) :
	$defaultCheckoutDateArray = explode('-', $defaultCheckoutDate);
	$defaultCheckoutDateArray[1] -= 1;
endif;

echo '<script>
	jQuery(function($) {
		var minLengthOfStay = '.$minLengthOfStay.';
		var checkout = $("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_datepicker_inline_module").datepicker({
			minDate : "+' . ( $minDaysBookInAdvance + $minLengthOfStay ). '",
			numberOfMonths : '.$datePickerMonthNum.',
			showButtonPanel : true,
			dateFormat : "'.$jsDateFormat.'",
			firstDay: '.$weekStartDay.',
			' . (isset($checkout) ? 'defaultDate: new Date(' . implode(',' , $defaultCheckoutDateArray) .'),' : '') . '
		onSelect: function() {
			$("#sr-checkavailability-form-asset-' . $asset->id . ' input[name=\'checkout\']").val($.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate")));
			$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_module").html($.datepicker.formatDate("'.$jsDateFormat.'", $(this).datepicker("getDate")) + "<i class=\"fa fa-calendar\"></i>");
			$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_datepicker_inline_module").slideToggle();
			$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkin_module").removeClass("disabledCalendar");
		}
	});
		var checkin = $("#sr-checkavailability-form-asset-' . $asset->id . ' .checkin_datepicker_inline_module").datepicker({
			minDate : "+' .  $minDaysBookInAdvance . 'd",
			'.($maxDaysBookInAdvance > 0 ? 'maxDate: "+'. ($maxDaysBookInAdvance) . '",' : '' ).'
		numberOfMonths : '.$datePickerMonthNum.',
			showButtonPanel : true,
			dateFormat : "'.$jsDateFormat.'",
			'. (isset($checkin) ? 'defaultDate: new Date(' . implode(',' , $defaultCheckinDateArray) .'),' : '') . '
		onSelect : function() {
			var currentSelectedDate = $(this).datepicker("getDate");
			var checkoutMinDate = $(this).datepicker("getDate", "+1d");
			checkoutMinDate.setDate(checkoutMinDate.getDate() + minLengthOfStay);
			checkout.datepicker( "option", "minDate", checkoutMinDate );
			checkout.datepicker( "setDate", checkoutMinDate);

			$("#sr-checkavailability-form-asset-' . $asset->id . ' input[name=\'checkin\']").val($.datepicker.formatDate("yy-mm-dd", currentSelectedDate));
			$("#sr-checkavailability-form-asset-' . $asset->id . ' input[name=\'checkout\']").val($.datepicker.formatDate("yy-mm-dd", checkoutMinDate));

			$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkin_module").html($.datepicker.formatDate("'.$jsDateFormat.'", currentSelectedDate) + "<i class=\"fa fa-calendar\"></i>");
			$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_module").html($.datepicker.formatDate("'.$jsDateFormat.'", checkoutMinDate) + "<i class=\"fa fa-calendar\"></i>");
			$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkin_datepicker_inline_module").slideToggle();
			$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_module").removeClass("disabledCalendar");
		},
		firstDay: '.$weekStartDay.'
	});
		$(".ui-datepicker").addClass("notranslate");
		$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkin_module").click(function() {
			if (!$(this).hasClass("disabledCalendar")) {
				$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkin_datepicker_inline_module").slideToggle("fast", function() {
					if ($(this).is(":hidden")) {
						$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_module").removeClass("disabledCalendar");
					} else {
						$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_module").addClass("disabledCalendar");
					}
				});
			}
		});

		$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_module").click(function() {
			if (!$(this).hasClass("disabledCalendar")) {
				$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkout_datepicker_inline_module").slideToggle("fast", function() {
					if ($(this).is(":hidden")) {
						$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkin_module").removeClass("disabledCalendar");
					} else {
						$("#sr-checkavailability-form-asset-' . $asset->id . ' .checkin_module").addClass("disabledCalendar");
					}
				});
			}
		});

		$("#sr-checkavailability-form-asset-' . $asset->id . ' .room_quantity").change(function() {
			var curQuantity = $(this).val();
			$("#sr-checkavailability-form-asset-' . $asset->id . ' .room_num_row").each(function( index ) {
				var index2 = index + 1;
				if (index2 <= curQuantity) {
					$("#sr-checkavailability-form-asset-' . $asset->id . ' #room_num_row_" + index2).show();
					$("#sr-checkavailability-form-asset-' . $asset->id . ' #room_num_row_" + index2 + " select").removeAttr("disabled");
				} else {
					$("#sr-checkavailability-form-asset-' . $asset->id . ' #room_num_row_" + index2).hide();
					$("#sr-checkavailability-form-asset-' . $asset->id . ' #room_num_row_" + index2 + " select").attr("disabled", "disabled");
				}
			});
		});

		if ($("#sr-checkavailability-form-asset-' . $asset->id . ' .room_quantity").val() > 0) {
			$("#sr-checkavailability-form-asset-' . $asset->id . ' .room_quantity").trigger("change");
		}
	});
</script>';

$enable_room_quantity = isset($asset_params['enable_room_quantity_option']) ? $asset_params['enable_room_quantity_option'] : 0;

?>

<form id="sr-checkavailability-form-asset-<?php echo $asset->id ?>" action="<?php echo get_site_url() . '/' . $asset->alias ?>" method="GET" class="form-stacked sr-validate">
	<fieldset>
		<input name="id" value="<?php echo $asset->id ?>" type="hidden" />
		<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
			<div class="<?php echo SR_UI_GRID_COL_12 ?>">
				<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
					<div class="<?php echo $enable_room_quantity == 0 ? SR_UI_GRID_COL_9 : SR_UI_GRID_COL_5 ?>">
						<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
							<div class="<?php echo SR_UI_GRID_COL_6 ?>">
								<label for="checkin">
									<?php _e( 'Arrival Date', 'solidres' )?>
								</label>
								<div class="checkin_module datefield">
									<?php
									echo isset($checkin) ? $checkin_module->format( $dateFormat ) : $dateCheckIn->format($dateFormat);
									?>
									<i class="fa fa-calendar"></i>
								</div>
								<div class="checkin_datepicker_inline_module datepicker_inline" style="display: none"></div>
								<input type="hidden" name="checkin" value="<?php echo isset($checkin) ? $checkin_module->format( 'Y-m-d' ) : $dateCheckIn->format('Y-m-d'); ?>" />
							</div>
							<div class="<?php echo SR_UI_GRID_COL_6 ?>">
								<label for="checkout">
									<?php _e( 'Departure Date', 'solidres' ); ?>
								</label>
								<div class="checkout_module datefield">
									<?php
									echo isset($checkout) ? $checkout_module->format( $dateFormat ) : $dateCheckOut->format($dateFormat);
									?>
									<i class="fa fa-calendar"></i>
								</div>
								<div class="checkout_datepicker_inline_module datepicker_inline" style="display: none"></div>
								<input type="hidden" name="checkout" value="<?php echo isset($checkout) ? $checkout_module->format( 'Y-m-d' ) : $dateCheckOut->format('Y-m-d'); ?>" />
							</div>
						</div>
					</div>
					<div <?php echo $enable_room_quantity == 0 ? 'style="display:none"' : '' ?> class="<?php echo SR_UI_GRID_COL_5 ?>">
						<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
							<div class="<?php echo SR_UI_GRID_COL_3 ?>">
								<label><?php _e( 'Rooms', 'solidres' ); ?></label>
								<select class="form-control input-block-level room_quantity" name="room_quantity">
									<?php for ( $room_num = 1; $room_num <= $maxRooms; $room_num ++ ) : ?>
										<option <?php echo $room_num == $asset->roomsOccupancyOptionsCount ? 'selected' : '' ?>
											value="<?php echo $room_num ?>"><?php echo $room_num ?></option>
									<?php endfor ?>
								</select>
							</div>
							<div class="<?php echo SR_UI_GRID_COL_9 ?>">
								<?php for ( $room_num = 1; $room_num <= $maxRooms; $room_num ++ ) : ?>
									<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
										<div class="<?php echo SR_UI_GRID_COL_12 ?> room_num_row" id="room_num_row_<?php echo $room_num ?>"
										     style="<?php echo $room_num > 0 ? 'display: none' : '' ?>">
											<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
												<div class="<?php echo SR_UI_GRID_COL_4 ?>">
													<label>&nbsp;</label>
													<?php _e( 'Room', 'solidres' ); ?> <?php echo $room_num ?>
												</div>
												<div class="<?php echo SR_UI_GRID_COL_4 ?>">
													<label><?php _e( 'Adults', 'solidres' ); ?></label>
													<select <?php echo $room_num > 0 ? 'disabled' : '' ?> class="form-control input-block-level" name="room_opt[<?php echo $room_num ?>][adults]">
														<?php
														for ( $a = 1; $a <= $maxAdults; $a ++ ) :
															$selected = '';
															if ( isset( $rooms_occupancy_options[ $room_num ]['adults'] ) && ( $a == $rooms_occupancy_options[ $room_num ]['adults'] ) ) {
																$selected = 'selected';
															} ?>
															<option <?php echo $selected ?>
																value="<?php echo $a ?>"><?php echo $a ?></option>
														<?php endfor ?>
													</select>
												</div>
												<div class="<?php echo SR_UI_GRID_COL_4 ?>">
													<label><?php _e( 'Children', 'solidres' ); ?></label>
													<select <?php echo $room_num > 0 ? 'disabled' : '' ?> class="form-control input-block-level"
													                                                      name="room_opt[<?php echo $room_num ?>][children]">
														<?php
														for ( $c = 0; $c < $maxChildren; $c ++ ) :
															$selected = '';
															if ( isset( $rooms_occupancy_options[ $room_num ]['children'] ) && $c == $rooms_occupancy_options[ $room_num ]['children'] ) {
																$selected = 'selected';
															} ?>
															<option <?php echo $selected ?>
																value="<?php echo $c ?>"><?php echo $c ?></option>
														<?php endfor ?>
													</select>
												</div>
											</div>
										</div>
									</div>
								<?php endfor ?>
							</div>
						</div>
					</div>
					<div class="<?php echo $enable_room_quantity == 0 ? SR_UI_GRID_COL_3 : SR_UI_GRID_COL_2 ?>">
						<div class="action">
							<label>&nbsp;</label>
							<button class="btn btn-block btn-primary primary" type="submit"><i class="fa fa-search"></i> <?php _e( 'Check', 'solidres' ); ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</form>