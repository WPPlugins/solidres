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

add_action( 'init', 'solidres_do_output_buffer' );
function solidres_do_output_buffer() {
	ob_start();
}

/**
 * Solidres Update version
 */
add_action( 'admin_init', 'solidres_update_version', 8 );
function solidres_update_version() {
	$solidres_installed_version = get_option( 'solidres_db_version' );
	if ( isset( $solidres_installed_version ) ) {
		$updates = array(
			'0.2.0' => 'updates/solidres-update-0.2.0.php',
			'0.2.1' => 'updates/solidres-update-0.2.1.php',
			'0.3.0' => 'updates/solidres-update-0.3.0.php',
			'0.4.0' => 'updates/solidres-update-0.4.0.php',
			'0.5.0' => 'updates/solidres-update-0.5.0.php',
			'0.6.0' => 'updates/solidres-update-0.6.0.php',
			'0.7.0' => 'updates/solidres-update-0.7.0.php',
			'0.8.0' => 'updates/solidres-update-0.8.0.php',
			'0.8.1' => 'updates/solidres-update-0.8.1.php',
			'0.8.2' => 'updates/solidres-update-0.8.2.php',
			'0.8.3' => 'updates/solidres-update-0.8.3.php',
			'0.8.4' => 'updates/solidres-update-0.8.4.php',
			'0.8.5' => 'updates/solidres-update-0.8.5.php',
		);
		foreach ( $updates as $version => $updater ) {
			if ( version_compare( $solidres_installed_version, $version, '<' ) ) {
				include( $updater );
				delete_option( 'solidres_db_version' );
				add_option( 'solidres_db_version', $version );
			}
		}
	}
}

/**
 *Check current page as login or register
 * @return bool
 */
function solidres_is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

/**
 * Get plugin version
 *
 * @param $plugin_file
 * @return mixed
 */
function solidres_check_version( $plugin_file ) {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	$plugin_data = get_plugins();
	return $plugin_data[$plugin_file]['Version'];
}

/**
 * Convert slug to String
 *
 * @param $string
 * @return string
 */
function solidres_convertslugtostring( $string ) {
	$string = str_replace( '_', ' ', $string );
	return ucfirst( $string );
}

/**
 * Set html type for email content
 *
 * @return string
 */
function solidres_set_html_content_type() {
	return 'text/html';
}

/**
 * Get template part
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function solidres_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/solidres/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", solidres()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( solidres()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = solidres()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/solidres/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", solidres()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugin filter template file from their plugin
	$template = apply_filters( 'solidres_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function solidres_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = solidres_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'solidres_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'solidres_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'solidres_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function solidres_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = solidres()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = solidres()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters('solidres_locate_template', $template, $template_name, $template_path);
}

function solidres_template_debug_mode() {
	if ( ! defined( 'SOLIDRES_TEMPLATE_DEBUG_MODE' ) ) {
		$tool_options = get_option( 'solidres_tools', array() );
		if ( ! empty( $tool_options['enable_template_debug'] ) && current_user_can( 'manage_options' ) ) {
			define( 'SOLIDRES_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'SOLIDRES_TEMPLATE_DEBUG_MODE', false );
		}
	}
}
add_action( 'after_setup_theme', 'solidres_template_debug_mode', 20 );

/**
 * Add Additional Links To The WordPress Plugin Admin
 */
if ( ! function_exists ( 'solidres_register_plugin_links' ) ) {
	function solidres_register_plugin_links( $links, $file ) {
		$base = plugin_basename(__FILE__);
		if ( $file == $base ) {
			$links[] = '<a href="admin.php?page=sr-option">' . __( 'Settings','solidres' ) . '</a>';
			$links[] = '<a href="http://www.solidres.com/support/frequently-asked-questions" target="_blank">' . __( 'FAQ','solidres' ) . '</a>';
			$links[] = '<a href="http://www.solidres.com" target="_blank">' . __( 'Support','solidres' ) . '</a>';
		}
		return $links;
	}
}
add_filter( 'plugin_row_meta', 'solidres_register_plugin_links', 10, 2 );

