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

if ( !isset($options_tariff[ 'show_frontend_tariffs' ])) :
	$options_tariff[ 'show_frontend_tariffs' ] = 1;
endif;
?>

<?php if ( ! empty( $msg ) ) : ?>
	<div class="alert alert-info">
		<?php echo $msg ?>
	</div>
<?php endif ?>

<?php
if ( ! isset( $asset_params['enable_coupon'] ) ) :
	$asset_params['enable_coupon'] = 0;
endif;

if ( $asset_params['enable_coupon'] == 1 ) :

	$coupon = solidres()->session->get( 'sr_coupon' );


	if ( ! $is_fresh ) : ?>
		<div class="coupon">
			<div class="input-append input-group col-md-6">
				<input type="text" name="coupon_code" class="form-control" id="coupon_code"
				       placeholder="<?php _e( 'Enter coupon code (Optional)', 'solidres' ) ?>"/>
				<span class="input-group-btn">
					<button id="coupon_code_check" class="btn btn-default" type="button"><?php _e( 'Check', 'solidres' ) ?></button>
				</span>
			</div>

			<?php if ( isset( $coupon ) ) : ?>
				<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
					<?php _e( 'Applied coupon', 'solidres' ) ?>
					<span class="label label-success">
					<?php echo $coupon['coupon_name'] ?>
					</span>&nbsp;
					<a id="sr-remove-coupon" href="javascript:void(0)" data-couponid="<?php echo $coupon['coupon_id'] ?>">
						<?php _e( 'Remove', 'solidres' ) ?>
					</a>
				</div>
			<?php endif ?>
		</div>

		<script>
			jQuery(function($) {
				$('#coupon_code_check').click(function() {
					var self = $('input#coupon_code');
					var coupon_code = self.val();
					if (coupon_code) {
						$.ajax({
							type: 'POST',
							url: solidres.ajaxurl,
							data: {
								coupon_code: coupon_code,
								raid: $('input[name="id"]').val(),
								action: 'solidres_check_coupon',
								security: '<?php echo wp_create_nonce( 'check-coupon' ) ?>'
							},
							success: function(response) {
								self.parent().next('span').remove();
								self.parent().after(response.message);
								if (!response.status) {
									$('#apply-coupon').attr('disabled', 'disabled');
								} else {
									$('#apply-coupon').removeAttr('disabled');
								}
							},
							dataType: 'JSON'
						});
					}
				});
			});
		</script>
	<?php
	endif;
endif;
?>
<a name="form"></a>

<?php if ( isset($asset_params['show_inline_checkavailability_form']) && $asset_params['show_inline_checkavailability_form'] == 1 ) : ?>
<div id="asset-checkavailability-form">
	<div class="inner">
		<?php require( 'checkavailability.php' ); ?>
	</div>
</div>
<?php endif ?>

<div class="wizard">
	<ul class="steps">
		<li data-target="#step1" class="active reservation-tab reservation-tab-room <?php echo SR_UI_GRID_COL_4 ?>"><span
				class="badge badge-info">1</span><?php _e( 'Room &amp; Rates', 'solidres' ); ?><span
				class="chevron"></span></li>
		<li data-target="#step2" class="reservation-tab reservation-tab-guestinfo <?php echo SR_UI_GRID_COL_4 ?>"><span
				class="badge">2</span><?php _e( 'Guest info &amp; Payment', 'solidres' ); ?><span
				class="chevron"></span></li>
		<li data-target="#step3" class="reservation-tab reservation-tab-confirmation <?php echo SR_UI_GRID_COL_4 ?>"><span class="badge">3</span><?php _e( 'Confirmation', 'solidres' ); ?>
		</li>
	</ul>
