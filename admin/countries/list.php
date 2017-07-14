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

class SR_Countries_Table_Data extends Solidres_List_Table {
	public $total;

	function __construct() {
		global $status, $state, $wpdb, $string_search;
		$countries     = new SR_Country();
		$status        = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$country_id    = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids           = (array) $country_id;
		$action        = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;
		$state         = SR_Helper::get_listview_state( $status );

		$query_default = "SELECT * FROM {$wpdb->prefix}sr_countries";
		if ( isset( $action ) && $action == 'edit' && isset( $country_id ) && $country_id != null ) {
			sr_edit_country( $country_id );
		}
		if ( $action == 'draft' || $action == 'publish' || $action == 'trash' || $action == 'untrash' ) {
			$countries->update_states( $action, $country_id, $ids );
		}

		if ( isset( $action ) && $action == 'delete' && isset( $country_id ) && $country_id != null ) {
			foreach ( $ids as $id ) {
				$country_name = $wpdb->get_row( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}sr_countries WHERE id = %d", $id ) );
				$return       = $countries->delete( $id );
				if ( $return === false ) {
					$message = __( 'Error, can not delete <span class="bold"> ' . $country_name->name . ' </span> because it is containing reservations or states. You must delete all its room reservations or states first.', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				} else {
					$message = __( '<span class="bold"> ' . $country_name->name . ' </span> permanently deleted.', 'solidres' );
					SR_Helper::show_message( $message );
				}
			}
		}

		if ( $string_search != '' ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_filter[] = ' id = ' . (int) substr( $string_search, 3 );
				//$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_countries WHERE id =  %d", (int) substr( $string_search, 3 ) ) );
			} else {
				$query_filter[] = ' name LIKE "%' . $string_search . '%"';
				//$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_countries WHERE name LIKE %s", '%' . $string_search . '%' ) );
			}
		} else if ( is_null( $state ) ) {
			$query_filter[] = ' state = 0 OR state = 1';
			//$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}sr_countries WHERE state = 0 OR state = 1" );
		} else {
			$query_filter[] = ' state = ' . $state;
			//$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sr_countries WHERE state = %d", $state ) );
		}

		if (count($query_filter) > 0) {
			$query_default .= " WHERE " . implode('AND', $query_filter);
		}

		$this->total = count($wpdb->get_results( $query_default ));

		$options = get_option( 'solidres_plugin' );
		$page_num = $this->get_pagenum();
		$num_per_page = isset( $options['list_limit'] ) ? $options['list_limit'] : 5;
		$start = ($page_num * $num_per_page) - $num_per_page;
		$query_default .= ' LIMIT ' . $start . ', ' . $num_per_page;

		$results = $wpdb->get_results( $query_default );

		foreach ( $results as $result ) {
			$published          = SR_Helper::view_status( $result->state );
			$this->items[] = array(
				'name' => '<strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="' . $result->name . '">' . $result->name . '</a>',
				'published' => $published,
				'id' => $result->id,
			);
		}
		parent::__construct( array(
			'singular' => __( 'country' ),
			'plural'   => __( 'countries' ),
			'ajax'     => false,
		) );
	}

	function no_items() {
		_e( 'No country found!', 'solidres' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'name'      => array( 'name', false ),
			'published' => array( 'published', false ),
			'id'        => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'name'      => __( 'Name', 'solidres' ),
			'published' => __( 'Published', 'solidres' ),
			'id'        => __( 'ID', 'solidres' ),
		);

		return $columns;
	}
}

function sr_countries() {
	global $list_table_data, $status, $string_search;
	$helper        = new SR_Helper();
	$list_table_data = new SR_Countries_Table_Data();
	if ( ! empty($_REQUEST['_wp_http_referer']) ) {
		wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$helper->listview( 'sr_countries', $action, $string_search, $status, $list_table_data );
}