function solidres_reservation_cleanup() {
	solidres()->session->destroy_session();
}
add_action( 'solidres_reservation_cleanup', 'solidres_reservation_cleanup');

function solidres_get_log_file_path( $handle ) {
	return trailingslashit( SR_LOG_DIR ) . $handle . '-' . sanitize_file_name( wp_hash( $handle ) ) . '.log';
}

function solidres_get_page_id( $page ) {
	$options = get_option( 'solidres_pages' );
	$page = apply_filters( 'solidres_get_' . $page . '_page_id', $options[$page] );

	return $page ? absint( $page ) : -1;
}

function solidres_get_page_permalink( $page ) {
	$page_id   = solidres_get_page_id( $page );
	$permalink = $page_id ? get_permalink( $page_id ) : '';
	return apply_filters( 'solidres_get_' . $page . '_page_permalink', $permalink );
}

/**
 * Get endpoint URL
 *
 * Gets the URL for an endpoint, which varies depending on permalink settings.
 *
 * @return string
 */
function solidres_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
	if ( ! $permalink )
		$permalink = get_permalink();

	// Map endpoint to options
	$endpoint = isset( solidres()->query->query_vars[ $endpoint ] ) ? solidres()->query->query_vars[ $endpoint ] : $endpoint;

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $permalink, '?' ) ) {
			$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
			$permalink    = current( explode( '?', $permalink ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $permalink );
	}

	return apply_filters( 'solidres_get_endpoint_url', $url, $endpoint, $value, $permalink );
}

function solidres_get_reservation_completed_url( $reservation ) {

	$reservation_completed_url = solidres_get_endpoint_url( 'reservation-id', $reservation->id, solidres_get_page_permalink( 'reservationcompleted' ) );

	if ( 'yes' == get_option( 'solidres_force_ssl_checkout' ) || is_ssl() ) {
		$reservation_completed_url = str_replace( 'http:', 'https:', $reservation_completed_url );
	}

	$reservation_completed_url = add_query_arg( 'code', $reservation->code, $reservation_completed_url );

	return apply_filters( 'solidres_get_reservation_completed_url', $reservation_completed_url, $reservation );
}

function solidres_get_view_reservation_url( $id ) {

	$view_reservation_url = solidres_get_endpoint_url( 'view-reservation', $id, solidres_get_page_permalink( 'customerdashboard' ) );

	return apply_filters( 'solidres_get_view_reservation_url', $view_reservation_url );
}

function solidres_get_cancel_reservation_url( $id ) {

	$cancel_reservation_url = solidres_get_endpoint_url( 'cancel-reservation', $id, solidres_get_page_permalink( 'customerdashboard' ) );

	return apply_filters( 'solidres_get_cancel_reservation_url', $cancel_reservation_url );
}

function solidres_get_edit_account_url() {

	$edit_account_url = solidres_get_endpoint_url( 'edit-account', '', solidres_get_page_permalink( 'customerdashboard' ) );

	return apply_filters( 'solidres_get_eidt_account_url', $edit_account_url );
}

function solidres_notice_count( $notice_type = '' ) {

	$notice_count = 0;
	$all_notices  = solidres()->session->get( 'sr_notices', array() );

	if ( isset( $all_notices[$notice_type] ) ) {

		$notice_count = absint( sizeof( $all_notices[$notice_type] ) );

	} elseif ( empty( $notice_type ) ) {

		foreach ( $all_notices as $notices ) {
			$notice_count += absint( sizeof( $all_notices ) );
		}

	}

	return $notice_count;
}

function solidres_add_notice( $message, $notice_type = 'success' ) {

	$notices = solidres()->session->get( 'sr_notices', array() );

	// Backward compatibility
	if ( 'success' === $notice_type ) {
		$message = apply_filters( 'solidres_add_message', $message );
	}

	$notices[$notice_type][] = apply_filters( 'solidres_add_' . $notice_type, $message );

	solidres()->session->set( 'sr_notices', $notices );
}

/**
 * Unset all notices.
 *
 * @since 2.1
 */
