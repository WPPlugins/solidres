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

if ( isset( $sr_form_data->metadata ) && ! empty( $sr_form_data->metadata )  ) {
	if ( isset( $_POST['save_asset'] ) ) {
		$json_metadata = $sr_form_data->metadata;
	} else {
		$json_metadata = json_decode( $sr_form_data->metadata, true );
	}
} ?>

<div id="asset_metadata" class="postbox closed">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Metadata', 'solidres' ); ?></span></h3>

	<div class="inside">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><label for="srform_metadesc"
				                         title="<?php _e( 'An optional paragraph to be used as the description of the page in the HTML output. This will generally display in the results of search engines.', 'solidres' ); ?>"><?php _e( 'Meta Description', 'solidres' ); ?></label>
				</th>
				<td><textarea class="srform_textarea" rows="5" name="srform[metadesc]"
				              id="srform_metadesc"><?php echo isset( $sr_form_data->metadesc ) ? $sr_form_data->metadesc : '' ?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_metakey"
				                         title="<?php _e( 'An optional comma-separated list of keywords and/or phrases to be used in the HTML output.', 'solidres' ); ?>"><?php _e( 'Meta Keywords', 'solidres' ); ?></label>
				</th>
				<td><textarea class="srform_textarea" rows="5" name="srform[metakey]"
				              id="srform_metakey"><?php echo isset( $sr_form_data->metakey ) ? $sr_form_data->metakey : '' ?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_xreference"
				                         title="<?php _e( 'An optional field to allow this record to be cross-referenced to an external data system if required.', 'solidres' ); ?>"><?php _e( 'External Reference', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[xreference]" maxlength="50"
				           value="<?php echo isset( $sr_form_data->xreference ) ? $sr_form_data->xreference : '' ?>"
				           id="srform_xreference" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_robot"
				                         title="<?php _e( 'Robots Instructions', 'solidres' ); ?>"><?php _e( 'Robots', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[metadata][robots]" class="" id="srform_robot">
						<option value="" <?php if ( isset( $json_metadata['robots'] ) ) {
							echo $json_metadata['robots'] == '' ? 'selected' : '';
						} ?> ><?php _e( 'Use Global', 'solidres' ); ?></option>
						<option value="0" <?php if ( isset( $json_metadata['robots'] ) ) {
							echo $json_metadata['robots'] == 0 ? 'selected' : '';
						} ?> ><?php _e( 'Index, Follow', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $json_metadata['robots'] ) ) {
							echo $json_metadata['robots'] == 1 ? 'selected' : '';
						} ?> ><?php _e( 'No index, follow', 'solidres' ); ?></option>
						<option value="2" <?php if ( isset( $json_metadata['robots'] ) ) {
							echo $json_metadata['robots'] == 2 ? 'selected' : '';
						} ?> ><?php _e( 'Index, No follow', 'solidres' ); ?></option>
						<option value="3" <?php if ( isset( $json_metadata['robots'] ) ) {
							echo $json_metadata['robots'] == 3 ? 'selected' : '';
						} ?> ><?php _e( 'No index, no follow', 'solidres' ); ?></option>
						<option value="4" <?php if ( isset( $json_metadata['robots'] ) ) {
							echo $json_metadata['robots'] == 4 ? 'selected' : '';
						} ?> ><?php _e( 'JGLOBAL_NO_ROBOTS_TAG', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_metadata_author"
				                         title="<?php _e( 'The author of this content', 'solidres' ); ?>"><?php _e( 'Author', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[metadata][author]"
				           value="<?php echo isset( $json_metadata['author'] ) ? $json_metadata['author'] : ''; ?>"
				           id="srform_metadata_author" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_metadata_rights"
				                         title="<?php _e( 'Describe what rights others have to use this content.', 'solidres' ); ?>"><?php _e( 'Content Rights', 'solidres' ); ?></label>
				</th>
				<td><textarea class="srform_textarea" rows="5" name="srform[metadata][rights]"
				              id="srform_metadata_rights"><?php echo isset( $json_metadata['rights'] ) ? $json_metadata['rights'] : ''; ?></textarea>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>