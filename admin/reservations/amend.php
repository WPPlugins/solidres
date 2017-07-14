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

function sr_amend_reservation_item( $id ) {
	global $wpdb;
	if ( isset($id) && $id > 0 ) {
		$reservations = new SR_Reservation();
		$reservations->record_access( $id );
		$reservation_data = $reservations->load( $id );
		$baseCurrency = new SR_Currency( 0, $reservation_data->currency_id );
	}

	$message = isset( $_GET['message'] ) ? $_GET['message'] : '';
	$text_message = '';
	$options_plugin = get_option( 'solidres_plugin' );
	wp_enqueue_script( 'solidres_editable' );
	wp_enqueue_style( 'solidres_editable', false );
	wp_enqueue_style( 'solidres_skeleton' );
	if ( $message == 1 ) {
		$text_message = __( 'Your invoice is sent.', 'solidres' );
	} else if ( $message == 2 ) {
		$text_message = __( 'Your invoice is not sent.', 'solidres' );
	} else if ( $message == 3 ) {
		$text_message = __( 'Your invoice is generated.', 'solidres' );
	} else if ( $message == 3 ) {
		$text_message = __( 'Your invoice is not generated.', 'solidres' );
	}

	$checkin = isset( $reservation_data->checkin ) ? $reservation_data->checkin : null;
	$checkout = isset( $reservation_data->checkout ) ? $reservation_data->checkout : null ;

	$tzoffset = get_option( 'timezone_string' );
	$tzoffset = $tzoffset == '' ? 'UTC' : $tzoffset;
	$timezone = new DateTimeZone( $tzoffset );
	$options = get_option( 'solidres_plugin' );
	$minDaysBookInAdvance = isset( $options['min_days_book_in_advance'] ) ? $options['min_days_book_in_advance'] : 0;
	$maxDaysBookInAdvance = isset( $options['max_days_book_in_advance'] ) ? $options['max_days_book_in_advance'] : 0;
	$minLengthOfStay = isset( $options['min_length_of_stay'] ) ? $options['min_length_of_stay'] : 1;
	$datePickerMonthNum = ! empty( $options['datepicker_month_number'] ) ? $options['datepicker_month_number'] : 1;
	$weekStartDay = ! empty( $options['week_start_day'] ) ? $options['week_start_day'] : 1;
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
	?>
	<script>
	jQuery(function($) {
		var minLengthOfStay = <?php echo $minLengthOfStay ?>;
		var checkout = $(".checkout_datepicker_inline_module").datepicker({
			minDate : "+<?php echo $minDaysBookInAdvance + $minLengthOfStay ?>",
			numberOfMonths : <?php echo $datePickerMonthNum ?>,
			showButtonPanel : true,
			dateFormat : "<?php echo $jsDateFormat ?>",
			firstDay: <?php echo $weekStartDay ?>,
			<?php echo (isset($checkout) ? 'defaultDate: new Date(' . implode(',' , $defaultCheckoutDateArray) .'),' : '') ?>
			onSelect: function() {
				$("#item-form input#checkout").val($.datepicker.formatDate("yy-mm-dd", $(this).datepicker("getDate")));
				$("#item-form .checkout_module").html($.datepicker.formatDate("<?php echo $jsDateFormat ?>", $(this).datepicker("getDate")) + "<i class=\"fa fa-calendar\"></i>");
				$(".checkout_datepicker_inline_module").slideToggle();
				$(".checkin_module").removeClass("disabledCalendar");
			}
		});
		var checkin = $(".checkin_datepicker_inline_module").datepicker({
			minDate : "+<?php echo $minDaysBookInAdvance ?>d",
			<?php echo ($maxDaysBookInAdvance > 0 ? 'maxDate: "+'. ($maxDaysBookInAdvance) . '",' : '' ) ?>
			numberOfMonths : <?php echo $datePickerMonthNum ?>,
			showButtonPanel : true,
			dateFormat : "<?php echo $jsDateFormat ?>",
			<?php echo (isset($checkin) ? 'defaultDate: new Date(' . implode(',' , $defaultCheckinDateArray) .'),' : '') ?>
			onSelect : function() {
				var currentSelectedDate = $(this).datepicker("getDate");
				var checkoutMinDate = $(this).datepicker("getDate", "+1d");
				checkoutMinDate.setDate(checkoutMinDate.getDate() + minLengthOfStay);
				checkout.datepicker( "option", "minDate", checkoutMinDate );
				checkout.datepicker( "setDate", checkoutMinDate);

				$("#item-form input#checkin").val($.datepicker.formatDate("yy-mm-dd", currentSelectedDate));
				$("#item-form input#checkout").val($.datepicker.formatDate("yy-mm-dd", checkoutMinDate));

				$("#item-form .checkin_module").html($.datepicker.formatDate("<?php echo $jsDateFormat ?>", currentSelectedDate) + "<i class=\"fa fa-calendar\"></i>");
				$("#item-form .checkout_module").html($.datepicker.formatDate("<?php echo $jsDateFormat ?>", checkoutMinDate) + "<i class=\"fa fa-calendar\"></i>");
				$(".checkin_datepicker_inline_module").slideToggle();
				$(".checkout_module").removeClass("disabledCalendar");
			},
			firstDay: '.$weekStartDay.'
		});
		$(".ui-datepicker").addClass("notranslate");
		$(".checkin_module").click(function() {
			if (!$(this).hasClass("disabledCalendar")) {
				$(".checkin_datepicker_inline_module").slideToggle("fast", function() {
					if ($(this).is(":hidden")) {
						$(".checkout_module").removeClass("disabledCalendar");
					} else {
						$(".checkout_module").addClass("disabledCalendar");
					}
				});
			}
		});

		$(".checkout_module").click(function() {
			if (!$(this).hasClass("disabledCalendar")) {
				$(".checkout_datepicker_inline_module").slideToggle("fast", function() {
					if ($(this).is(":hidden")) {
						$(".checkin_module").removeClass("disabledCalendar");
					} else {
						$(".checkin_module").addClass("disabledCalendar");
					}
				});
			}
		});

		$(".room_quantity").change(function() {
			var curQuantity = $(this).val();
			$(".room_num_row").each(function( index ) {
				var index2 = index + 1;
				if (index2 <= curQuantity) {
					$("#room_num_row_" + index2).show();
					$("#room_num_row_" + index2 + " select").removeAttr("disabled");
				} else {
					$("#room_num_row_" + index2).hide();
					$("#room_num_row_" + index2 + " select").attr("disabled", "disabled");
				}
			});
		});

		if ($(".room_quantity").val() > 0) {
			$(".room_quantity").trigger("change");
		}

		var checkin, checkout, reservation_id, assetid, requesturl, available_rooms_holder, state, payment_status;
		available_rooms_holder = $(".room");
		var doValidate = function() {
			checkin = $("#checkin").val();
			checkout = $("#checkout").val();
			state = $("#state").val();
			payment_status = $("#payment_status").val();
			reservation_id = <?php echo $id > 0 ? $id : 0 ?>;
			assetid = $("#reservation_asset_id").val();
			//requesturl = "index.php?option=com_solidres&task=reservation" + (Solidres.context == "frontend" ? "" : "base") + ".getAvailableRooms&checkin=" + checkin + "&checkout="+ checkout + "&id=" + reservation_id + "&assetid=" + assetid + "&state=" + state + "&payment_status=" + payment_status;
			if (checkin.length == 0 || checkout.length == 0 || assetid.length == 0) {
				alert("Please make sure that you selected reservation asset, start date and end date.");
				return false;
			} else {
				return true;
			}
		};

		$("#reservation_load_available_rooms").click(function() {
			var isFormValid;
			isFormValid = doValidate();
			if (isFormValid) {
				$(".reservation-single-step-holder").removeClass("nodisplay").addClass("nodisplay");
				available_rooms_holder.addClass("nodisplay");
				$(".processing").removeClass("nodisplay");
				$.ajax({
					url : '<?php echo admin_url( 'admin-ajax.php' ) ?>',
					data: {
						action: 'solidres_load_available_rooms',
						security : '<?php echo wp_create_nonce( 'load-available-rooms' ) ?>',
						checkin: checkin,
						checkout: checkout,
						id: reservation_id,
						assetid: assetid,
						state: state,
						payment_status: payment_status
					},
					success : function(html) {
						available_rooms_holder.empty().html(html);
						available_rooms_holder.find("input.reservation_room_select").each(function() {
							var self = $(this);
							/*if (self.is(":checked")) {
							 self.parents(".room_selection_wrapper").find("select.tariff_selection").trigger("change");
							 }*/
						});
						$(".processing").addClass("nodisplay");
						available_rooms_holder.removeClass("nodisplay");
						isAtLeastOneRoomSelected();
					}
				});
			}
		});
	});
	</script>

	<div class="wrap">
		<div id="wpbody">
			<div id="message" class="updated below-h2 <?php echo $message != '' ? '' : 'nodisplay'; ?>"><p><?php echo $text_message; ?></p></div>
			<h2>
				<?php _e( 'Amend Reservation', 'solidres' ); ?>
				<a
					href="<?php echo admin_url( 'admin.php?page=sr-reservations&action=edit&id=' . $id ); ?>"
					class="add-new-h2"><?php _e( 'View', 'solidres' ); ?></a>
			</h2>

			<div id="solidres" class="metabox-holder columns-2">
				<div id="post-body-content" class="edit-form-section edit_reservation_table">

					<div id="reservation_general_info" class="postbox">
						<form enctype="multipart/form-data" action="" method="post" name="adminForm" id="item-form" class="form-validate form-horizontal">
								<div class="handlediv"><br></div>
								<h3 class="hndle"><span><?php _e( 'General info', 'solidres' ) ?></span></h3>
								<div class="inside">
									<div class="sr_row">
										<div class="six columns">
											<div class="control-group">
												<label class="control-label" for="inputEmail"><?php _e( 'Arrival Date', 'solidres' )?></label>
												<div class="controls">
													<div class="checkin_module datefield">
														<?php
														echo isset($checkin) ? $checkin_module->format( $dateFormat ) : $dateCheckIn->format($dateFormat);
														?>
														<i class="fa fa-calendar"></i>
													</div>
													<div class="checkin_datepicker_inline_module datepicker_inline" style="display: none"></div>
													<?php // this field must always be "Y-m-d" as it is used internally only ?>
													<input type="hidden"
													       name="srform[checkin]"
													       id="checkin"
													       value="<?php echo isset($checkin) ? $checkin_module->format( 'Y-m-d' ) : $dateCheckIn->format('Y-m-d'); ?>" />
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="inputPassword"><?php _e( 'Departure Date', 'solidres' ); ?></label>
												<div class="controls">
													<div class="checkout_module datefield">
														<?php
														echo isset($checkout) ? $checkout_module->format( $dateFormat ) : $dateCheckOut->format($dateFormat);
														?>
														<i class="fa fa-calendar"></i>
													</div>
													<div class="checkout_datepicker_inline_module datepicker_inline" style="display: none"></div>
													<?php // this field must always be "Y-m-d" as it is used internally only ?>
													<input type="hidden"
													       name="srform[checkout]"
													       id="checkout"
													       value="<?php echo isset($checkout) ? $checkout_module->format( 'Y-m-d' ) : $dateCheckOut->format('Y-m-d'); ?>" />
												</div>
											</div>
											<div class="control-group">
												<label class="control-label" for="inputPassword"><?php _e( 'Asset name', 'solidres' )?></label>
												<div class="controls">
													<select required id="reservation_asset_id" name="srform[reservation_asset_id]" class="twelve columns">
														<?php echo SR_Helper::render_list_asset( isset( $reservation_data->reservation_asset_id ) ? $reservation_data->reservation_asset_id : 0 ) ?>
													</select>
												</div>
											</div>

											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<button type="button" data-limit-booking-id="<?php echo $id ?>" class="btn btn-info" id="reservation_load_available_rooms"><?php _e( 'Load available rooms', 'solidres' ) ?></button>
												</div>
											</div>
										</div>
										<div class="six columns">
											<div class="control-group">
												<label class="control-label"><?php _e( 'Status', 'solidres' ) ?></label>
												<div class="controls">
													<?php
													$reservation_state = isset( $reservation_data->state ) ? $reservation_data->state : ''; ?>
													<select id="state" name="srform[state]" class="twelve columns">
														<option <?php echo $reservation_state === '' ? 'selected' : '' ?> value=""><?php _e( 'Filter by status', 'solidres' ); ?></option>
														<option value="0" <?php echo $reservation_state === '0' ? 'selected' : '' ?>><?php _e( 'Pending arrival', 'solidres' ); ?></option>
														<option value="1" <?php echo $reservation_state === '1' ? 'selected' : '' ?>><?php _e( 'Checked-in', 'solidres' ); ?></option>
														<option value="2" <?php echo $reservation_state === '2' ? 'selected' : '' ?>><?php _e( 'Checked-out', 'solidres' ); ?></option>
														<option value="3" <?php echo $reservation_state === '3' ? 'selected' : '' ?>><?php _e( 'Closed', 'solidres' ); ?></option>
														<option value="4" <?php echo $reservation_state === '4' ? 'selected' : '' ?>><?php _e( 'Canceled', 'solidres' ); ?></option>
														<option value="5" <?php echo $reservation_state === '5' ? 'selected' : '' ?>><?php _e( 'Confirmed', 'solidres' ); ?></option>
														<option value="-2" <?php echo $reservation_state === '-2' ? 'selected' : '' ?>><?php _e( 'Trashed', 'solidres' ); ?></option>
													</select>
												</div>
											</div>
											<div class="control-group">
												<label class="control-label"><?php _e( 'Payment status', 'solidres' ) ?></label>
												<div class="controls">
													<?php
													$reservation_payment_status = isset( $reservation_data->payment_status ) ? $reservation_data->payment_status : '';
													?>
													<select id="payment_status" name="srform[payment_status]" class="twelve columns">
														<option value=""><?php _e( 'Filter by payment status', 'solidres' ); ?></option>
														<option value="0" <?php echo $reservation_payment_status == 0 ? 'selected' : '' ?>><?php _e( 'Unpaid', 'solidres' ); ?></option>
														<option value="1" <?php echo $reservation_payment_status == 1 ? 'selected' : '' ?>><?php _e( 'Completed', 'solidres' ); ?></option>
														<option	value="2" <?php echo $reservation_payment_status == 2 ? 'selected' : '' ?>><?php _e( 'Cancelled', 'solidres' ); ?></option>
														<option value="3" <?php echo $reservation_payment_status == 3 ? 'selected' : '' ?>><?php _e( 'Pending', 'solidres' ); ?></option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
						</form>
					</div>

					<div class="postbox">
						<div class="handlediv"><br></div>
						<h3 class="hndle"><span><?php _e( 'Room & Rate', 'solidres' ) ?></span></h3>
						<div class="processing nodisplay"></div>
						<div class="reservation-single-step-holder backend room"></div>
					</div>

					<div class="postbox">
						<div class="handlediv"><br></div>
						<h3 class="hndle"><span><?php _e( 'Guest information', 'solidres' ) ?></span></h3>
						<div class="reservation-single-step-holder guestinfo backend nodisplay"></div>
					</div>

					<div class="postbox">
						<div class="handlediv"><br></div>
						<h3 class="hndle"><span><?php _e( 'Confirmation', 'solidres' ) ?></span></h3>
						<div class="reservation-single-step-holder backend confirmation nodisplay"></div>
					</div>

				</div>
			</div>
		</div>
	</div>
<?php }