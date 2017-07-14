<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

global $current_user;
get_currentuserinfo();
$is_front_end = solidres()->is_request( 'frontend' );
?>
<form enctype="multipart/form-data"
	  id="sr-reservation-form-guest"
	  class="sr-reservation-form form-stacked sr-validate"
	  action="index.php"
	  method="POST">

	<input type="hidden" name="action" value="solidres_reservation_process"/>
	<input type="hidden" name="step" value="guestinfo"/>
	<input type="hidden" name="security" value="<?php echo wp_create_nonce( 'process-reservation' ) ?>" />

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row button-row button-row-top">
		<div class="<?php echo SR_UI_GRID_COL_8 ?> eight columns">
			<div class="inner">
				<p><?php _e( 'Enter your information and payment method', 'solidres' ) ?></p>
			</div>
		</div>
		<div class="<?php echo SR_UI_GRID_COL_4 ?> four columns">
			<div class="inner">
				<div class="btn-group">
					<button type="button" class="btn btn-default reservation-navigate-back" data-step="guestinfo"
					        data-prevstep="room">
						<i class="fa fa-arrow-left"></i> <?php _e( 'Back', 'solidres' ) ?>
					</button>
					<button data-step="guestinfo" type="submit" class="btn btn-success">
						<i class="fa fa-arrow-right"></i> <?php _e( 'Next', 'solidres' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
		<div class="<?php echo SR_UI_GRID_COL_12 ?> twelve columns">
			<div class="inner">
				<h3><?php _e( 'Guest information', 'solidres' ) ?></h3>
			</div>
		</div>
	</div>

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
		<div class="<?php echo SR_UI_GRID_COL_6 ?> six columns">
			<div class="inner">

				<fieldset>
					<div class="form-group">
						<label for="customer_title">
							<?php _e( 'Your title (Optional)', 'solidres' ) ?>
						</label>

						<?php echo SR_Helper::get_generic_list(
							$display_data['customer_titles'],
							array( 'name' => 'srform[customer_title]', 'class' => 'form-control input-block-level twelve columns' ),
							$display_data['reservation_details_guest']["customer_title"]
						) ?>
					</div>

					<div class="form-group">
						<label for="firstname">
							<?php _e( 'First name', 'solidres' ) ?>
						</label>
						<input id="firstname"
							   required
							   name="srform[customer_firstname]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="255"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_firstname"] ) ? $display_data['reservation_details_guest']["customer_firstname"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="middlename">
							<?php _e( 'Middlename (Optional)', 'solidres' ) ?>
						</label>
						<input id="middlename"
							   name="srform[customer_middlename]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="255"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_middlename"] ) ? $display_data['reservation_details_guest']["customer_middlename"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="lastname">
							<?php _e( 'Last name', 'solidres' ) ?>
						</label>
						<input id="lastname"
							   required
							   name="srform[customer_lastname]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="255"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_lastname"] ) ? $display_data['reservation_details_guest']["customer_lastname"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="email">
							<?php _e( 'Email', 'solidres' ) ?>
						</label>
						<input id="email"
							   required
							   name="srform[customer_email]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="255"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_email"] ) ? $display_data['reservation_details_guest']["customer_email"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="phonenumber">
							<?php _e( 'Phone number', 'solidres' ) ?>
						</label>
						<input id="phonenumber"
							   required
							   name="srform[customer_phonenumber]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="45"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_phonenumber"] ) ? $display_data['reservation_details_guest']["customer_phonenumber"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="mobilephone">
							<?php _e( 'Mobile phone', 'solidres' ) ?>
						</label>
						<input id="mobilephone"
						       required
						       name="srform[customer_mobilephone]"
						       type="text"
						       class="form-control input-block-level twelve columns"
						       maxlength="45"
						       value="<?php echo( isset( $display_data['reservation_details_guest']["customer_mobilephone"] ) ? $display_data['reservation_details_guest']["customer_mobilephone"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="company">
							<?php _e( 'Company (Optional)', 'solidres' ) ?>
						</label>
						<input id="company"
							   name="srform[customer_company]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="45"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_company"] ) ? $display_data['reservation_details_guest']["customer_company"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="address1">
							<?php _e( 'Address 1', 'solidres' ) ?>
						</label>
						<input id="address1"
							   required
							   name="srform[customer_address1]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="45"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_address1"] ) ? $display_data['reservation_details_guest']["customer_address1"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="address2">
							<?php _e( 'Address 2 (Optional)', 'solidres' ) ?>
						</label>
						<input id="address2"
							   name="srform[customer_address2]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="45"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_address2"] ) ? $display_data['reservation_details_guest']["customer_address2"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="address_2">
							<?php _e( 'VAT Number (Optional)', 'solidres' ) ?>
						</label>
						<input id="address_2"
							   name="srform[customer_vat_number]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="255"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_vat_number"] ) ? $display_data['reservation_details_guest']["customer_vat_number"] : "" ) ?>"/>
					</div>
				</fieldset>
			</div>
		</div>

		<div class="<?php echo SR_UI_GRID_COL_6 ?> six columns">
			<div class="inner">
				<fieldset>
					<div class="form-group">
						<label for="city"><?php _e( 'City', 'solidres' ) ?></label>
						<input id="city"
							   required
							   name="srform[customer_city]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="45"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_city"] ) ? $display_data['reservation_details_guest']["customer_city"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="zip"><?php _e( 'Zip/Postal code (Optional)', 'solidres' ) ?></label>
						<input id="zip"
							   name="srform[customer_zipcode]"
							   type="text"
							   class="form-control input-block-level twelve columns"
							   maxlength="45"
							   value="<?php echo( isset( $display_data['reservation_details_guest']["customer_zipcode"] ) ? $display_data['reservation_details_guest']["customer_zipcode"] : "" ) ?>"/>
					</div>

					<div class="form-group">
						<label for="srform[country_id]"><?php _e( 'Country', 'solidres' ) ?></label>

						<select name="srform[customer_country_id]" class="country_select form-control input-block-level twelve columns" required>
							<?php echo $display_data['countries'] ?>
						</select>
					</div>

					<div class="form-group">
						<label for="srform[customer_geo_state_id]"><?php _e( 'State/Province (Optional)', 'solidres' ) ?></label>
						<select name="srform[customer_geo_state_id]" class="state_select form-control input-block-level twelve columns">
							<?php echo $display_data['geo_states'] ?>
						</select>
					</div>

					<div class="form-group">
						<label for="note"><?php _e( 'Note (Optional)', 'solidres' ) ?></label>
						<textarea id="note" name="srform[note]" rows="10" cols="30"
							  class="form-control input-block-level twelve columns"><?php echo( isset( $display_data['reservation_details_guest']["note"] ) ? $display_data['reservation_details_guest']["note"] : "" ) ?></textarea>

						<p class="help-block"><?php _e( 'Enter any information you wish to attach to your reservation. The staff cannot guarantee additional requests or comments. Please avoid the use of special characters.', 'solidres' ) ?></p>
					</div>

					<?php if ( defined( 'SR_PLUGIN_USER_ENABLED' ) && true == SR_PLUGIN_USER_ENABLED && $current_user->ID <= 0 ) : ?>
					<div class="form-group">
						<label class="checkbox">
							<input id="register_an_account_form"
								   type="checkbox"> <?php _e( 'Register with us for future convenience: fast and easy booking. Please enter your desired username and password in the following fields.', 'solidres' ) ?>
						</label>
						<div class="register_an_account_form" style="display: none">
							<label for="username">
								<?php _e( 'Username', 'solidres' ) ?>
							</label>
							<input id="customer_username"
								   name="srform[customer_username]"
								   type="text"
								   class="form-control input-block-level twelve columns"
								   maxlength="60"
								   value=""/>

							<label for="password">
								<?php _e( 'Password', 'solidres' ) ?>
							</label>
							<input id="customer_password"
								   name="srform[customer_password]"
								   type="password"
								   class="form-control input-block-level twelve columns"
								   value=""
								   autocomplete="off"
								/>
						</div>
					</div>
					<?php endif ?>
				</fieldset>
			</div>
		</div>
	</div>

	<?php
	// Show Per Booking Extras
	if (count( $display_data['extras'] )) :
	?>
	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
		<div class="<?php echo SR_UI_GRID_COL_12 ?> twelve columns">
			<div class="inner">
				<h3><?php _e( 'Enhance your stay', 'solidres' ) ?></h3>
			</div>
		</div>
	</div>

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
		<div class="<?php echo SR_UI_GRID_COL_12 ?> twelve columns">
			<div class="inner">
				<?php
				foreach ( $display_data['extras'] as $extra ) :
					$extraInputCommonName = 'srform[extras][' . $extra->id . ']';
					$checked              = '';
					$disabledCheckbox     = '';
					$disabledSelect       = 'disabled="disabled"';
					$alreadySelected      = false;
					if ( isset( $display_data['reservation_details_guest']['extras'] ) ) :
						$alreadySelected = array_key_exists( $extra->id, (array) $display_data['reservation_details_guest']['extras'] );
					endif;

					if ( $extra->mandatory == 1 || $alreadySelected ) :
						$checked = 'checked="checked"';
					endif;

					if ( $extra->mandatory == 1 ) :
						$disabledCheckbox = 'disabled="disabled"';
						$disabledSelect   = '';
					endif;

					if ( $alreadySelected && $extra->mandatory == 0 ) :
						$disabledSelect = '';
					endif;
					?>
					<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
						<div class="<?php echo SR_UI_GRID_COL_12 ?> extras_row_guestform">
						<input <?php echo $checked ?> <?php echo $disabledCheckbox ?> type="checkbox"
																					  data-target="guest_extra_<?php echo $extra->id ?>"/>

						<?php
						if ( $extra->mandatory == 1 ) :
							?>
							<input type="hidden" name="<?php echo $extraInputCommonName ?>[quantity]" value="1"/>
						<?php
						endif;
						?>
						<select class="<?php echo SR_UI_GRID_COL_2 ?> guest_extra_<?php echo $extra->id ?>"
								name="<?php echo $extraInputCommonName ?>[quantity]"
							<?php echo $disabledSelect ?>>
							<?php
							for ( $quantitySelection = 1; $quantitySelection <= $extra->max_quantity; $quantitySelection ++ ) :
								$checked = '';
								if ( isset( $display_data['reservation_details_guest']['extras'][ $extra->id ]['quantity'] ) ) :
									$checked = ( $display_data['reservation_details_guest']['extras'][ $extra->id ]['quantity'] == $quantitySelection ) ? 'selected="selected"' : '';
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
							   data-target="extra_details_<?php echo $extra->id ?>">
								<?php _e( 'Details', 'solidres' ) ?>
							</a>
						</span>
						<span class="extra_details" id="extra_details_<?php echo $extra->id ?>" style="display: none">
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
				endif;
				?>
			</div>
		</div>
	</div>
	<?php
	// Show available payment methods
	$solidres_payment_config_data = new SR_Config( array( 'scope_id' => $display_data['assetId'] ) );
	?>
	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
		<div class="<?php echo SR_UI_GRID_COL_12 ?> twelve columns">
			<div class="inner">
				<h3><?php _e( 'Payment information', 'solidres' ) ?></h3>
			</div>
		</div>
	</div>

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row">
		<div class="<?php echo SR_UI_GRID_COL_12 ?> twelve columns">
			<div class="inner">
				<ul class="unstyled list-unstyled payment_method_list">
					<?php
					if( ! empty( $display_data['solidresPaymentPlugins'] ) ) :
						foreach ( $display_data['solidresPaymentPlugins']->payment_gateways as $key => $gateway ) :
							$paymentPluginId = $gateway->identifier;

							if ( $solidres_payment_config_data->get( "payments/$paymentPluginId/{$paymentPluginId}_enabled" ) == 0 ):
								continue;
							endif;

							$checked = '';
							if ( isset( $display_data['reservation_details_guest']["payment_method_id"] ) ) :
								if ( $display_data['reservation_details_guest']["payment_method_id"] == $paymentPluginId ) :
									$checked = "checked";
								endif;
							else :
								if ( $solidres_payment_config_data->get( "payments/$paymentPluginId/{$paymentPluginId}_is_default" ) == 1 ):
									$checked = "checked";
								endif;
							endif;

							// Load custom payment plugin field template if it is available, otherwise just render it normally
							$fieldTemplatePath = WP_PLUGIN_DIR . '/solidres-' . $paymentPluginId . '/includes/field.php';
							if ( file_exists( $fieldTemplatePath ) ) :
								@ob_start();
								include $fieldTemplatePath;
								echo @ob_get_clean();
							else :
								?>
								<li>
									<input id="payment_method_<?php echo $paymentPluginId ?>"
									   type="radio"
									   name="srform[payment_method_id]"
									   value="<?php echo $paymentPluginId ?>"
									   class="payment_method_radio"
									<?php echo $checked ?>
										/>
									<span class="popover_payment_methods"
										  data-content="<?php echo $solidres_payment_config_data->get( 'payments/' . $paymentPluginId . '/' . $paymentPluginId . '_frontend_message') ?>"
										  data-title="<?php echo $gateway->title; ?>" >
										<?php echo $gateway->title; ?>
										<i class="fa fa-question-cirlce"></i>
									</span>
								</li>
							<?php
							endif;
						endforeach;
					endif; ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="<?php echo SR_UI_GRID_CONTAINER ?> sr_row button-row button-row-bottom">
		<div class="<?php echo SR_UI_GRID_COL_8 ?> eight columns">
			<div class="inner">
				<p><?php _e( 'Enter your information and payment method', 'solidres' ) ?></p>
			</div>
		</div>
		<div class="<?php echo SR_UI_GRID_COL_4 ?> four columns">
			<div class="inner">
				<div class="btn-group">
					<button type="button" class="btn btn-default reservation-navigate-back" data-step="guestinfo"
					        data-prevstep="room">
						<i class="fa fa-arrow-left"></i> <?php _e( 'Back', 'solidres' ) ?>
					</button>
					<button data-step="guestinfo" type="submit" class="btn btn-success">
						<i class="fa fa-arrow-right"></i> <?php _e( 'Next', 'solidres' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="srform[next_step]" value="confirmation"/>
</form>