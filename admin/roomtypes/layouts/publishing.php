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
if ( isset( $sr_form_data->params ) ) {
	if ( isset( $_POST['save_room_type'] ) ){
		$sr_form_data->params = json_encode( $sr_form_data->params );
	}
	$json_param = $sr_form_data->params;
}
?>

<div id="rootype_publishing" class="postbox closed open">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Publishing', 'solidres' ); ?></span></h3>

	<div class="inside">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><label for="srform_featured"
				                         title="<?php _e( 'Set a room type as Featured', 'solidres' ); ?>"><?php _e( 'Featured', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[featured]" class="" id="srform_featured">
						<option value="0" <?php if ( isset( $sr_form_data->featured ) ) {
							echo $sr_form_data->featured == 0 ? 'selected' : '';
						} ?> ><?php _e( 'Off', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->featured ) ) {
							echo $sr_form_data->featured == 1 ? 'selected' : '';
						} ?> ><?php _e( 'On', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<?php if ( ! current_user_can( 'solidres_partner' ) ) { ?>
			<tr>
				<th scope="row"><label for="srform_created_by"
				                         title="<?php _e( 'The user who created this', 'solidres' ); ?>"><?php _e( 'Created by', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[created_by]" class="" id="srform_created_by">
						<option value=""><?php _e( 'Selected creted by', 'solidres' ); ?></option>
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
			<?php } ?>
			<tr>
				<th scope="row"><label for="srform_ordering"
				                         title="<?php _e( 'Select the ordering', 'solidres' ); ?>"><?php _e( 'Ordering', 'solidres' ); ?></label>
				</th>
				<td><input type="number" name="srform[ordering]"
				           value="<?php echo isset( $sr_form_data->ordering ) ? $sr_form_data->ordering : '' ?>"
				           id="srform_ordering" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_id"
				                         title="<?php _e( 'Id desc', 'solidres' ); ?>"><?php _e( 'Id', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[id]" value="<?php echo isset( $id ) ? $id : ''; ?>"
				           id="srform_id" disabled class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srfor_show_smoking_option"
				                         title="<?php _e( 'Specify wheter to show smoking option in front end for this room type.', 'solidres' ); ?>"><?php _e( 'Show smoking option', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[params][show_smoking_option]" class=""
					        id="srfor_show_smoking_option">
						<option value="0" <?php if ( isset( $json_param['show_smoking_option'] ) ) {
							echo $json_param['show_smoking_option'] == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $json_param['show_smoking_option'] ) ) {
							echo $json_param['show_smoking_option'] == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_show_child_option"
				                         title="<?php _e( 'Specify whether to show child option in front end for this room type.', 'solidres' ); ?>"><?php _e( 'Show child option', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[params][show_child_option]" class=""
					        id="srform_show_child_option">
						<option value="0" <?php if ( isset( $json_param['show_child_option'] ) ) {
							echo $json_param['show_child_option'] == 0 ? 'selected' : '';
						} ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $json_param['show_child_option'] ) ) {
							echo $json_param['show_child_option'] == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_show_adult_option"
				                         title="<?php _e( 'Specify whether to show adult option in front end for this room type.', 'solidres' ); ?>"><?php _e( 'Show adult option', 'solidres' ); ?></label>
				</th>
				<td>
					<?php
					if ( !isset( $json_param['show_adult_option'] ) ) :
						$json_param['show_adult_option'] = 1;
					endif;
					?>
					<select name="srform[params][show_adult_option]" class=""
					        id="srform_show_adult_option">
						<option value="0" <?php echo $json_param['show_adult_option'] == 0 ? 'selected' : '' ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php echo $json_param['show_adult_option'] == 1 ? 'selected' : '' ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_show_guest_name_field"
				                         title="<?php _e( 'Specify whether to show guest name field in front end for this room type.', 'solidres' ); ?>"><?php _e( 'Show guest name field', 'solidres' ); ?></label>
				</th>
				<td>
					<?php
					if ( !isset( $json_param['show_guest_name_field'] ) ) :
						$json_param['show_guest_name_field'] = 1;
					endif;
					?>
					<select name="srform[params][show_guest_name_field]" class=""
					        id="srform_show_guest_name_field">
						<option value="0" <?php echo $json_param['show_guest_name_field'] == 0 ? 'selected' : '' ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php echo $json_param['show_guest_name_field'] == 1 ? 'selected' : '' ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_show_number_remaining_rooms"
				                         title="<?php _e( 'Choose whether to show the message that display the remaining rooms count in front end of this room type', 'solidres' ); ?>"><?php _e( 'Show remaining rooms count', 'solidres' ); ?></label>
				</th>
				<td>
					<?php
					if ( !isset( $json_param['show_number_remaining_rooms'] ) ) :
						$json_param['show_number_remaining_rooms'] = 1;
					endif;
					?>
					<select name="srform[params][show_number_remaining_rooms]" class=""
					        id="srform_show_number_remaining_rooms">
						<option value="0" <?php echo $json_param['show_number_remaining_rooms'] == 0 ? 'selected' : '' ?> ><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php echo $json_param['show_number_remaining_rooms'] == 1 ? 'selected' : '' ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="srform_enable_single_supplement"
				           title="<?php _e( 'Default is no, if you enable this option, guest will be charged a supplement when if they book this room type for 1 adult only.', 'solidres' ); ?>">
						<?php _e( 'Enable single supplement', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset($json_param['enable_single_supplement'])) :
						$json_param['enable_single_supplement'] = 0;
					endif;
					?>
					<select name="srform[params][enable_single_supplement]" class=""
					        id="srform_enable_single_supplement">
						<option value="0" <?php echo $json_param['enable_single_supplement'] == 0 ? 'selected' : ''?>><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php echo $json_param['enable_single_supplement'] == 1 ? 'selected' : ''?>><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="srform_single_supplement_value"
					       title="<?php _e( 'Enter the value of this single supplement, this must be a number', 'solidres' ); ?>">
						<?php _e( 'Single supplement value', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset($json_param['single_supplement_value'])) :
						$json_param['single_supplement_value'] = '';
					endif;
					?>
					<input type="text" name="srform[params][single_supplement_value]"
					       id="srform_single_supplement_value"
					       value="<?php echo $json_param['single_supplement_value'] ?>"
					       class="regular-text"
					/>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="srform_single_supplement_is_percent"
					       title="<?php _e( 'Choose Yes if the single supplement value above is a percentage value. Choose No if the value is a fixed number.', 'solidres' ); ?>">
						<?php _e( 'Single supplement is percentage', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset($json_param['single_supplement_is_percent'])) :
						$json_param['single_supplement_is_percent'] = '';
					endif;
					?>
					<select name="srform[params][single_supplement_is_percent]" class=""
					        id="srform_single_supplement_is_percent">
						<option value="0" <?php echo $json_param['single_supplement_is_percent'] == 0 ? 'selected' : ''?>><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php echo $json_param['single_supplement_is_percent'] == 1 ? 'selected' : ''?>><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="srform_is_exclusive"
					       title="<?php _e( 'Turn on this option if this room type has only one room and you don\'t want to show the quantity dropdown in front end. This option is good for apartment/villas booking when each room type is being used as a whole apartment/villa.', 'solidres' ); ?>">
						<?php _e( 'Is exclusive', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset($json_param['is_exclusive'])) :
						$json_param['is_exclusive'] = '';
					endif;
					?>
					<select name="srform[params][is_exclusive]" class=""
					        id="is_exclusive">
						<option value="0" <?php echo $json_param['is_exclusive'] == 0 ? 'selected' : ''?>><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php echo $json_param['is_exclusive'] == 1 ? 'selected' : ''?>><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="srform_skip_room_form"
					       title="<?php _e( 'Turn on this option you don\'t want to show the room form (where guest can select adult/child number, guest name, extras, smoking options) in front end', 'solidres' ); ?>">
						<?php _e( 'Skip room form', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<?php
					if (!isset($json_param['skip_room_form'])) :
						$json_param['skip_room_form'] = '';
					endif;
					?>
					<select name="srform[params][skip_room_form]" class=""
					        id="skip_room_form">
						<option value="0" <?php echo $json_param['skip_room_form'] == 0 ? 'selected' : ''?>><?php _e( 'No', 'solidres' ); ?></option>
						<option value="1" <?php echo $json_param['skip_room_form'] == 1 ? 'selected' : ''?>><?php _e( 'Yes', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>