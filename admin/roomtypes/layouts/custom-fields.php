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

if ( isset( $id ) ) {
	$custom_fields = new SR_Custom_Field( array( 'id' => (int) $id, 'type' => 'room_type' ) );
} else {
	$custom_fields = new SR_Custom_Field( array( 'id' => null, 'type' => 'room_type' ) );
}

$custom_field_view   = '';
$list_tab            = '';
$list_table_content  = '';
$custom_fields_array = $custom_fields->create_array_group();
if ( ! empty( $custom_fields_array ) ) {
	foreach ( $custom_fields_array as $group_key => $group_fields ) {
		$list_tab .= '<li class="' . $group_key . '">';
		$list_tab .= '<a href="#' . $group_key . '">' . solidres_convertslugtostring( $group_key ) . '</a>';
		$list_tab .= '<a href="#" id="del_custom_field_group" title="Delete custom field group"><img src="' . solidres()->plugin_url() . '/assets/images/close_btn.png" alt="Delete custom field group"/></a>';
		$list_tab .= '</li>';
		$list_table_content .= '<div id="' . $group_key . '" class="group_parent">';
		$list_table_content .= '<table class="form-table">';
		$list_table_content .= '<tbody>';
		$list_table_content .= '<tr class="add_new_field">';
		$list_table_content .= '<td class="first"><input type="text" name="ignfield[]"  size="20" value="" id="new_custom_field_key_' . $group_key . '" class="new_custom_field_key" placeholder="Enter field name"></td>';
		$list_table_content .= '<td><textarea class="srform_textarea new_custom_field_value i18n-multilingual" id="new_custom_field_value_' . $group_key . '" rows="5" name="ignfield[]"  placeholder="Enter field value"></textarea></td>';
		$list_table_content .= '<td><input type="button" id="add_new_field" class="button" value="Add New Field" /></td>';
		$list_table_content .= '</tr>';
		foreach ( $group_fields as $field ) {
			$list_table_content .= '<tr class="field_' . $custom_fields->split_field_name( $field[0] ) . '">';
			$list_table_content .= '<td class="first">' . ucfirst( $custom_fields->split_field_name( solidres_convertslugtostring( $field[0] ) ) ) . '</td>';
			$list_table_content .= '<td><textarea class="srform_textarea i18n-multilingual" rows="5" name="srform[customfields][' . $group_key . '][' . $custom_fields->split_field_name( $field[0] ) . ']" id="srform_customfields_' . $group_key . '_' . $custom_fields->split_field_name( $field[0] ) . '">' . $field[1] . '</textarea></td>';
			$list_table_content .= '<td><a href="#" id="del_custom_field_element" title="Delete field"><img src="' . solidres()->plugin_url() . '/assets/images/close_btn.png" alt="Delete field"/></a>';
			$list_table_content .= '</tr>';
		}
		$list_table_content .= '</tbody>';
		$list_table_content .= '</table>';
		$list_table_content .= '</div>';
	}
	$custom_field_view .= '<ul>';
	$custom_field_view .= $list_tab;
	$custom_field_view .= '</ul>';
	$custom_field_view .= $list_table_content;
} else {
	$custom_field_view .= '<ul>';
	$custom_field_view .= '</ul>';
} ?>



<div id="roomtype_custom_fields" class="postbox closed open">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Custom fields', 'solidres' ); ?></span></h3>

	<div class="inside">
		<div class="add_tabs_dynamic">
			<input type="button" id="add_new_group" class="button" value="<?php _e( 'Add New Group', 'solidres' ); ?>"/>
			<input type="text" id="group_name" value="" placeholder="<?php _e( 'Enter group name', 'solidres' ); ?>">
		</div>

		<div id="custom_fields_tab" class="<?php echo empty( $custom_fields_array ) ? 'nodisplay' : ''; ?>">
			<?php echo $custom_field_view; ?>
		</div>
	</div>
</div>