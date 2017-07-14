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

include_once( ABSPATH . 'wp-includes/pluggable.php' );

if( current_user_can('administrator') ) {
add_action('admin_bar_menu', 'solidres_admin_toolbar', 100.201108);
	function solidres_admin_toolbar( $admin_bar ){
		$admin_bar->add_menu( array(
			'id'    => 'solidres_admin_toolbar',
			'title' => __( 'Solidres', 'solidres' ),
			'href'  => admin_url( 'admin.php?page=sr-add-new-asset' ),
			'meta'  => array(
				'title' => __('Solidres'),
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'add_new_asset_toolbar',
			'parent' => 'solidres_admin_toolbar',
			'title' => 'Add new Asset',
			'href'  => admin_url( 'admin.php?page=sr-add-new-asset' ),
			'meta'  => array(
				'title' => __('Add new Asset'),
				'target' => '_blank',
				'class' => 'solidres_sub_admin_toolbar'
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'add_new_roomtype_toolbar',
			'parent' => 'solidres_admin_toolbar',
			'title' => 'Add new Room Type',
			'href'  => admin_url( 'admin.php?page=sr-add-new-room-type' ),
			'meta'  => array(
				'title' => __('Add new Room Type'),
				'target' => '_blank',
				'class' => 'solidres_sub_admin_toolbar'
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'add_new_coupon_toolbar',
			'parent' => 'solidres_admin_toolbar',
			'title' => 'Add new Coupon ',
			'href'  => admin_url( 'admin.php?page=sr-add-new-coupon' ),
			'meta'  => array(
				'title' => __('Add new Coupon '),
				'target' => '_blank',
				'class' => 'solidres_sub_admin_toolbar'
			),
		));
		$admin_bar->add_menu( array(
			'id'    => 'add_new_extra_toolbar',
			'parent' => 'solidres_admin_toolbar',
			'title' => 'Add new Extra',
			'href'  => admin_url( 'admin.php?page=sr-add-new-extra' ),
			'meta'  => array(
				'title' => __('Add new Extra'),
				'target' => '_blank',
				'class' => 'solidres_sub_admin_toolbar'
			),
		));
	}
}