</div>
<div class="step-content">
	<div class="step-pane active" id="step1">
		<!-- Tab 1 -->
		<div class="reservation-single-step-holder room">

			<?php require( 'searchinfo.php' ); ?>

			<form enctype="multipart/form-data"
				  id="sr-reservation-form-room"
				  class="sr-reservation-form"
				  action="index.php"
				  method="POST">

				<?php if ( count( $room_types ) > 0 ) : ?>
					<?php if ( ! $is_fresh ) : ?>
						<div class="<?php echo SR_UI_GRID_CONTAINER ?> button-row button-row-top">
							<div class="<?php echo SR_UI_GRID_COL_9 ?>">
								<div class="inner">
									<p><?php _e( 'Select your room type, review the prices and click Next to continue', 'solidres' ) ?></p>
								</div>
							</div>
							<div class="<?php echo SR_UI_GRID_COL_3 ?>">
								<div class="inner">
									<div class="btn-group">
										<button data-step="room" type="submit" class="btn btn-success">
											<i class="fa fa-arrow-right"></i> <?php _e( 'Next', 'solidres' ) ?>
										</button>
									</div>
								</div>
							</div>
						</div>
					<?php endif ?>

					<?php
					$count = 1;
					foreach ( $room_types as $room_type ) :
						if (!is_array($room_type->params)) : 
							$room_type->params = json_decode( $room_type->params, true );
						endif;

						$skip_room_form = false;
						if ( isset( $room_type->params[ 'skip_room_form' ] ) && $room_type->params[ 'skip_room_form' ] == 1 ) :
							$skip_room_form = true;
						endif;

						$is_exclusive = false;
						if ( isset( $room_type->params[ 'is_exclusive' ] ) && $room_type->params[ 'is_exclusive' ] == 1 ) :
							$is_exclusive = true;
						endif;

						$show_remaining_rooms = true;
						if (isset($room_type->params['show_number_remaining_rooms']) && $room_type->params['show_number_remaining_rooms'] == 0) :
							$show_remaining_rooms = false;
						endif;

					?>
						<div class="<?php echo SR_UI_GRID_CONTAINER ?> <?php echo esc_attr( $room_type->rowCSSClass ) ?>"
							 id="room_type_row_<?php echo (int) $room_type->id; ?>">
							<div class="<?php echo SR_UI_GRID_COL_12 ?>">
								<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
									<div class="<?php echo SR_UI_GRID_COL_12 ?>">
										<div class="inner">
											<h4 class="roomtype_name"
												id="room_type_details_handler_<?php echo (int) $room_type->id; ?>">
										<span class="label label-info">
											<?php echo $room_type->occupancy_max > 0 ? $room_type->occupancy_max : (int ) $room_type->occupancy_adult + (int) $room_type->occupancy_child; ?>
											<i class="fa fa-user"></i>
										</span>

												<?php echo apply_filters( 'solidres_roomtype_name', esc_attr( $room_type->name ) ); ?>
												<?php if ( $room_type->featured == 1 ) : ?>
													<span
														class="label label-success"><?php _e( 'Featured', 'solidres' ) ?></span>
												<?php endif ?>
											</h4>
										</div>
									</div>
								</div>
								<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
									<div class="<?php echo SR_UI_GRID_COL_4 ?>">
										<div class="inner">
											<?php
											if ( ! empty( $room_type->media ) ) :
												echo '<div id="carousel' . (int) $room_type->id . '" class="carousel slide">';
												echo '<div class="carousel-inner">';
												$count_media = 0;
												$active      = '';
												foreach ( $room_type->media as $media ) :
													$active     = ( $count_media == 0 ) ? 'active' : '';
													$media_attr = wp_get_attachment_image_src( $media->media_id, 'full' );
													?>
													<div class="item <?php echo esc_attr( $active ) ?>">
														<a class="room_type_details sr-photo-<?php echo (int) $room_type->id ?>"
														   href="<?php echo esc_url( $media_attr[0] ); ?>">
															<?php echo wp_get_attachment_image( $media->media_id, array( 300, 250 ) ); ?>
														</a>
													</div>
													<?php
													$count_media ++;
												endforeach;
												echo '</div>';
												echo '<a class="carousel-control left" href="#carousel' . (int) $room_type->id . '" data-slide="prev">&lsaquo;</a>';
												echo '<a class="carousel-control right" href="#carousel' . (int) $room_type->id . '" data-slide="next">&rsaquo;</a>';
												echo '</div>';
											endif;
											?>
											<script>
											jQuery(function($){
												$(".sr-photo-<?php echo $room_type->id ?>").colorbox({rel:"sr-photo-<?php echo $room_type->id ?>", transition:"fade", width: "98%", height: "98%", className: "colorbox-w"});
												//$(".carousel").carousel();
											});
											</script>
										</div>
									</div>
									<div class="<?php echo SR_UI_GRID_COL_8 ?>">
										<div class="inner">
											<div class="roomtype_desc">
												<?php echo apply_filters( 'solidres_roomtype_desc', $room_type->description ); ?>
											</div>
											<?php
											if ( !$is_fresh && !empty($room_type->availableTariffs) && $show_remaining_rooms ) :
												if ( isset($room_type->totalAvailableRoom) ) : ?>
											<p>
											<span class="num_rooms_available_msg" id="num_rooms_available_msg_<?php echo (int) $room_type->id; ?>"
												  data-original-text="<?php printf( _n( 'Last chance! Only 1 room left', 'Only %d rooms left', $room_type->totalAvailableRoom, 'solidres' ), $room_type->totalAvailableRoom )  ?>">
												<?php printf( _n( 'Last chance! Only 1 room left', 'Only %d rooms left', $room_type->totalAvailableRoom, 'solidres' ), $room_type->totalAvailableRoom )  ?>
											</span>
											</p>
											<?php
												endif;
											endif;
											?>

											<button type="button" class="btn btn-default toggle_more_desc"
													data-target="<?php echo (int) $room_type->id; ?>">
												<i class="fa fa-eye"></i>
												<?php _e( 'More info', 'solidres' ) ?>
											</button>

											<?php if ( isset( $options_plugin['availability_calendar_enable'] ) && $options_plugin['availability_calendar_enable'] == 1 ) : ?>
												<button type="button"
														data-roomtypeid="<?php echo (int) $room_type->id ?>"
														class="btn btn-default load-calendar">
													<i class="fa fa-calendar"></i> <?php _e( 'View calendar', 'solidres' ) ?>
												</button>
											<?php endif ?>

											<?php if (defined('SR_PLUGIN_COMPLEXTARIFF_ENABLED') && SR_PLUGIN_COMPLEXTARIFF_ENABLED && $options_tariff[ 'show_frontend_tariffs' ] != 0) : ?>
												<button type="button" data-roomtypeid="<?php echo $room_type->id ?>" class="btn btn-default toggle-tariffs">
													<?php if ($options_tariff[ 'show_frontend_tariffs' ]) : ?>
														<i class="fa fa-compress"></i> <?php _e( 'Tariffs', 'solidres' ) ?>
													<?php else : ?>
														<i class="fa fa-expand"></i> <?php _e( 'Tariffs', 'solidres' ) ?>
													<?php endif ?>
												</button>
											<?php endif ?>

											<div class="unstyled more_desc"
												 id="more_desc_<?php echo (int) $room_type->id ?>"
												 style="display: none">
												<?php
												$room_type_field_data      = new SR_Custom_Field( array(
													'id'   => (int) $room_type->id,
													'type' => 'room_type'
												) );
												$load_roomtype_field_data = $room_type_field_data->create_array_group();
												$room_type_field_data_view = '';
												foreach ( $load_roomtype_field_data as $group_name => $fields ) {
													foreach ( $fields as $field ) {
														$room_type_field_data_view .= '<p><strong>' . __( ucfirst( $room_type_field_data->split_field_name( solidres_convertslugtostring( $field[0] ) ) ), 'solidres' ) . ':</strong> ' . apply_filters( 'solidres_roomtype_customfield', $field[1] ) . '</p>';
													}
												}
												echo $room_type_field_data_view;
												?>
											</div>
										</div>
									</div>
									<!-- end of span8 -->
								</div>
								<!-- end of row-fluid -->

								<?php
								if ( isset( $options_plugin['availability_calendar_enable'] ) && $options_plugin['availability_calendar_enable'] == 1 ) { ?>
									<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
										<div class="<?php echo SR_UI_GRID_COL_12 ?> availability-calendar"
											 id="availability-calendar-<?php echo (int) $room_type->id; ?>"
											 style="display: none"></div>
									</div>
								<?php } ?>

								<div class="<?php echo SR_UI_GRID_CONTAINER ?>" id="tariff-holder-<?php echo $room_type->id ?>" style="<?php echo $options_tariff[ 'show_frontend_tariffs' ] ? '' : 'display: none' ?>">
									<div class="<?php echo SR_UI_GRID_COL_12 ?>">
										<div class="inner">
											<?php
											$hasMatchedTariffs = true;
											if ((!$is_fresh)) :
												$hasMatchedTariffs = false;

												// Special case: join tariffs
												if (!empty($room_type->availableTariffs)) :
													foreach ( $room_type->availableTariffs as $tariffKey => $tariffInfo ) :
												?>
													<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
														<div
															id="tariff-box-<?php echo $room_type->id ?>-<?php echo $tariffKey ?>" data-targetcolor="FF981D" class="<?php echo SR_UI_GRID_COL_12 ?> tariff-box <?php //echo $tariffIsSelected ?>">
															<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
																<div class="<?php echo SR_UI_GRID_COL_5 ?> tariff-title-desc">
																	<strong>
																		<?php
																		if (!empty($tariffInfo['tariffTitle'])) :
																			echo apply_filters( 'solidres_tariff_title', $tariffInfo['tariffTitle'] );
																		else :
																			if ($asset->booking_type == 0) :
																				echo sprintf( _n( 'Price is for %s night', 'Price is for %s nights', $stay_length, 'solidres' ), $stay_length );
																			else :
																				echo sprintf( _n( 'Price is for %s day', 'Price is for %s days', $stay_length + 1, 'solidres' ), $stay_length + 1);
																			endif;
																		endif;

																		?>
																	</strong>
																	<?php
																	if (!empty($tariffInfo['tariffDescription'])) :
																		echo '<p>' . apply_filters( 'solidres_tariff_desc', $tariffInfo['tariffDescription'] ) . '</p>';
																	endif;
																	?>
																</div>
																<div
																	class="<?php echo SR_UI_GRID_COL_4 ?> tariff-value normal_tariff">
																	<div class="inner">
																		<?php echo $tariffInfo['val']->format() ?>
																	</div>
																</div>
																<div class="<?php echo SR_UI_GRID_COL_3 ?>">
																	<div class="inner">
																		<?php
																		if ( isset ( $room_type->totalAvailableRoom ) ) :
																			if ( $room_type->totalAvailableRoom == 0 ) :
																				_e( 'Sold out!', 'solidres' );
																			else :
																				if ( !$is_exclusive ) :
																				?>
																				<select
																					name="solidres[ign<?php echo rand() ?>]"
																					data-raid="<?php echo $asset->id ?>"
																					data-rtid="<?php echo $room_type->id ?>"
																					data-tariffid="<?php echo $tariffKey ?>"
																					data-adjoininglayer="<?php echo $tariffInfo['tariffAdjoiningLayer'] ?>"
																					data-totalroomsleft="<?php echo $room_type->totalAvailableRoom ?>"
																					class="form-control input-block-level roomtype-quantity-selection quantity_<?php echo $room_type->id ?>">
																					<option value="0"><?php _e( 'Quantity', 'solidres' ); ?></option>
																					<?php
																					for ( $i = 1; $i <= $room_type->totalAvailableRoom; $i ++ ) :
																						$selected = '';
																						if ( isset($selectedRoomTypes['room_types'][$room_type->id][$tariffKey]) ) :
																							$selected = ( $i == count( $selectedRoomTypes['room_types'][$room_type->id][$tariffKey] ) ) ? 'selected="selected"' : '';
																						endif;
																						echo '<option ' . $selected . ' value="' . $i . '">' . sprintf( _n( '1 room', '%d rooms', $i, 'solidres' ), $i ) . '</option>';
																					endfor;
																					?>
																				</select>
																				<?php
																				else :
																				?>
																					<button <?php echo $skip_room_form ? 'data-step="room"' : '' ?> type="button"
																						data-raid="<?php echo $asset->id ?>"
																						data-rtid="<?php echo $room_type->id ?>"
																						data-tariffid="<?php echo $tariffKey ?>"
																						data-adjoininglayer="<?php echo $tariffInfo['tariffAdjoiningLayer'] ?>"
																						data-totalroomsleft="<?php echo $room_type->totalAvailableRoom ?>"
																						class="btn btn-default <?php echo SR_UI_GRID_COL_12 ?> <?php echo $skip_room_form ? 'roomtype-reserve-exclusive' : 'roomtype-reserve' ?> quantity_<?php echo $room_type->id ?>">
																						<?php _e( 'Reserve', 'solidres' ) ?>
																					</button>
																				<?php endif ?>
																				<input type="hidden"
																					   name="srform[selected_tariffs][<?php echo $room_type->id ?>][]"
																					   value="<?php echo $tariffKey ?>"
																					   id="selected_tariff_<?php echo $room_type->id ?>_<?php echo $tariffKey ?>"
																					   class="selected_tariff_hidden_<?php echo $room_type->id ?>"
																					   disabled
																					/>
																				<div class="processing" style="display: none"></div>
																				<?php if ($is_exclusive && $skip_room_form) : ?>
																				<input type="hidden"
																				       data-raid="<?php echo $asset->id ?>"
																				       data-roomtypeid="<?php echo $room_type->id ?>"
																				       data-tariffid="<?php echo $tariffKey ?>"
																				       data-adjoininglayer="<?php echo $tariffInfo['tariffAdjoiningLayer'] ?>"
																				       data-roomindex="1"
																				       name="srform[room_types][<?php echo $room_type->id ?>][<?php echo $tariffKey ?>][1][adults_number]"
																				       value="1"
																				       disabled
																				/>
																				<?php endif ?>
																			<?php
																			endif;
																		endif;
																		?>
																	</div>
																</div>
															</div>

															<!-- check in form -->
															<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
																<div class="<?php echo SR_UI_GRID_COL_12 ?> checkinoutform" id="checkinoutform-<?php echo $room_type->id ?>-<?php echo $tariffKey ?>" style="display: none">

																</div>
															</div>
															<!-- /check in form -->

															<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
																<div
																	class="<?php echo SR_UI_GRID_COL_12 ?> room-form room-form-<?php echo $room_type->id ?>-<?php echo $tariffKey ?>"
																	id="room-form-<?php echo $room_type->id ?>-<?php echo $tariffKey ?>"
																	style="display: none">

																</div>
															</div>

														</div> <!-- end of span12 -->
													</div> <!-- end of row-fluid -->
												<?php
													endforeach;
												else :
													$link = esc_url( home_url( $post->post_name ) );
													echo '<div class="alert alert-notice">' . sprintf( __( 'We have no availability for this room type between %s and %s. <a href="%s">Click here to start over by changing your dates.</a>', 'solidres' ), $checkinFormatted->format($date_format), $checkoutFormatted->format($date_format), $link ) . '</div>';
												endif;
											endif;

											if ($is_fresh && $options_tariff[ 'show_frontend_tariffs' ] == 1) :
												if ( isset( $room_type->tariffs ) && is_array( $room_type->tariffs ) ) :
													foreach ( $room_type->tariffs as $tariff ) :
														$tariffIsSelected = '';

														if ( isset( $selectedTariffs[ $room_type->id ] ) ) :
															$tariffIsSelected = in_array( $tariff->id, $selectedTariffs[ $room_type->id ] ) ? 'selected' : '';
														endif;

														if ( isset( $selectedRoomTypes['room_types'][ $room_type->id ][ $tariff->id ] ) ) :
															$currentSelectedRoomNumberPerTariff[ $tariff->id ] = count( $selectedRoomTypes['room_types'][ $room_type->id ][ $tariff->id ] );
														endif;

														$min = 0;
														?>
														<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
															<div
																id="tariff-box-<?php echo $room_type->id ?>-<?php echo $tariff->id ?>"
																class="<?php echo SR_UI_GRID_COL_12 ?> tariff-box <?php echo $tariffIsSelected ?>">
																<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
																	<div class="<?php echo SR_UI_GRID_COL_5 ?> tariff-title-desc">
																		<strong><?php echo empty( $tariff->title ) ? __( 'Standard rate', 'solidres' ) : apply_filters( 'solidres_tariff_title', $tariff->title ) ?></strong>

																		<p><?php echo apply_filters( 'solidres_tariff_desc', $tariff->description ) ?></p>
																	</div>
																	<div class="<?php echo SR_UI_GRID_COL_4 ?> tariff-value ">
																		<?php
																		$show_price_with_tax = isset( $options_plugin['show_price_with_tax'] ) ? $options_plugin['show_price_with_tax'] : '';
																		echo $solidres_room_type->get_min_price( $tariff, $solidres_currency, $show_price_with_tax, $imposed_tax_types, $asset ) ?>
																	</div>
																	<div class="<?php echo SR_UI_GRID_COL_3 ?>">
																		<div class="inner">
																			<?php if ( $is_fresh ) : ?>
																				<button
																					class="btn btn-default btn-block trigger_checkinoutform"
																					type="button"
																					data-roomtypeid="<?php echo $room_type->id ?>"
																					data-itemid="<?php //echo $this->itemid ?>"
																					data-assetid="<?php echo $asset->id ?>"
																					data-tariffid="<?php echo $tariff->id ?>"
																					><?php _e( 'Select', 'solidres' ) ?></button>
																			<?php else :
																				if ( isset ( $room_type->totalAvailableRoom ) ) :
																					if ( $room_type->totalAvailableRoom == 0 ) :
																						_e( 'Sold out!', 'solidres' );
																					else :
																						?>
																						<select
																							name="solidres[ign<?php echo $tariff->id ?>]"
																							data-raid="<?php echo $asset->id ?>"
																							data-rtid="<?php echo $room_type->id ?>"
																							data-tariffid="<?php echo $tariff->id ?>"
																							data-adjoininglayer="<?php echo $tariffInfo['tariffAdjoiningLayer'] ?>"
																							data-totalroomsleft="<?php echo $room_type->totalAvailableRoom ?>"
																							class="form-control input-block-level roomtype-quantity-selection quantity_<?php echo $room_type->id ?>">
																							<option
																								value="0"><?php _e( 'Quantity', 'solidres' ) ?></option>
																							<?php
																							for ( $i = 1; $i <= $room_type->totalAvailableRoom; $i ++ ) :
																								$selected = '';
																								if ( isset( $currentSelectedRoomNumberPerTariff[ $tariff->id ] ) ) :
																									$selected = ( $i == $currentSelectedRoomNumberPerTariff[ $tariff->id ] ) ? 'selected' : '';
																								endif;

																								echo '<option ' . $selected . ' value="' . $i . '">' . sprintf( _n( '1 room', '%d rooms', $i, 'solidres' ), $i ) . '</option>';
																							endfor;
																							?>
																						</select>
																						<input type="hidden"
																							   name="srform[selected_tariffs][<?php echo $room_type->id ?>][]"
																							   value="<?php echo $tariff->id ?>"
																							   id="selected_tariff_<?php echo $room_type->id ?>_<?php echo $tariff->id ?>"
																							   class="selected_tariff_hidden_<?php echo $room_type->id ?>"
																							   disabled
																							/>
																						<div class="processing" style="display: none"></div>
																					<?php
																					endif;
																				endif;
																			endif;
																			?>
																		</div>
																	</div>
																</div>

																<!-- check in form -->
																<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
																	<div class="<?php echo SR_UI_GRID_COL_12 ?> checkinoutform"
																		 id="checkinoutform-<?php echo $room_type->id ?>-<?php echo $tariff->id ?>"
																		 style="display: none">

																	</div>
																</div>
																<!-- /check in form -->


																<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
																	<div
																		class="<?php echo SR_UI_GRID_COL_12 ?> room-form room-form-<?php echo $room_type->id ?>-<?php echo $room_type->id ?>"
																		id="room-form-<?php echo $room_type->id ?>-<?php echo $tariff->id ?>"
																		style="display: none">

																	</div>
																</div>


															</div> <!-- end of span12 -->
														</div> <!-- end of row-fluid -->
													<?php
													endforeach; // end foreach of complex tariffs
												endif;
											endif // end if in line 274
											?>
										</div>
									</div> <!-- end of span12 -->
								</div> <!-- end of row-fluid -->
							</div> <!-- end of span12 -->
						</div> <!-- end of row-fluid -->

						<?php
						$count ++;
					endforeach; ?>
				<?php
				else :
					?>
					<div class="alert alert-warning">
						<?php
						if ($is_fresh) :
							echo __('We found no matched rooms for your search, please adjust your booking dates or room options.', 'solidres');
						else :
							echo sprintf( __('We found no matched rooms for your search from %s to %s, please adjust your booking dates or room options.', 'solidres'), $checkinFormatted->format($date_format), $checkoutFormatted->format($date_format) );
						endif;
						?>
						<a class="" href="<?php echo esc_attr( home_url( '/' . $post->post_name ) ) ?>"><i class="fa fa-refresh"></i> <?php _e( 'Reset', 'solidres' )?></a>
					</div>
				<?php
				endif;
				?>

				<?php if (!$is_fresh && count($room_types) > 0) : ?>
					<div class="<?php echo SR_UI_GRID_CONTAINER ?> button-row button-row-bottom">
						<div class="<?php echo SR_UI_GRID_COL_9 ?>">
							<div class="inner">
								<p><?php _e( 'Select your room type, review the prices and click Next to continue', 'solidres' ) ?></p>
							</div>
						</div>
						<div class="<?php echo SR_UI_GRID_COL_3 ?>">
							<div class="inner">
								<div class="btn-group">
									<button data-step="room" type="submit" class="btn btn-success">
										<i class="fa fa-arrow-right"></i> <?php _e( 'Next', 'solidres' ) ?>
									</button>
								</div>
							</div>
						</div>
					</div>
				<?php endif ?>

				<input type="hidden" name="srform[customer_id]" value="" />
				<input type="hidden" name="srform[raid]" value="<?php echo $asset->id ?>" />
				<input type="hidden" name="srform[state]" value="0" />
				<input type="hidden" name="srform[next_step]" value="guestinfo" />
				<input type="hidden" name="step" value="room" />
				<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'process-reservation' ) ?>" />
				<input type="hidden" name="action" value="solidres_reservation_process" />
				<input type="hidden" name="srform[bookingconditions]" value="<?php //echo $this->item->params['termsofuse'] ?>" />
				<input type="hidden" name="srform[privacypolicy]" value="<?php //echo $this->item->params['privacypolicy'] ?>" />

			</form>
		</div>
		<!-- /Tab 1 -->
	</div>
	<div class="step-pane" id="step2">
		<!-- Tab 2 -->
		<div class="reservation-single-step-holder guestinfo nodisplay"></div>
		<!-- /Tab 2 -->
	</div>
	<div class="step-pane" id="step3">
		<!-- Tab 3 -->
		<div class="reservation-single-step-holder confirmation nodisplay"></div>
		<!-- /Tab 3 -->
	</div>
</div>