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

$charge_types = array(
	0 => __( 'Per room', 'solidres' ),
	1 => __( 'Per booking', 'solidres' ),
	2 => __( 'Per booking per stay (night or day)', 'solidres' ),
	3 => __( 'Per booking per person', 'solidres' ),
	4 => __( 'Per room per stay', 'solidres' ),
	5 => __( 'Per room per person', 'solidres' ),
	6 => __( 'Per room per person per stay', 'solidres' )
);
?>

<div id=extra_general_infomation" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'General infomartion', 'solidres' ); ?></span></h3>

	<div class="inside">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><label for="srform_name" title=""><?php _e( 'Extra name', 'solidres' ); ?> <span
							class="required">*</span></label></th>
				<td><input type="text" name="srform[name]" maxlength="255"
				           value="<?php echo isset( $sr_form_data->name ) ? $sr_form_data->name : ''; ?>"
				           id="srform_name" class="regular-text" required></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_asset"
				                         title="<?php _e( 'Enter the name of your Extra/Services. For example: Airport pickup or Spa.', 'solidres' ); ?>"><?php _e( 'Reservation Asset', 'solidres' ); ?>
						<span class="required">*</span></label></th>
				<td>
					<select name="srform[reservation_asset_id]"
					        class=" srform_select_extra_reservation_asset_id" id="srform_asset" required>
						<option value=""><?php _e( 'Reservation Asset', 'solidres' ); ?></option>
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
				<th scope="row"><label for="srform_tax"
				                         title="<?php _e( 'Select the tax that will apply to this extra item. The tax list is depend on the selected reservation asset above. Please select a reservation asset first and the tax list will be displayed accordingly.', 'solidres' ); ?>"><?php _e( 'Tax', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[tax_id]" class="" id="srform_tax">
						<option value=""><?php _e( 'Select Tax', 'solidres' ); ?></option>
						<?php echo isset( $sr_form_data->tax_id ) ? SR_Helper::render_list_tax( $sr_form_data->tax_id ) : SR_Helper::render_list_tax(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_state"
				                         title="<?php _e( 'Select the state of this Extra/Service, only published Extra/Service can be used.', 'solidres' ); ?>"><?php _e( 'State', 'solidres' ); ?></label>
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
			<tr>
				<th scope="row"><label for="srform_mandatory"
				                         title="<?php _e( 'Mandatory extra item will be always selected and the guests are not able to change it', 'solidres' ); ?>"><?php _e( 'Mandatory', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[mandatory]" class="" id="srform_mandatory">
						<option value="0" <?php if ( isset( $sr_form_data->mandatory ) ) {
							echo $sr_form_data->mandatory == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->mandatory ) ) {
							echo $sr_form_data->mandatory == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_charge_type"
				                         title="<?php _e( 'Select the charge type of this extra item. The FREE version supports 2 basic charge types: Per booking and Per Room. With Advanced Extra plugin, 5 more charge types will be available for choosing. If you choose Per person charge type, you need to enter values into fields Price Adult and Price Children.', 'solidres' ); ?>"><?php _e( 'Charge type', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[charge_type]" class="" id="srform_charge_type">
						<?php
						foreach ($charge_types as $charge_type_key => $charge_type_name) :
							$charge_type_disabled = 'disabled';
							if (defined('SR_PLUGIN_ADVANCEDEXTRA_ENABLED') && SR_PLUGIN_ADVANCEDEXTRA_ENABLED) :
								$charge_type_disabled = '';
							endif;
							?>
						<option
							<?php echo $charge_type_key != 0 && $charge_type_key != 1 ? $charge_type_disabled : '' ?>
							<?php echo isset($sr_form_data->charge_type) && $sr_form_data->charge_type == $charge_type_key ? 'selected' : '' ?>
							value="<?php echo $charge_type_key ?>">
							<?php echo $charge_type_name ?>
						</option>
						<?php endforeach ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_max_quantity"
				                         title="<?php _e( 'Enter the maximum extra quantity that can be selected in the reservation process.', 'solidres' ); ?>"><?php _e( 'Max Quantity', 'solidres' ); ?></label>
				</th>
				<td><input type="number" name="srform[max_quantity]" maxlength="11"
				           value="<?php echo isset( $sr_form_data->max_quantity ) ? $sr_form_data->max_quantity : ''; ?>"
				           id="srform_max_quantity" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_daily_chargable"
				                         title="<?php _e( 'Select No if this Extra/Service is free.', 'solidres' ); ?>"><?php _e( 'Chargable', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[daily_chargable]" class="" id="srform_daily_chargable">
						<option value="0" <?php if ( isset( $sr_form_data->daily_chargable ) ) {
							echo $sr_form_data->daily_chargable == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->daily_chargable ) ) {
							echo $sr_form_data->daily_chargable == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_price"
				                         title="<?php _e( 'Enter the price of this Extra/Service. The currency of Reservation Asset will apply here.', 'solidres' ); ?>"><?php _e( 'Price', 'solidres' ); ?></label>
				</th>
				<td><input type="number" name="srform[price]" maxlength="12"
				           value="<?php echo isset( $sr_form_data->price ) ? $sr_form_data->price : ''; ?>"
				           id="srform_price" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_price_adult"
				                         title="<?php _e( 'Enter the price for adult of this Extra/Service. The currency of Reservation Asset will apply here.', 'solidres' ); ?>"><?php _e( 'Price for adult', 'solidres' ); ?></label>
				</th>
				<td><input type="number" name="srform[price_adult]" maxlength="12"
				           value="<?php echo isset( $sr_form_data->price_adult ) ? $sr_form_data->price_adult : ''; ?>"
				           id="srform_price_adult" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_price_child"
				                         title="<?php _e( 'Enter the price for child of this Extra/Service. The currency of Reservation Asset will apply here.', 'solidres' ); ?>"><?php _e( 'Price for child', 'solidres' ); ?></label>
				</th>
				<td><input type="number" name="srform[price_child]" maxlength="12"
				           value="<?php echo isset( $sr_form_data->price_child ) ? $sr_form_data->price_child : ''; ?>"
				           id="srform_price_child" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_description"
				                         title="<?php _e( 'Enter some text to decribe your Extra/Service', 'solidres' ); ?>"><?php _e( 'Description', 'solidres' ); ?></label>
				</th>
				<td><textarea class="srform_textarea" rows="5" name="srform[description]"
				              id="srform_description"><?php echo isset( $sr_form_data->description ) ? $sr_form_data->description : ''; ?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>