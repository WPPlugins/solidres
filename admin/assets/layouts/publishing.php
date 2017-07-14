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

if ( isset( $sr_form_data->params ) && ! empty( $sr_form_data->params ) ) {
	if ( isset( $_POST['save_asset'] ) ) {
		$json_param = $sr_form_data->params;
	} else {
		$json_param = json_decode( $sr_form_data->params, true );
	}
} ?>

<div id="asset_publishing" class="postbox closed">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Publishing', 'solidres' ); ?></span></h3>

	<div class="inside">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><label for="srform_state"
				                         title="<?php _e( 'The status of this item.', 'solidres' ); ?>"><?php _e( 'Status', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[state]" class="" id="srform_state">
						<option value="0" <?php if ( isset( $sr_form_data->state ) ) {
							echo $sr_form_data->state == 0 ? 'selected' : '';
						} ?> ><?php _e( 'Unpublished', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->state ) ) {
							echo $sr_form_data->state == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Published', 'solidres' ); ?></option>
						<option value="-2" <?php if ( isset( $sr_form_data->state ) ) {
							echo $sr_form_data->state == - 2 ? 'selected' : '';
						} ?> ><?php _e( 'Trashed', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_default"
				                         title="<?php _e( 'The front-end only checks room availability against the default reservation asset.', 'solidres' ); ?>"><?php _e( 'Default', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[default]" class="" id="srform_default">
						<option value="0" <?php if ( isset( $sr_form_data->default ) ) {
							echo $sr_form_data->default == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->default ) ) {
							echo $sr_form_data->default == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_rating"
				                         title="<?php _e( 'Select a rating for your asset', 'solidres' ); ?>"><?php _e( 'Rating', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[rating]" class="" id="srform_rating">
						<?php echo isset( $sr_form_data->rating ) ? SR_Helper::render_list_rating( $sr_form_data->rating ) : SR_Helper::render_list_rating(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_deposit_required"
				                         title="<?php _e( 'If select YES, deposit amount will be calculated and the guest will be charged.', 'solidres' ); ?>"><?php _e( 'Deposit required', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[deposit_required]" class="" id="srform_deposit_required">
						<option value="0" <?php if ( isset( $sr_form_data->deposit_required ) ) {
							echo $sr_form_data->deposit_required == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->deposit_required ) ) {
							echo $sr_form_data->deposit_required == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_deposit_is_percentage"
				                         title="<?php _e( 'The deposit amount is a flat number or percentage of total booking cost.', 'solidres' ); ?>"><?php _e( 'Deposit is percentage', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[deposit_is_percentage]" class=""
					        id="srform_deposit_is_percentage">
						<option value="0" <?php if ( isset( $sr_form_data->deposit_is_percentage ) ) {
							echo $sr_form_data->deposit_is_percentage == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->deposit_is_percentage ) ) {
							echo $sr_form_data->deposit_is_percentage == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_deposit_amount"
				                         title="<?php _e( 'The amount that will be applied to guest. It could be a flat number or percentage.', 'solidres' ); ?>"><?php _e( 'Deposit amount', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[deposit_amount]" maxlength="12"
				           value="<?php echo isset( $sr_form_data->deposit_amount ) ? $sr_form_data->deposit_amount : '' ?>"
				           id="srform_deposit_amount" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_deposit_by_stay_length"
				                         title="<?php _e( 'You can choose to charge deposit per stay length, for example if you enter 2, guest need to pay a deposit amount equals the first 2 nights/days cost. Default value is 0 means not applicable. If you enter a value greater than 0 for this field, all other deposit settings will be ignored.', 'solidres' ); ?>"><?php _e( 'Deposit by stay length', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[deposit_by_stay_length]" maxlength="12"
				           value="<?php echo isset( $sr_form_data->deposit_by_stay_length ) ? $sr_form_data->deposit_by_stay_length : '' ?>"
				           id="srform_deposit_by_stay_length" class="regular-text"></td>
			</tr>
			<tr>
				<?php
				$deposit_include_extra_cost = isset( $sr_form_data->deposit_include_extra_cost ) ? $sr_form_data->deposit_include_extra_cost : 1;
				?>
				<th scope="row"><label for="srform_deposit_include_extra_cost"
				                       title="<?php _e( 'Choose whether you want to include extra cost in deposit calculation. By default, deposit includes room cost only', 'solidres' ); ?>"><?php _e( 'Deposit includes extra cost', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[deposit_include_extra_cost]" class=""
					        id="srform_deposit_include_extra_cost">
						<option value="0" <?php echo $deposit_include_extra_cost == 0 ? 'selected' : ''?>><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php echo $deposit_include_extra_cost == 1 ? 'selected' : ''?>><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_id"
				                         title="<?php _e( 'Id desc', 'solidres' ); ?>"><?php _e( 'Id', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[id]" value="<?php echo isset( $id ) ? $id : ''; ?>"
				           id="srform_id" class="regular-text" disabled></td>
			</tr>
			<?php if ( ! current_user_can( 'solidres_partner' ) ) : ?>
			<tr>
				<th scope="row"><label for="srform_created_by"
				                         title="<?php _e( 'The user who created this', 'solidres' ); ?>"><?php _e( 'Created by', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[created_by]" class="" id="srform_created_by">
						<option value=""><?php _e( 'Selected user', 'solidres' ); ?></option>
						<?php echo isset( $sr_form_data->created_by ) ? SR_Helper::render_created_by( $sr_form_data->created_by ) : SR_Helper::render_created_by(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_created_date"
				                         title="<?php _e( 'Created Date', 'solidres' ); ?>"><?php _e( 'Created Date', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[created_date]"
				           value="<?php echo isset( $sr_form_data->created_date ) ? $sr_form_data->created_date : ''; ?>"
				           id="srform_created_date" class="regular-text srform_datepicker" disabled></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_modified_date"
				                         title="<?php _e( 'Modified Date', 'solidres' ); ?>"><?php _e( 'Modified Date', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[modified_date]"
				           value="<?php echo isset( $sr_form_data->modified_date ) ? $sr_form_data->modified_date : ''; ?>"
				           id="srform_modified_date" class="regular-text srform_datepicker" disabled></td>
			</tr>
			<?php endif; ?>
			<tr>
				<th scope="row"><label
						title="<?php _e( 'Select a page that contains your Terms and Condition', 'solidres' ); ?>"><?php _e( 'Terms of Use', 'solidres' ); ?></label>
				</th>
				<td>
					<?php echo wp_dropdown_pages( array(
						'name'             => 'srform[params][termsofuse]',
						'echo'             => false,
						'show_option_none' => __( 'Select a page', 'solidres' ),
						'selected'         => ! empty( $json_param['termsofuse'] ) ? $json_param['termsofuse'] : false
					) ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label
						title="<?php _e( 'Select a page that contains your Privacy Policy', 'solidres' ); ?>"><?php _e( 'Privacy Policy', 'solidres' ); ?></label>
				</th>
				<td>
					<?php echo wp_dropdown_pages( array(
						'name'             => 'srform[params][privacypolicy]',
						'echo'             => false,
						'show_option_none' => __( 'Select a page', 'solidres' ),
						'selected'         => ! empty( $json_param['privacypolicy'] ) ? $json_param['privacypolicy'] : false
					) ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label
						title="<?php _e( 'Select a page that contains your Disclaimer', 'solidres' ); ?>"><?php _e( 'Disclaimer', 'solidres' ); ?></label>
				</th>
				<td>
					<?php echo wp_dropdown_pages( array(
						'name'             => 'srform[params][disclaimer]',
						'echo'             => false,
						'show_option_none' => __( 'Select a page', 'solidres' ),
						'selected'         => ! empty( $json_param['disclaimer'] ) ? $json_param['disclaimer'] : false
					) ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_only_show_reservation_form"
				                         title="<?php _e( "If enabled, show only reservation form in the front end, other reservation asset's info (like name, address, description, gallery) will be hidden.", 'solidres' ); ?>"><?php _e( 'Show only reservation form', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[params][only_show_reservation_form]" class=""
					        id="srform_only_show_reservation_form">
						<option value="0" <?php if ( isset( $json_param['only_show_reservation_form'] ) ) {
							echo $json_param['only_show_reservation_form'] == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $json_param['only_show_reservation_form'] ) ) {
							echo $json_param['only_show_reservation_form'] == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_enable_coupon"
				                         title="<?php _e( 'Select whether to enable coupon for this asset.', 'solidres' ); ?>"><?php _e( 'Enable coupon', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[params][enable_coupon]" class="" id="srform_enable_coupon">
						<option value="0" <?php if ( isset( $json_param['enable_coupon'] ) ) {
							echo $json_param['enable_coupon'] == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $json_param['enable_coupon'] ) ) {
							echo $json_param['enable_coupon'] == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<?php if ( ! current_user_can( 'solidres_partner' ) ) : ?>
			<tr>
				<th scope="row"><label for="srform_image"
				                         title="<?php _e( 'Enter the logo file name into this field. This logo will be used in front end display and email templates. Before enter the logo file name here, it must be uploaded it first using Media Manager.', 'solidres' ); ?>"><?php _e( 'Logo', 'solidres' ); ?></label>
				</th>
				<td>
					<input type="text" name="srform[params][logo]" class="regular-text"
					       value="<?php echo isset( $json_param['logo'] ) ? $json_param['logo'] : ''; ?>"
					       id="srform_image" readonly="true">
					<input type="button" name="upload_srform_image" class="button upload_srform_image"
					       value="Upload"/>
				</td>
			</tr>
			<?php endif ?>

			<tr>
				<th scope="row">
					<label for="srform_show_facilities"
				           title="<?php _e( 'Choose whether to show asset facilities in front end or not', 'solidres' ); ?>">
						<?php _e( 'Show facilities', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset( $json_param['show_facilities'] )) :
						$json_param['show_facilities'] = 1;
					endif;
					?>
					<select name="srform[params][show_facilities]" class="" id="srform_show_facilities">
						<option value="0" <?php echo $json_param['show_facilities'] == 0 ? 'selected' : '' ?>>
							<?php _e( 'No', 'solidres' ); ?>
						</option>
						<option value="1" <?php echo $json_param['show_facilities'] == 1 ? 'selected' : '' ?>>
							<?php _e( 'Yes', 'solidres' ); ?>
						</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="srform_show_policies"
					       title="<?php _e( 'Choose whether to show asset policies in front end or not', 'solidres' ); ?>">
						<?php _e( 'Show policies', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset( $json_param['show_policies'] )) :
						$json_param['show_policies'] = 1;
					endif;
					?>
					<select name="srform[params][show_policies]" class="" id="srform_show_policies">
						<option value="0" <?php echo $json_param['show_policies'] == 0 ? 'selected' : '' ?>>
							<?php _e( 'No', 'solidres' ); ?>
						</option>
						<option value="1" <?php echo $json_param['show_policies'] == 1 ? 'selected' : '' ?>>
							<?php _e( 'Yes', 'solidres' ); ?>
						</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="srform_show_inline_checkavailability_form"
					       title="<?php _e( 'Choose whether to show the inline check availability form in your asset page in front end', 'solidres' ); ?>">
						<?php _e( 'Show inline check availability', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset( $json_param['show_inline_checkavailability_form'] )) :
						$json_param['show_inline_checkavailability_form'] = 0;
					endif;
					?>
					<select name="srform[params][show_inline_checkavailability_form]" class="" id="srform_show_inline_checkavailability_form">
						<option value="0" <?php echo $json_param['show_inline_checkavailability_form'] == 0 ? 'selected' : '' ?>>
							<?php _e( 'No', 'solidres' ); ?>
						</option>
						<option value="1" <?php echo $json_param['show_inline_checkavailability_form'] == 1 ? 'selected' : '' ?>>
							<?php _e( 'Yes', 'solidres' ); ?>
						</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="srform_enable_room_quantity_option"
					       title="<?php _e( 'Enable room quantity in front end to allow guest choosing room quantity, adult quantity and children quantity.', 'solidres' ); ?>">
						<?php _e( 'Enable room quantity', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset( $json_param['enable_room_quantity_option'] )) :
						$json_param['enable_room_quantity_option'] = 0;
					endif;
					?>
					<select name="srform[params][enable_room_quantity_option]" class="" id="srform_enable_room_quantity_option">
						<option value="0" <?php echo $json_param['enable_room_quantity_option'] == 0 ? 'selected' : '' ?>>
							<?php _e( 'No', 'solidres' ); ?>
						</option>
						<option value="1" <?php echo $json_param['enable_room_quantity_option'] == 1 ? 'selected' : '' ?>>
							<?php _e( 'Yes', 'solidres' ); ?>
						</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="srform_max_room_number"
				                         title="<?php _e( 'Enter the maximum number of rooms quantity that could be chosen in front end. Default is 10.', 'solidres' ); ?>"><?php _e( 'Max room number', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[params][max_room_number]" maxlength="12"
				           value="<?php echo isset( $json_param['max_room_number'] ) ? $json_param['max_room_number'] : '10' ?>"
				           id="srform_max_room_number" class="regular-text"></td>
			</tr>

			<tr>
				<th scope="row"><label for="srform_max_adult_number"
				                         title="<?php _e( 'Enter the maximum number of adult quantity that could be chosen in front end. Default is 10.', 'solidres' ); ?>"><?php _e( 'Max adult number', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[params][max_adult_number]" maxlength="12"
				           value="<?php echo isset( $json_param['max_adult_number'] ) ? $json_param['max_adult_number'] : '10' ?>"
				           id="srform_max_adult_number" class="regular-text"></td>
			</tr>

			<tr>
				<th scope="row"><label for="srform_max_child_number"
				                         title="<?php _e( 'Enter the maximum number of children quantity that could be chosen in front end. Default is 10.', 'solidres' ); ?>"><?php _e( 'Max child number', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[params][max_child_number]" maxlength="12"
				           value="<?php echo isset( $json_param['max_child_number'] ) ? $json_param['max_child_number'] : '10' ?>"
				           id="srform_max_child_number" class="regular-text"></td>
			</tr>

			<tr>
				<?php
				if (!isset( $json_param['show_unavailable_roomtype'] )) :
					$json_param['show_unavailable_roomtype'] = 1;
				endif;
				?>
				<th scope="row"><label for="srform_show_unavailable_roomtype"
				                         title="<?php _e( 'By default, when a guest check availability and a room type is unavailable, it will still be showed in front end with a message. If you want to hide unavailable room type completely, turn on this option.', 'solidres' ); ?>"><?php _e( 'Show unavailable room type', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[params][show_unavailable_roomtype]" class="" id="srform_show_unavailable_roomtype">
						<option value="0" <?php echo $json_param['show_unavailable_roomtype'] == 0 ? 'selected' : '' ?>>
							<?php _e( 'No', 'solidres' ); ?>
						</option>
						<option value="1" <?php echo $json_param['show_unavailable_roomtype'] == 1 ? 'selected' : '' ?>>
							<?php _e( 'Yes', 'solidres' ); ?>
						</option>
					</select>
				</td>
			</tr>

			<tr>
				<?php
				if (!isset( $json_param['additional_notification_emails'] )) :
					$json_param['additional_notification_emails'] = '';
				endif;
				?>
				<th scope="row"><label for="srform_additional_notification_emails"
				                         title="<?php _e( 'By default, Solidres sends notification to the main asset\'s email address, you can tell Solidres to send to more email addresses by entering those email addresses here, separated by commas.', 'solidres' ); ?>"><?php _e( 'Additional notification emails', 'solidres' ); ?></label>
				</th>
				<td>
					<input type="text" name="srform[params][additional_notification_emails]" maxlength="255"
					       value="<?php echo isset( $json_param['additional_notification_emails'] ) ? $json_param['additional_notification_emails'] : '10' ?>"
					       id="srform_additional_notification_emails"
							placeholder="Enter email addresses (commas separated)" class="regular-text">
				</td>
			</tr>

			</tbody>
		</table>
	</div>
</div>