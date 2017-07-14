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

switch (SR_UI) {
	case 'bs2':
		define('SR_UI_GRID_CONTAINER', 'row-fluid');
		define('SR_UI_GRID_COL_1', 'span1');
		define('SR_UI_GRID_COL_2', 'span2');
		define('SR_UI_GRID_COL_3', 'span3');
		define('SR_UI_GRID_COL_4', 'span4');
		define('SR_UI_GRID_COL_5', 'span5');
		define('SR_UI_GRID_COL_6', 'span6');
		define('SR_UI_GRID_COL_7', 'span7');
		define('SR_UI_GRID_COL_8', 'span8');
		define('SR_UI_GRID_COL_9', 'span9');
		define('SR_UI_GRID_COL_10', 'span10');
		define('SR_UI_GRID_COL_12', 'span12');
		define('SR_UI_GRID_OFFSET_1', 'offset1');
		define('SR_UI_GRID_OFFSET_2', 'offset2');
		define('SR_UI_GRID_OFFSET_3', 'offset3');
		define('SR_UI_GRID_OFFSET_4', 'offset4');
		define('SR_UI_GRID_OFFSET_5', 'offset5');
		define('SR_UI_GRID_OFFSET_6', 'offset6');
		define('SR_UI_GRID_OFFSET_7', 'offset7');
		define('SR_UI_INPUT_APPEND', 'input-append');
		define('SR_UI_FORM_ROW', 'control-group');
		define('SR_UI_FORM_LABEL', 'control-label');
		define('SR_UI_FORM_FIELD', 'controls');
		break;
	case 'bs3':
		define('SR_UI_GRID_CONTAINER', 'row');
		define('SR_UI_GRID_COL_1', 'col-md-1');
		define('SR_UI_GRID_COL_2', 'col-md-2');
		define('SR_UI_GRID_COL_3', 'col-md-3');
		define('SR_UI_GRID_COL_4', 'col-md-4');
		define('SR_UI_GRID_COL_5', 'col-md-5');
		define('SR_UI_GRID_COL_6', 'col-md-6');
		define('SR_UI_GRID_COL_7', 'col-md-7');
		define('SR_UI_GRID_COL_8', 'col-md-8');
		define('SR_UI_GRID_COL_9', 'col-md-9');
		define('SR_UI_GRID_COL_10', 'col-md-10');
		define('SR_UI_GRID_COL_12', 'col-md-12');
		define('SR_UI_GRID_OFFSET_1', 'col-md-offset-1');
		define('SR_UI_GRID_OFFSET_2', 'col-md-offset-2');
		define('SR_UI_GRID_OFFSET_3', 'col-md-offset-3');
		define('SR_UI_GRID_OFFSET_4', 'col-md-offset-4');
		define('SR_UI_GRID_OFFSET_5', 'col-md-offset-5');
		define('SR_UI_GRID_OFFSET_6', 'col-md-offset-6');
		define('SR_UI_GRID_OFFSET_7', 'col-md-offset-7');
		define('SR_UI_INPUT_APPEND', 'input-group');
		define('SR_UI_FORM_ROW', 'form-group');
		define('SR_UI_FORM_LABEL', 'col-sm-2 control-label');
		define('SR_UI_FORM_FIELD', 'col-sm-10');
		break;
	case 'uk2':
		break;
}