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

<table class="form-table">
	<tbody>
	<tr>
		<th scope="row"><label for="srform_currency_name"
		                         title="<?php _e( 'For example: Dollar, Euro', 'solidres' ); ?>"><?php _e( 'Currency name', 'solidres' ); ?>
				<span class="required">*</span></label></th>
		<td><input type="text" name="srform[currency_name]" maxlength="45"
		           value="<?php echo isset( $sr_form_data->currency_name ) ? $sr_form_data->currency_name : '' ?>"
		           id="srform_currency_name" required class="regular-text"></td>
	</tr>
	<tr>
		<th scope="row"><label for="srform_currency_code"
		                         title="<?php _e( 'For example: USD, EUR', 'solidres' ); ?>"><?php _e( 'Currency code', 'solidres' ); ?>
				<span class="required">*</span></label></th>
		<td><input type="text" name="srform[currency_code]" maxlength="10"
		           value="<?php echo isset( $sr_form_data->currency_code ) ? $sr_form_data->currency_code : '' ?>"
		           id="srform_currency_code" required class="regular-text"></td>
	</tr>
	<tr>
		<th scope="row"><label for="srform_sign"
		                         title="<?php _e( 'Currency symbol', 'solidres' ); ?>"><?php _e( 'Symbol', 'solidres' ); ?>
				<span class="required">*</span></label></th>
		<td><input type="text" name="srform[sign]" maxlength="10"
		           value="<?php echo isset( $sr_form_data->sign ) ? $sr_form_data->sign : '' ?>" id="srform_sign"
		           required class="regular-text"></td>
	</tr>
	<tr>
		<th scope="row"><label for="srform_exchange_rate"
		                         title="<?php _e( 'Exchange rate with Dollar, for example if your currency is Euro, the exchange rate should be about 1.2', 'solidres' ); ?>"><?php _e( 'Exchange rate', 'solidres' ); ?>
				<span class="required">*</span></label></th>
		<td><input type="number" name="srform[exchange_rate]"
		           value="<?php echo isset( $sr_form_data->exchange_rate ) ? $sr_form_data->exchange_rate : '' ?>"
		           id="srform_exchange_rate" required maxlength="7" class="regular-text"></td>
	</tr>
	<tr>
		<th scope="row"><label for="srform_state"
		                         title="<?php _e( 'Enable or disable this currency', 'solidres' ); ?>"><?php _e( 'State', 'solidres' ); ?></label>
		</th>
		<td>
			<select name="srform[state]" class="solidres_select" id="srform_state">
				<option value="0" <?php if ( isset( $sr_form_data->state ) ) {
					echo $sr_form_data->state == 0 ? 'selected' : '';
				} ?> ><?php _e( 'Unpublished', 'solidres' ); ?></option>
				<option value="1" <?php if ( isset( $sr_form_data->state ) ) {
					echo $sr_form_data->state == 1 ? 'selected' : '';
				} ?> ><?php _e( 'Published', 'solidres' ); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="srform_filter_range"
		                         title="<?php _e( 'Specify the filter range which will be used by HUB plugin in the front end for filtering purpose.', 'solidres' ); ?>"><?php _e( 'Filter range', 'solidres' ); ?></label>
		</th>
		<td><textarea class="srform_textarea" rows="5" name="srform[filter_range]" maxlength="255"
		              id="srform_filter_range"><?php echo isset( $sr_form_data->filter_range ) ? $sr_form_data->filter_range : '' ?></textarea>
		</td>
	</tr>
	</tbody>
</table>