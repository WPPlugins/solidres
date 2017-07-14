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
$today = date( 'Y-m-d' );
$users = 'solidres-user/solidres-user.php';
?>

<div id="coupon_general_infomation" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'General infomartion', 'solidres' ); ?></span></h3>

	<div class="inside">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><label for="srform_coupon_name"
				                         title="<?php _e( 'Enter the name of your coupon', 'solidres' ); ?>"><?php _e( 'Coupon name', 'solidres' ); ?>
						<span class="required">*</span></label></th>
				<td><input type="text" name="srform[coupon_name]" maxlength="255"
				           value="<?php echo isset( $sr_form_data->coupon_name ) ? $sr_form_data->coupon_name : ''; ?>"
				           id="srform_coupon_name" class="regular-text" required></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_coupon_code"
				                         title="<?php _e( 'Enter the coupon code, for example: NEWYEAR', 'solidres' ); ?>"><?php _e( 'Coupon code', 'solidres' ); ?>
						<span class="required">*</span></label></th>
				<td><input type="text" name="srform[coupon_code]" maxlength="15"
				           value="<?php echo isset( $sr_form_data->coupon_code ) ? $sr_form_data->coupon_code : ''; ?>"
				           id="srform_coupon_code" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_amount"
				                         title="<?php _e( "Enter the value of your coupon. Value can be a fixed number or percentage. Note: the coupon value's currency depends on the reservation asset's currency", 'solidres' ); ?>"><?php _e( 'Coupon value', 'solidres' ); ?>
						<span class="required">*</span></label></th>
				<td><input type="number" name="srform[amount]" size="30" maxlength="11"
				           value="<?php echo isset( $sr_form_data->amount ) ? $sr_form_data->amount : ''; ?>"
				           id="srform_amount" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_asset"
				                         title="<?php _e( 'The reservation asset which this coupon belongs to.', 'solidres' ); ?>"><?php _e( 'Reservation Asset', 'solidres' ); ?>
						<span class="required">*</span></label></th>
				<td>
					<select name="srform[reservation_asset_id]"
					        class="srform_select_coupon_reservation_asset_id" id="srform_asset"
					        required>
						<option value=""><?php _e( 'Select Asset', 'solidres' ); ?></option>
                        <?php
                        if ( current_user_can( 'solidres_partner' ) ) {
                            echo isset( $sr_form_data->reservation_asset_id ) ? SR_Helper::render_list_asset( $sr_form_data->reservation_asset_id, $author_id ) : SR_Helper::render_list_asset( 0, $author_id );
                        } else {
                            echo isset( $sr_form_data->reservation_asset_id ) ? SR_Helper::render_list_asset( $sr_form_data->reservation_asset_id ) : SR_Helper::render_list_asset();
                        }
                        ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_is_percent"
				                         title="<?php _e( 'Is this coupon percentage based?', 'solidres' ); ?>"><?php _e( 'Coupon percent', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[is_percent]" class="" id="srform_is_percent">
						<option value="0" <?php if ( isset( $sr_form_data->is_percent ) ) {
							echo $sr_form_data->is_percent == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->is_percent ) ) {
							echo $sr_form_data->is_percent == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_valid_from"
				                         title="<?php _e( 'The date coupon is valid to use from', 'solidres' ); ?>"><?php _e( 'Valid from', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[valid_from]"
				           value="<?php echo date( 'd-m-Y', strtotime( isset( $sr_form_data->valid_from ) ? $sr_form_data->valid_from : $today ) ); ?>"
				           id="srform_valid_from" class="regular-text srform_datepicker"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_valid_to"
				                         title="<?php _e( 'The date coupon is valid to use to', 'solidres' ); ?>"><?php _e( 'Valid to', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[valid_to]"
				           value="<?php echo date( 'd-m-Y', strtotime( isset( $sr_form_data->valid_to ) ? $sr_form_data->valid_to : $today ) ); ?>"
				           id="srform_valid_to" class="regular-text srform_datepicker"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_valid_from_checkin"
				                         title="<?php _e( "Coupon is only valid to be used if the reservation's checkin date is within the Valid from checkin/Valid to checkin period", 'solidres' ); ?>"><?php _e( 'Valid from checkin', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[valid_from_checkin]"
				           value="<?php echo date( 'd-m-Y', strtotime( isset( $sr_form_data->valid_from_checkin ) ? $sr_form_data->valid_from_checkin : $today ) ); ?>"
				           id="srform_valid_from_checkin" class="regular-text srform_datepicker"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_valid_to_checkin"
				                         title="<?php _e( "Coupon is only valid to be used if the reservation's checkin date is within the Valid from checkin/Valid to checkin period", 'solidres' ); ?>"> <?php _e( 'Valid to checkin', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[valid_to_checkin]"
				           value="<?php echo date( 'd-m-Y', strtotime( isset( $sr_form_data->valid_to_checkin ) ? $sr_form_data->valid_to_checkin : $today ) ); ?>"
				           id="srform_valid_to_checkin" class="regular-text srform_datepicker"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_customer_group"
				                         title="<?php _e( "Select a group for your customer. Note: this group is different with Joomla's user groups. By default, all customers created belong to Joomla's group Registered.", 'solidres' ); ?>"><?php _e( 'User group', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[customer_group_id]" class=""
					        id="srform_customer_group" <?php echo ( ! is_plugin_active( $users ) ) ? 'disabled' : ''; ?> >
						<option value=""><?php _e( 'Public', 'solidres' ); ?></option>
						<?php echo isset( $sr_form_data->customer_group_id ) ? SR_Helper::render_list_usergroup( $sr_form_data->customer_group_id ) : SR_Helper::render_list_usergroup(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_quantity"
				                         title="<?php _e( 'Enter the available quantity of this coupon, leave this field empty means unlimited.', 'solidres' ); ?>"><?php _e( 'Quantity', 'solidres' ); ?></label>
				</th>
				<td><input type="number" name="srform[quantity]" maxlength="11"
				           value="<?php echo isset( $sr_form_data->quantity ) ? $sr_form_data->quantity : ''; ?>"
				           id="srform_quantity" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_state"
				                         title="<?php _e( 'Please select a state of this coupon. Only published coupon can be used.', 'solidres' ); ?>"> <?php _e( 'State', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[state]" class="" id="srform_state">
						<option value="0" <?php if ( isset( $sr_form_data->state ) ) {
							echo $sr_form_data->state == 0 ? 'selected' : '';
						} ?> ><?php _e( 'Unpublished', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->state ) ) {
							echo $sr_form_data->state == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Published', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>