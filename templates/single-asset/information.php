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


$information_data_view = '';
foreach ( $custom_fields as $key => $fields ) {
	if ( $key == 'socialnetworks' ) continue;
	if ( $key == 'facilities' && isset($asset_params['show_facilities']) && $asset_params['show_facilities'] == 0) continue;
	if ( $key == 'policies' && isset($asset_params['show_policies']) && $asset_params['show_policies'] == 0) continue;
	$information_data_view .= '<h3>'. __( solidres_convertslugtostring( $key ), 'solidres' ) .'</h3>';
	foreach ( $fields as $field ) {
		$information_data_view .= '<div class="' . SR_UI_GRID_CONTAINER . ' custom-field-row">';
		$information_data_view .= '<div class="' . SR_UI_GRID_COL_2 . ' info-heading">'. __( ucfirst( $asset_custom_fields->split_field_name( solidres_convertslugtostring( $field[0] ) ) ), 'solidres' ) . '</div>';
		$information_data_view .= '<div class="' . SR_UI_GRID_COL_10 . '">'.apply_filters( 'solidres_asset_customfield', $field[1] ) .'</div>';
		$information_data_view .= '</div>';
	}
}
echo $information_data_view;
