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

function solidres_uninstall() {
	global $wpdb;
	$plugins = array(
		'solidres-advancedextra/solidres-advancedextra.php' => __( 'Solidres Advanced Extra', 'solidres' ),
		'solidres-assets/solidres-assets.php'               => __( 'Solidres Assets', 'solidres' ),
		'solidres-camera/solidres-camera.php'               => __( 'Solidres Camera', 'solidres' ),
		'solidres-coupons/solidres-coupons.php'             => __( 'Solidres Coupons', 'solidres' ),
		'solidres-discount/solidres-discount.php'           => __( 'Solidres Discount', 'solidres' ),
		'solidres-extras/solidres-extras.php'               => __( 'Solidres Extras', 'solidres' ),
		'solidres-hub/solidres-hub.php'                     => __( 'Solidres Hub', 'solidres' ),
		'solidres-invoice/solidres-invoice.php'             => __( 'Solidres Invoice', 'solidres' ),
		'solidres-limitbooking/solidres-limitbooking.php'   => __( 'Solidres Limitbooking', 'solidres' ),
		'solidres-map/solidres-map.php'                     => __( 'Solidres Limitbooking', 'solidres' ),
		'solidres-roomtypes/solidres-roomtypes.php'         => __( 'Solidres Roomtypes', 'solidres' ),
		'solidres-statistics/solidres-statistics.php'       => __( 'Solidres Statistics', 'solidres' ),
		'solidres-user/solidres-user.php'                   => __( 'Solidres User', 'solidres' ),
		'solidres-feedback/solidres-feedback.php'           => __( 'Solidres feedback', 'solidres' )
	);

	$active = 0;
	foreach ( $plugins as $plugin => $name ) {
		$file_path = WP_PLUGIN_DIR . '/' . $plugin;
		if ( file_exists( $file_path ) ) {
			$active ++;
		}
	}
	if ( $active > 0 ) {
		_e( '<p>Please uninstall all Solidres add-on plugins before uninstall Solidres Plugin</p>', 'solidres' );
		printf( __( '<p><a href="%s">Return to the plugins list</a></p>', 'solidres' ), admin_url( 'plugins.php' ) );
		wp_die();
	}

	$wpdb->query( "DROP TABLE IF EXISTS
	{$wpdb->prefix}sr_categories,
	{$wpdb->prefix}sr_reservation_extra_xref,
	{$wpdb->prefix}sr_reservation_room_details,
	{$wpdb->prefix}sr_config_data,
	{$wpdb->prefix}sr_reservation_notes,
	{$wpdb->prefix}sr_room_type_fields,
	{$wpdb->prefix}sr_reservation_room_extra_xref,
	{$wpdb->prefix}sr_room_type_extra_xref,
	{$wpdb->prefix}sr_room_type_coupon_xref,
	{$wpdb->prefix}sr_reservation_asset_fields,
	{$wpdb->prefix}sr_media_roomtype_xref,
	{$wpdb->prefix}sr_media_reservation_assets_xref,
	{$wpdb->prefix}sr_reservation_room_xref,
	{$wpdb->prefix}sr_rooms,
	{$wpdb->prefix}sr_extras,
	{$wpdb->prefix}sr_reservations,
	{$wpdb->prefix}sr_tariff_details,
	{$wpdb->prefix}sr_tariffs,
	{$wpdb->prefix}sr_coupons,
	{$wpdb->prefix}sr_room_types,
	{$wpdb->prefix}sr_reservation_assets,
	{$wpdb->prefix}sr_taxes,
	{$wpdb->prefix}sr_currencies,
	{$wpdb->prefix}sr_customer_groups,
	{$wpdb->prefix}sr_geo_states,
	{$wpdb->prefix}sr_countries,
	{$wpdb->prefix}sr_sessions"
	);

	delete_option( 'solidres_db_version' );
	delete_option( 'solidres_plugin' );
	delete_option( 'solidres_tariff' );
	delete_option( 'solidres_currency' );
	delete_option( 'solidres_invoice' );
	delete_option( 'solidres_pages' );
	delete_option( 'solidres_tools' );
	delete_option( 'solidres_hub' );

}