function solidres_clear_notices() {
	solidres()->session->set( 'sr_notices', null );
}

/**
 * Prints messages and errors which are stored in the session, then clears them.
 *
 * @since 2.1
 */
function solidres_print_notices() {

	$all_notices  = solidres()->session->get( 'sr_notices', array() );
	$notice_types = apply_filters( 'solidres_notice_types', array( 'error', 'success', 'notice' ) );

	foreach ( $notice_types as $notice_type ) {
		if ( solidres_notice_count( $notice_type ) > 0 ) {
			solidres_get_template( "notices/{$notice_type}.php", array(
				'messages' => $all_notices[$notice_type]
			) );
		}
	}

	solidres_clear_notices();
}

/**
 * Set a cookie - wrapper for setcookie using WP constants.
 *
 * @param  string  $name   Name of the cookie being set.
 * @param  string  $value  Value of the cookie.
 * @param  integer $expire Expiry of the cookie.
 * @param  boolean $secure Whether the cookie should be served only over https.
 */
function solidres_setcookie( $name, $value, $expire = 0, $secure = false ) {
	if ( ! headers_sent() ) {
		setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		headers_sent( $file, $line );
		trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
	}
}

function solidres_request_updates() {

	$solidres_plugins = array_merge( solidres_get_plugins(), solidres_get_widgets() );
	$extensions       = array( 'solidres' => solidres_check_version( 'solidres/solidres.php' ) );
	$url              = 'https://www.solidres.com/checkupdates';
	foreach ( $solidres_plugins as $solidres_plugin ) :
		$plugin_path_absolute = WP_PLUGIN_DIR . '/solidres-' . $solidres_plugin . '/solidres-' . $solidres_plugin . '.php';
		$is_installed         = false;
		if ( file_exists( $plugin_path_absolute ) ) :
			$is_installed = true;
		endif;

		if ( $is_installed ) :
			$extensions[ 'solidres-' . $solidres_plugin ] = solidres_check_version( 'solidres-' . $solidres_plugin . '/' . 'solidres-' . $solidres_plugin . '.php' );
		endif;
	endforeach;

	$data = array(
		'data' => array(
			'extensions' => $extensions
		)
	);

	$response = wp_remote_post( $url, array( 'body' => $data ) );

	if ($response instanceof WP_Error) {
		return false;
	}

	$updates = json_decode( trim( $response['body'] ), true );

	if ( is_array( $updates ) && ! empty( $updates ) && json_last_error() == JSON_ERROR_NONE && ! isset( $updates['data'] ) ) :
		$cache_path = solidres()->plugin_path() . '/admin/systems/cache';

		if ( ! is_dir( $cache_path ) ) :
			if ( ! mkdir( $cache_path, 0755 ) ) :
				echo 'Solidres update cache folder failed to be created';
			endif;
		endif;

		if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
			$update_content = json_encode( $updates, JSON_PRETTY_PRINT );
		} else {
			$update_content = json_encode( $updates );
		}

		if ( ! file_put_contents( $cache_path . '/updates.json', $update_content ) ) :
			echo 'Solidres update cache file failed to be created';
		endif;
	endif;
}

function solidres_get_updates() {
	$file = WP_PLUGIN_DIR . '/solidres/admin/systems/cache/updates.json';

	if ( file_exists( $file )
	     && ( $raw = file_get_contents( $file ) )
	     && ( $updates = json_decode( $raw, true ) )
	     && json_last_error() == JSON_ERROR_NONE
	) {
		return $updates;
	}

	return array();
}

function solidres_get_plugins() {

	return apply_filters( 'solidres_plugins', array(
		'complextariff',
		'invoice',
		'limitbooking',
		'simple_gallery',
		'statistics',
		'discount',
		'advancedextra',
		'paypal',
		'offline',
		'user',
		'sms',
		'googleanalytics',
		'currency'
	) );

}

function solidres_get_widgets() {

	return apply_filters( 'solidres_widgets', array(
		'camera',
		'checkavailability',
		'currency',
		'roomtypes',
		'coupons',
		'extras',
		'map',
	) );

}