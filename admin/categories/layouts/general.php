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
		<th scope="row"><label for="srform_name"
		                         title="<?php _e( 'Enter your asset category name', 'solidres' ); ?>"><?php _e( 'Category name', 'solidres' ); ?>
				<span class="required">*</span></label></th>
		<td><input type="text" name="srform[name]"
		           value="<?php echo isset( $sr_form_data->name ) ? $sr_form_data->name : '' ?>"
		           id="srform_name" maxlength="200" class="regular-text"
		           placeholder="<?php _e( 'Enter asset category name', 'solidres' ); ?>" required></td>
	</tr>
	<tr>
		<th scope="row"><label for="srform_alias"
		                         title="<?php _e( 'Slug is used in Search Engine Friendly URL.', 'solidres' ); ?>"><?php _e( 'Slug', 'solidres' ); ?>
				<span class="required">*</span></label></th>
		<td><input type="text" name="srform[slug]" class="regular-text"
		           value="<?php echo isset( $sr_form_data->slug ) ? $sr_form_data->slug : '' ?>" id="srform_alias" maxlength="200"
		           placeholder="<?php _e( 'Enter asset category slug', 'solidres' ); ?>" required></td>
	</tr>
	<tr>
		<th scope="row"><label for="srform_state"
		                         title="<?php _e( 'The status of this item.', 'solidres' ); ?>"><?php _e( 'State', 'solidres' ); ?></label>
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
		<th scope="row"><label for="srform_parent"
		                         title="<?php _e( 'Select Parent category for this item', 'solidres' ); ?>"><?php _e( 'Parent Node', 'solidres' ); ?></label>
		</th>
		<td>
			<select name="srform[parent_id]" class=" srform_select_parent" id="srform_parent">
				<option value="0"><?php _e( 'Select parent node', 'solidres' ); ?></option>
				<?php echo isset( $sr_form_data->parent_id ) ? SR_Helper::render_list_parent_caterogy( $sr_form_data->parent_id ) : SR_Helper::render_list_parent_caterogy(); ?>
			</select>
		</td>
	</tr>
	</tbody>
</table>