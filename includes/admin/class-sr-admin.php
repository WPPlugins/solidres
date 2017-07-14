<?php
/**
 * Solidres Admin.
 *
 * @class       SR_Admin
 * @author      Solidres
 * @category    Admin
 * @package     Solidres/Admin
 * @version     0.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Solidres_Admin {
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'init', array( $this, 'check_updates' ) );
		add_filter( 'qtranslate_compatibility', array( $this, 'qtrans_compat' ) );
		add_action( 'qtranslate_init_language', array( $this, 'init_multilingual_mode' ) );
	}

	public function includes() {
		include_once( solidres()->plugin_path() . '/includes/class-sr-list-table.php' );
		include_once( solidres()->plugin_path() . '/includes/top-menu.php' );
		include_once( solidres()->plugin_path() . '/admin/includes/assets.php' );
		include_once( solidres()->plugin_path() . '/admin/includes/customers.php' );
		include_once( solidres()->plugin_path() . '/admin/includes/reservations.php' );
		include_once( solidres()->plugin_path() . '/admin/includes/coupons_extras.php' );
		include_once( solidres()->plugin_path() . '/admin/includes/options.php' );
		include_once( solidres()->plugin_path() . '/includes/admin/class-sr-admin-menus.php' );
	}

	public function init_multilingual_mode($url_info) {
		if( !$url_info['doing_front_end'] && solidres()->is_multilingual ) {
			add_filter('qtranslate_load_admin_page_config', array( $this, 'add_admin_page_config' ) );
		}
	}

	public function qtrans_compat($compatibility) {
		return true;
	}

	public function add_admin_page_config($page_configs) {
		$page_config = array();

		// Asset
		$page_config['pages'] = array( 'admin.php' => 'page=sr-assets|page=sr-assets&action=edit|page=sr-add-new-asset' );
		$page_config['anchors'] = array(
			'asset_general_infomation' => array( 'where' => 'before' ),
			'asset_custom_fields' => array( 'where' => 'before' )
		);
		$page_config['forms'] = array();

		$f = array();
		$f['form'] = array( 'id' => 'srform' );
		$f['fields'] = array();
		$fields = &$f['fields']; // shortcut
		$fields[] = array( 'id' => 'srform_name' );
		$fields[] = array( 'id' => 'srform_description' );
		$fields[] = array( 'id' => 'new_custom_field_key' );
		$fields[] = array( 'id' => 'testfieldinput' );

		$page_config['forms'][] = $f;
		$page_configs[] = $page_config;

		// Room type
		$page_config = array();
		$page_config['pages'] = array( 'admin.php' => 'page=sr-room-types|page=sr-room-types&action=edit' );
		$page_config['anchors'] = array(
			'roomtype_general_infomation' => array( 'where' => 'before' ),
			'roomtype_custom_fields' => array( 'where' => 'before' )
		);
		$page_config['forms'] = array();

		$f = array();
		$f['form'] = array( 'id' => 'srform' );
		$f['fields'] = array();
		$fields = &$f['fields']; // shortcut
		$fields[] = array( 'id' => 'srform_name' );
		$fields[] = array( 'id' => 'srform_standard_tariff_title' );
		$fields[] = array( 'id' => 'srform_standard_tariff_description' );
		$fields[] = array( 'id' => 'srform_description' );

		$page_config['forms'][] = $f;
		$page_configs[] = $page_config;

		// Category
		$page_config = array();
		$page_config['pages'] = array( 'admin.php' => 'page=sr-categories|page=sr-categories&action=edit' );
		$page_config['anchors'] = array( 'post-body' => array( 'where' => 'before' ) );
		$page_config['forms'] = array();

		$f = array();
		$f['form'] = array( 'id' => 'srform' );
		$f['fields'] = array();
		$fields = &$f['fields']; // shortcut
		$fields[] = array( 'id' => 'srform_name' );

		$page_config['forms'][] = $f;
		$page_configs[] = $page_config;

		// Coupon
		$page_config = array();
		$page_config['pages'] = array( 'admin.php' => 'page=sr-coupons|page=sr-coupons&action=edit' );
		$page_config['anchors'] = array( 'post-body' => array( 'where' => 'before' ) );
		$page_config['forms'] = array();

		$f = array();
		$f['form'] = array( 'id' => 'srform' );
		$f['fields'] = array();
		$fields = &$f['fields']; // shortcut
		$fields[] = array( 'id' => 'srform_coupon_name' );

		$page_config['forms'][] = $f;
		$page_configs[] = $page_config;

		// Extra
		$page_config = array();
		$page_config['pages'] = array( 'admin.php' => 'page=sr-extras|page=sr-extras&action=edit' );
		$page_config['anchors'] = array( 'post-body' => array( 'where' => 'before' ) );
		$page_config['forms'] = array();

		$f = array();
		$f['form'] = array( 'id' => 'srform' );
		$f['fields'] = array();
		$fields = &$f['fields']; // shortcut
		$fields[] = array( 'id' => 'srform_name' );
		$fields[] = array( 'id' => 'srform_description' );

		$page_config['forms'][] = $f;
		$page_configs[] = $page_config;

		// Currency
		$page_config = array();
		$page_config['pages'] = array( 'admin.php' => 'page=sr-currencies|page=sr-currencies&action=edit' );
		$page_config['anchors'] = array( 'post-body' => array( 'where' => 'before' ) );
		$page_config['forms'] = array();

		$f = array();
		$f['form'] = array( 'id' => 'srform' );
		$f['fields'] = array();
		$fields = &$f['fields']; // shortcut
		$fields[] = array( 'id' => 'srform_currency_name' );

		$page_config['forms'][] = $f;
		$page_configs[] = $page_config;

		// Tariff
		$page_config = array();
		$page_config['pages'] = array( 'admin.php' => 'page=sr-complextariff' );
		$page_config['anchors'] = array( 'sr-tariff-form' => array( 'where' => 'before' ) );
		$page_config['forms'] = array();

		$f = array();
		$f['form'] = array( 'id' => 'sr-tariff-form' );
		$f['fields'] = array();
		$fields = &$f['fields']; // shortcut
		$fields[] = array( 'id' => 'sr-tariff-title' );

		$page_config['forms'][] = $f;
		$page_configs[] = $page_config;

		return $page_configs;
	}

	public function check_updates() {
		$solidres_config_data = new SR_Config( array( 'scope_id' => 0, 'data_namespace' => 'system' ) );
		$last_update_check    = $solidres_config_data->get( 'system/last_update_check', '' );
		$need_update_checking = false;
		$update_source_file   = solidres()->plugin_path() . '/admin/systems/cache/updates.json';

		if ( empty( $last_update_check ) || ! file_exists( $update_source_file ) ) {
			$need_update_checking = true;
		} else {
			$now      = time();
			$next_run = strtotime( '+1 day', $last_update_check );

			if ( $now > $next_run ) {
				$need_update_checking = true;
			}
		}

		if ( $need_update_checking ) {
			solidres_request_updates();
			$solidres_config_data->set( array( 'last_update_check' => time() ) );
		}
	}
}

return new Solidres_Admin();