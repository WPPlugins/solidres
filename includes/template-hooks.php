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

add_action( 'solidres_before_main_content', 'solidres_output_content_wrapper', 10 );
add_action( 'solidres_after_main_content', 'solidres_output_content_wrapper_end', 10 );
add_action( 'solidres_before_main_content', 'solidres_breadcrumb', 20, 0 );
add_action( 'solidres_sidebar', 'solidres_get_sidebar', 10 );