<?php
/**
* Plugin Name: 	Solidres
* Plugin URI: 	http://www.solidres.com
* Description: 	Solidres - Hotel booking plugin for WordPress
* Author: 		Solidres Team
* Author URI: 	http://www.solidres.com
* Version: 		0.8.5
* Text Domain: 	solidres
* License   	GNU General Public License version 3, or later
* Copyright 	Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Solidres' ) ) :

	final class Solidres {

		public $version;

		public $nav = array();

		public $session = null;

		public $query = null;

		public $locale;

		public $is_multilingual = false;

		public $plugins = array();

		public static function get_instance( $config = array() )
		{
			static $instance = null;

			if ( null === $instance ) {
				$instance = new Solidres( $config );
				$instance->define_constants();
				$instance->setup_globals();
				$instance->includes();
				$instance->setup_actions();
				$instance->setup_media();
				if ( $instance->is_multilingual ) {
					$instance->load_filters();
				}
				do_action('sr_plugin_register');
			}

			return $instance;
		}

		private function __construct( $config = array() ) {
		}

		private function define_constants() {

			$relatives = $this->find_relatives();
			$upload_dir = wp_upload_dir();

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			foreach ( $relatives as $relative ) {
				$relative_key = strtoupper( substr( $relative, 9 ) );
				if(!defined('SR_PLUGIN_' . $relative_key . '_ENABLED')){
					if ( is_plugin_active( $relative . '/' . $relative . '.php' )) {
						define( 'SR_PLUGIN_' . $relative_key . '_ENABLED', true );
					} else {
						define( 'SR_PLUGIN_' . $relative_key . '_ENABLED', false );
					}
				}
			}

			define( 'SR_LOG_DIR', $upload_dir['basedir'] . '/sr-logs/' );
		}

		private function setup_globals() {
			$options = get_option( 'solidres_plugin' );
			$this->version = '0.8.5';
			$this->is_multilingual = false;
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( !empty( $options[ 'enable_multilingual_mode' ] )
			     && $options[ 'enable_multilingual_mode' ] == 1
				 && is_plugin_active( 'qtranslate-x/qtranslate.php' )
			) {
				$this->is_multilingual = true;
			}

			if ( !isset($options['ui_framework'])) {
				$options['ui_framework'] = 'bs2';
			}

			if ($options['ui_framework'] == 'bs3' && $this->is_request( 'frontend' ) ) {
				define('SR_UI', 'bs3');
			} else {
				define('SR_UI', 'bs2');
			}
		}

		private function includes() {

			include_once( 'includes/core-functions.php' );

			if ( $this->is_request( 'ajax' ) ) {
				include_once( 'libraries/ajax/ajax.php' );
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'includes/template-const.php' );
				include_once( 'includes/template-hooks.php' );
				include_once( 'includes/class-sr-breadcrumb.php' );
				include_once( 'includes/class-sr-shortcodes.php' );
				include_once( 'includes/shortcodes/class-sr-shortcode-reservation.php' );
			}

			$this->query = include( 'includes/class-sr-query.php' );
			$this->api   = include( 'includes/class-sr-api.php' );

			include_once( 'libraries/asset/asset.php' );
			include_once( 'libraries/calendar/calendar.php' );
			include_once( 'libraries/category/category.php' );
			include_once( 'libraries/config/config.php' );
			include_once( 'libraries/country/country.php' );
			include_once( 'libraries/coupon/coupon.php' );
			include_once( 'libraries/currency/currency.php' );
			include_once( 'libraries/customfield/customfield.php' );
			include_once( 'libraries/extra/base.php' );
			if ( defined( 'SR_PLUGIN_ADVANCEDEXTRA_ENABLED' ) && SR_PLUGIN_ADVANCEDEXTRA_ENABLED ) {
				include_once( ABSPATH . 'wp-content/plugins/solidres-advancedextra/libraries/extra/extra.php' );
			} else {
				include_once( 'libraries/extra/extra.php' );
			}

			if ( defined( 'SR_PLUGIN_DISCOUNT_ENABLED' ) && SR_PLUGIN_DISCOUNT_ENABLED ) {
				include_once( ABSPATH . 'wp-content/plugins/solidres-discount/libraries/discount/discount_process.php' );
			}

			include_once( 'libraries/media/media.php' );
			include_once( 'libraries/reservation/reservation.php' );
			include_once( 'libraries/roomtype/roomtype.php' );
			include_once( 'libraries/room/room.php' );
			include_once( 'libraries/state/state.php' );
			include_once( 'libraries/tariff/tariff.php' );
			include_once( 'libraries/tariff/tariffdetail.php' );
			include_once( 'libraries/tax/tax.php' );
			include_once( 'libraries/utilities/utilities.php' );

			include_once( 'includes/activate.php' );
			include_once( 'includes/install-data.php' );
			include_once( 'includes/install-sampledata.php' );
			include_once( 'includes/uninstall.php' );
			include_once( 'includes/class-sr-helper.php' );
			include_once( 'includes/widgets/class-sr-widget-check-availability.php' );
			include_once( 'includes/widgets/class-sr-widget-currency.php' );
			include_once( 'includes/class-sr-form-handler.php' );
			include_once( 'includes/class-sr-payment-gateways.php' );
			include_once( 'includes/gateways/paylater/class-sr-payment-gateway-paylater.php' );
			include_once( 'includes/gateways/bankwire/class-sr-payment-gateway-bankwire.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'includes/admin/class-sr-admin.php' );
			}
		}

		private function setup_actions() {
			add_action( 'init', array($this, 'include_assets') );
			add_action( 'init', array($this, 'init_session'), 0 );
			if ( $this->is_request( 'frontend' ) ) {
				add_action( 'init', array( 'Solidres_Shortcodes', 'init' ) );
			}
			add_action( 'plugins_loaded', array($this, 'solidres_load_language') );
			add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
			register_activation_hook( __FILE__, 'solidres_install' );
			register_activation_hook( __FILE__, 'solidres_install_data' );
			register_uninstall_hook( __FILE__, 'solidres_uninstall' );
		}

		public function init_session() {

			if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) ) {
				include_once( 'libraries/session/session.php' );
				$session_class  = apply_filters( 'solidres_session_handler', 'SR_Session_Handler' );
				$this->session = new $session_class;
				do_action( 'solidres_set_reservation_cookies', true );
			}
		}

		/**
		 * Todo: move this method into better locations to avoid too many files including
		 *
		 */
		public function include_assets() {
			global $wp_scripts;
			$options = get_option( 'solidres_plugin' );
			$this->locale = str_replace( '_', '-', get_locale() );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_register_script( 'solidres_datepicker_lc', solidres()->plugin_url() . '/assets/lib/datePicker/localization/jquery.ui.datepicker-' . $this->locale . '.js' );
			wp_enqueue_script( 'solidres_datepicker_lc' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_enqueue_script( 'jquery-ui-tooltip' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-ui-button' );

			if ( $this->is_request( 'admin' ) ) {
				wp_register_script( 'solidres_admin_script', solidres()->plugin_url() . '/assets/js/admin.js', array(
					'jquery',
					'jquery-ui-sortable'
				), $this->version, true );

				wp_register_style( 'solidres_skeleton', solidres()->plugin_url() . '/assets/css/skeleton.css' );

			}

			if ( $this->is_request( 'frontend' ) ) {
				wp_register_script( 'solidres_site_script', solidres()->plugin_url() . '/assets/js/site.js' , array('jquery'), $this->version, true );
			}

			wp_localize_script( 'solidres_admin_script', 'solidres', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajaxnonce' => wp_create_nonce( 'ajax_post_validation' ),
				'nonce_load_states' => wp_create_nonce( 'load-states' ),
				'nonce_load_taxes' => wp_create_nonce( 'load-taxes' ),
				'nonce_load_coupons' => wp_create_nonce( 'load-coupons' ),
				'nonce_load_extras' => wp_create_nonce( 'load-extras' ),
				'nonce_save_note' => wp_create_nonce( 'save-note' ),
				'nonce_delete_room' => wp_create_nonce( 'delete-room' ),
				'nonce_confirm_delete_room' => wp_create_nonce( 'confirm-delete-room' ),
				'plugin_url' => solidres()->plugin_url(),
				'is_multilingual' => $this->is_multilingual
			) );

			wp_localize_script( 'solidres_site_script', 'solidres', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajaxnonce' => wp_create_nonce( 'ajax_post_validation' ),
				'nonce_load_taxes' => wp_create_nonce( 'load-taxes' ),
				'nonce_load_calendar' => wp_create_nonce( 'load-calendar' ),
				'nonce_load_room_form' => wp_create_nonce( 'load-room-form' ),
				'nonce_load_date_form' => wp_create_nonce( 'load-date-form' ),
				'child_max_age_limit' => isset( $options['child_max_age_limit'] ) ? $options['child_max_age_limit'] : 17,
				'nonce_set_currency' => wp_create_nonce( 'set-currency' ),
				'nonce_apply_coupon' => wp_create_nonce( 'apply-coupon' ),
				'nonce_remove_coupon' => wp_create_nonce( 'remove-coupon' ),
				'is_multilingual' => $this->is_multilingual,
				'enable_auto_scroll' => isset( $options['enable_auto_scroll'] ) ? $options['enable_auto_scroll'] : 1
			) );

			wp_localize_script( 'solidres_site_script', 'solidres_text_site', array(
				'close_calendar' => __( 'Close calendar', 'solidres' ),
				'view_calendar' => __( 'View calendar', 'solidres' ),
				'hide_info' => __( 'Hide info', 'solidres' ),
				'more_info' => __( 'More info', 'solidres' ),
			) );

			if ( $this->is_request( 'admin' ) ) {
				wp_enqueue_script( 'solidres_admin_script' );
			}

			if ( $this->is_request( 'frontend' ) )  {
				wp_enqueue_script( 'solidres_site_script' );
			}

			wp_register_script( 'solidres_common_script', solidres()->plugin_url() . '/assets/js/common.js' , array('jquery'), $this->version, true );

			wp_localize_script( 'solidres_common_script', 'solidres_common', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajaxnonce' => wp_create_nonce( 'ajax_post_validation' ),
				'is_multilingual' => $this->is_multilingual,
				'child_max_age_limit' => isset( $options['child_max_age_limit'] ) ? $options['child_max_age_limit'] : 17,
				'nonce_cal_tariff' => wp_create_nonce( 'cal-tariff' ),
				'nonce_process_reservation' => wp_create_nonce( 'process-reservation' ),
				'nonce_check_user_exists' => wp_create_nonce( 'check-user-exists' ),
				'nonce_load_states' => wp_create_nonce( 'load-states' ),
				'context' => solidres()->is_request( 'frontend') ? 'frontend' : 'backend',
			) );

			wp_localize_script('solidres_common_script', 'solidres_text', array(
				'can_not_remove_coupon'        => __('Can not remove coupon', 'solidres'),
				'select_at_least_one_roomtype' => __('Please select at least one room type to proceed.', 'solidres'),
				'error_child_max_age'          => __('Ages must be between', 'solidres'),
				'and'                          => __('and', 'solidres'),
				'tariff_break_down'            => __('Tariff break down', 'solidres'),
				'sun'                          => __('Sun', 'solidres'),
				'mon'                          => __('Mon', 'solidres'),
				'tue'                          => __('Tue', 'solidres'),
				'wed'                          => __('Wed', 'solidres'),
				'thu'                          => __('Thu', 'solidres'),
				'fri'                          => __('Fri', 'solidres'),
				'sat'                          => __('Sat', 'solidres'),
				'next'                         => __('Next', 'solidres'),
				'back'                         => __('Back', 'solidres'),
				'processing'                   => __('Processing...', 'solidres'),
				'child'                        => __('Child', 'solidres'),
				'child_age_selection_js'       => __('years old', 'solidres'),
				'child_age_selection_1_js'     => __('year old', 'solidres'),
				'only_1_left'                  => __('Last chance! Only 1 room left', 'solidres'),
				'only_2_left'                  => __('Only 2 rooms left', 'solidres'),
				'only_3_left'                  => __('Only 3 rooms left', 'solidres'),
				'only_4_left'                  => __('Only 4 rooms left', 'solidres'),
				'only_5_left'                  => __('Only 5 rooms left', 'solidres'),
				'only_6_left'                  => __('Only 6 rooms left', 'solidres'),
				'only_7_left'                  => __('Only 7 rooms left', 'solidres'),
				'only_8_left'                  => __('Only 8 rooms left', 'solidres'),
				'only_9_left'                  => __('Only 9 rooms left', 'solidres'),
				'only_10_left'                 => __('Only 10 rooms left', 'solidres'),
				'only_11_left'                 => __('Only 11 rooms left', 'solidres'),
				'only_12_left'                 => __('Only 12 rooms left', 'solidres'),
				'only_13_left'                 => __('Only 13 rooms left', 'solidres'),
				'only_14_left'                 => __('Only 14 rooms left', 'solidres'),
				'only_15_left'                 => __('Only 15 rooms left', 'solidres'),
				'only_16_left'                 => __('Only 16 rooms left', 'solidres'),
				'only_17_left'                 => __('Only 17 rooms left', 'solidres'),
				'only_18_left'                 => __('Only 18 rooms left', 'solidres'),
				'only_19_left'                 => __('Only 19 rooms left', 'solidres'),
				'only_20_left'                 => __('Only 20 rooms left', 'solidres'),
				'show_more_info'               => __('More info', 'solidres'),
				'hide_more_info'               => __('Hide info', 'solidres'),
				'availability_calendar_close'  => __('Close calendar', 'solidres'),
				'availability_calendar_view'   => __('View calendar', 'solidres'),
				'username_exists'              => __('Username exists. Please choose another one.', 'solidres'),
				'show_tariffs'                 => __('Tariffs', 'solidres'),
				'hide_tariffs'                 => __('Tariffs', 'solidres'),
			));

			wp_enqueue_script( 'solidres_common_script' );

			wp_register_style( 'solidres_styles' , solidres()->plugin_url() . '/assets/css/style.css' );
			wp_enqueue_style( 'solidres_styles' );

			if ( $this->is_request( 'frontend' ) && ! solidres_is_login_page() ) {
				wp_register_style( 'solidres_site_styles', solidres()->plugin_url() . '/assets/css/site.css' );
				wp_enqueue_style( 'solidres_site_styles' );
			}

			wp_register_style( 'solidres_fontawesome' , solidres()->plugin_url() . '/assets/css/font-awesome.min.css' );
			wp_enqueue_style( 'solidres_fontawesome' );

			wp_register_script( 'solidres_validate', solidres()->plugin_url() . '/assets/lib/validate/jquery.validate.min.js' );
			wp_enqueue_script( 'solidres_validate' );

			wp_register_script( 'solidres_validate_additional', solidres()->plugin_url() . '/assets/lib/validate/additional-methods.min.js' );
			wp_enqueue_script( 'solidres_validate_additional' );

			$validate_allowed_language_tags = array('ar-AA', 'bg-BG', 'ca-ES', 'cs-CZ', 'da-DK', 'de-DE', 'el-GR', 'es-AR', 'es-ES', 'et-EE',
				'fa-IR', 'fi-FI', 'fr-FR', 'he-IL', 'hr-HR', 'hu-HU', 'it-IT', 'ja-JP', 'ko-KR', 'lv-LV', 'nb-NO', 'nl-NL',
				'pl-PL', 'pt-BR', 'ro-RO', 'ru-RU', 'sk-SK', 'sr-RS', 'sv-SE', 'th-TH', 'tr-TR', 'uk-UA', 'vi-VN', 'zh-CN', 'zh-TW'
			);

			if ( in_array( $this->locale, $validate_allowed_language_tags ) ) {
				wp_register_script( 'solidres_validate_lc', solidres()->plugin_url() . '/assets/lib/validate/localization/messages_' . $this->locale . '.js' );
				wp_enqueue_script( 'solidres_validate_lc' );
			}

			$google_map_api_key = isset( $options['google_map_api_key'] ) ? $options['google_map_api_key'] : '';
			wp_register_script( 'solidres_site_map', '//maps.googleapis.com/maps/api/js?libraries=places' . ( !empty( $google_map_api_key ) ? '&key=' . $google_map_api_key : '' ) );
			wp_enqueue_script( 'solidres_site_map' );

			wp_register_script( 'solidres_geocomplete', solidres()->plugin_url() . '/assets/lib/geocomplete/jquery.geocomplete.js'  );

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array() );
			wp_register_script( 'solidres_editable', solidres()->plugin_url() . '/assets/lib/editable/js/jqueryui-editable.min.js', array( 'jquery-ui-tooltip' ), false, true  );
			wp_register_style( 'solidres_editable', solidres()->plugin_url() . '/assets/lib/editable/css/jqueryui-editable.css' );
			wp_enqueue_script( 'custom-header' );


			if ( $this->is_request( 'frontend' ) && ! solidres_is_login_page() ) {
				wp_register_script( 'solidres_bootstrap', solidres()->plugin_url() . '/assets/lib/bootstrap/js/bootstrap.min.js' );
				wp_register_style( 'solidres_bootstrap', solidres()->plugin_url() . '/assets/lib/bootstrap/css/bootstrap.min.css' );
				wp_register_style( 'solidres_bootstrap_responsive', solidres()->plugin_url() . '/assets/lib/bootstrap/css/bootstrap-responsive.min.css' );
				if ( 'bs2' == SR_UI ) {
					wp_enqueue_script( 'solidres_bootstrap' );
					wp_enqueue_style( 'solidres_bootstrap' );
					wp_enqueue_style( 'solidres_bootstrap_responsive' );
				}

				wp_register_style( 'solidres_colorbox', solidres()->plugin_url() . '/assets/lib/colorbox/colorbox.css' );
				wp_register_script( 'solidres_colorbox', solidres()->plugin_url() . '/assets/lib/colorbox/jquery.colorbox.min.js', array('jquery') );
				wp_enqueue_style( 'solidres_colorbox' );
				wp_enqueue_script( 'solidres_colorbox' );
				$colorbox_allowed_language_tags = array(
					'ar-AA', 'bg-BG', 'ca-ES', 'cs-CZ', 'da-DK', 'de-DE', 'el-GR', 'es-ES', 'et-EE',
					'fa-IR', 'fi-FI', 'fr-FR', 'he-IL', 'hr-HR', 'hu-HU', 'it-IT', 'ja-JP', 'ko-KR',
					'lv-LV', 'nb-NO', 'nl-NL', 'pl-PL', 'pt-BR', 'ro-RO', 'ru-RU', 'sk-SK', 'sr-RS',
					'sv-SE', 'tr-TR', 'uk-UA', 'zh-CN', 'zh-TW',
				);

				if ( in_array( $this->locale, $colorbox_allowed_language_tags ) ) {
					wp_register_script( 'solidres_colorbox_lc', solidres()->plugin_url() . '/assets/lib/colorbox/i18n/jquery.colorbox-' . $this->locale . '.js' );
					wp_enqueue_script( 'solidres_colorbox_lc' );
				}
			}
		}

		public function solidres_load_language() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'solidres' );

			if ( $this->is_request( 'admin' ) ) {
				load_textdomain( 'solidres', WP_LANG_DIR . '/solidres/solidres-admin-' . $locale . '.mo' );
				load_textdomain( 'solidres', WP_LANG_DIR . '/plugins/solidres-admin-' . $locale . '.mo' );
			}

			load_textdomain( 'solidres', WP_LANG_DIR . '/solidres/solidres-' . $locale . '.mo' );
			load_plugin_textdomain( 'solidres', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/' );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'solidres_template_path', 'solidres/' );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get plugin url
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		public function payment_gateways() {
			return Solidres_Payment_Gateways::get_instance();
		}

		public function setup_media() {
			$options = get_option( 'solidres_plugin' );
			$small_thumbnail_width = ( ! empty( $options['sr_small_thumbnail_width'] ) ) ? $options['sr_small_thumbnail_width'] : 75;
			$small_thumbnail_height = ( ! empty( $options['sr_small_thumbnail_width'] ) ) ? $options['sr_small_thumbnail_height'] : 75;
			$medium_thumbnail_width = ( ! empty( $options['sr_medium_thumbnail_width'] ) ) ? $options['sr_medium_thumbnail_width'] : 300;
			$medium_thumbnail_height = ( ! empty( $options['sr_medium_thumbnail_height'] ) ) ? $options['sr_medium_thumbnail_height'] : 250;

			add_image_size( 'sr_small_thumbnail', $small_thumbnail_width, $small_thumbnail_height, true );
			add_image_size( 'sr_medium_thumbnail', $medium_thumbnail_width, $medium_thumbnail_height, true );
		}

		private function find_relatives() {
			$plugins = scandir( WP_PLUGIN_DIR );
			$relatives = array();

			foreach ( $plugins as $plugin ) {
				if ( $plugin === '.' OR $plugin === '..' OR substr( $plugin, 0, 8 ) !== 'solidres') continue;

				if ( is_dir( WP_PLUGIN_DIR . '/' . $plugin ) ) {
					$relatives[] = $plugin;
				}
			}

			return $relatives;
		}

		public function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		public function include_template_functions() {
			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'includes/template-functions.php' );
			}
		}

		public function api_request_url( $request, $ssl = null ) {
			if ( is_null( $ssl ) ) {
				$scheme = parse_url( home_url(), PHP_URL_SCHEME );
			} elseif ( $ssl ) {
				$scheme = 'https';
			} else {
				$scheme = 'http';
			}

			if ( strstr( get_option( 'permalink_structure' ), '/index.php/' ) ) {
				$api_request_url = trailingslashit( home_url( '/index.php/sr-api/' . $request, $scheme ) );
			} elseif ( get_option( 'permalink_structure' ) ) {
				$api_request_url = trailingslashit( home_url( '/sr-api/' . $request, $scheme ) );
			} else {
				$api_request_url = add_query_arg( 'sr-api', $request, trailingslashit( home_url( '', $scheme ) ) );
			}

			return esc_url_raw( $api_request_url );
		}

		public function load_filters() {
			$filters = array(
				'solidres_asset_name' => 20,
				'solidres_asset_desc' => 20,
				'solidres_asset_address1' => 20,
				'solidres_asset_address2' => 20,
				'solidres_roomtype_name' => 20,
				'solidres_roomtype_desc' => 20,
				'solidres_tariff_title' => 20,
				'solidres_tariff_desc' => 20,
				'solidres_asset_category' => 20,
				'solidres_coupon_name' => 20,
				'solidres_extra_name' => 20,
				'solidres_extra_desc' => 20,
				'solidres_currency_name' => 20,
				'solidres_category_name' => 20,
				'solidres_asset_customfield' => 20,
				'solidres_roomtype_customfield' => 20,
				'solidres_asset_socialnetworks' => 20
			);

			foreach ( $filters as $name => $priority ) {
				add_filter( $name, 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage', $priority );
			}
		}
	}

	function solidres() {
		return Solidres::get_instance();
	}

	/**
	 * Hook Solidres early onto the 'plugins_loaded' action..
	 *
	 * This gives all other plugins the chance to load before Solidres, to get
	 * their actions, filters, and overrides setup without Solidres being in the
	 * way.
	 */
	if ( defined( 'SOLIDRES_LATE_LOAD' ) ) {
		add_action( 'plugins_loaded', 'solidres', (int) SOLIDRES_LATE_LOAD );
	} else {
		$GLOBALS['solidres'] = solidres();
	}

endif;