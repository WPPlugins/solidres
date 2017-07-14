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

class SR_Currencies_Table_Data extends Solidres_List_Table {
	public $total;

	function __construct() {
		global $status, $state, $wpdb, $string_search;
		$currencies    = new SR_Currency();
		$status        = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$currency_id   = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids           = (array) $currency_id;
		$action        = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;
		$state         = SR_Helper::get_listview_state( $status );

		$query_default = "SELECT * FROM {$wpdb->prefix}sr_currencies";

		if ( isset( $action ) && $action == 'edit' && isset( $currency_id ) && $currency_id != null ) {
			sr_edit_currency( $currency_id );
		}
		if ( $action == 'draft' || $action == 'publish' || $action == 'trash' || $action == 'untrash' ) {
			$currencies->update_states( $action, $currency_id, $ids );
		}
		if ( isset( $action ) && $action == 'delete' && isset( $currency_id ) && $currency_id != null ) {
			foreach ( $ids as $id ) {
				$currency_name = $wpdb->get_row( $wpdb->prepare( "SELECT currency_name FROM {$wpdb->prefix}sr_currencies WHERE id = %d", $id ) );
				$return        = $currencies->delete( $id );
				if ( $return === false ) {
					$message = __( 'Error, can not delete <span class="bold"> ' . $currency_name->currency_name . ' </span> because it is containing tarrifs or reservations. You must delete all its room tarrifs or reservations first.', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				} else {
					$message = __( '<span class="bold"> ' . $currency_name->currency_name . ' </span> permanently deleted.', 'solidres' );
					SR_Helper::show_message( $message );
				}
			}
		}
		if(isset($action) && $action == 'updated')
		{
			$message = __('Solidres currency exchange rate updated', 'solidres');
			SR_Helper::show_message( $message);
		}

		if ( $string_search != '' ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_filter[] = ' id = ' . (int) substr( $string_search, 3 );
			} else {
				$query_filter[] = ' currency_name LIKE "%' . $string_search . '%"';
			}
		} else if ( is_null( $state ) ) {
			$query_filter[] = ' state = 0 OR state = 1';
		} else {
			$query_filter[] = ' state = ' . $state;
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
			$name               = apply_filters( 'solidres_currency_name', $result->currency_name );
			$this->items[] = array(
				'currencyname' => '<strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="'. $name .'">' . $name . '</a>',
				'published'    => $published,
				'currencycode' => $result->currency_code,
				'exchangerate' => $result->exchange_rate,
				'id'           => $result->id,
			);
		}
		parent::__construct(
			array(
				'singular' => __( 'currency' ),
				'plural'   => __( 'currencies' ),
				'ajax'     => false,
			)
		);
	}

	function no_items() {
		_e( 'No currency found!', 'solidres' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'currencyname' => array( 'name', false ),
			'published'    => array( 'published', false ),
			'currencycode' => array( 'currencycode', false ),
			'exchangerate' => array( 'exchangerate', false ),
			'id'           => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'currencyname' => __( 'Currency name', 'solidres' ),
			'published'    => __( 'Published', 'solidres' ),
			'currencycode' => __( 'Code', 'solidres' ),
			'exchangerate' => __( 'Exchange rate', 'solidres' ),
			'id'           => __( 'ID', 'solidres' ),
		);

		return $columns;
	}

	function column_currencyname( $item ) {
		global $status;
		if ( $status == 'trash' ) {
			$actions = array(
				'untrash' => sprintf( __( '<a href="?page=%s&action=%s&id=%s">Restore</a>', 'solidres' ), $_REQUEST['page'], 'untrash', $item['id'] ),
				'delete'  => sprintf( __( '<a href="?page=%s&action=%s&id=%s">Delete Permanently</a>', 'solidres' ), $_REQUEST['page'], 'delete', $item['id'] ),
			);
		} else {
			$actions = array(
				'edit'  => sprintf( __( '<a href="?page=%s&action=%s&id=%s">Edit</a>', 'solidres' ), $_REQUEST['page'], 'edit', $item['id'] ),
				'trash' => sprintf( __( '<a href="?page=%s&action=%s&id=%s">Trash</a>', 'solidres' ), $_REQUEST['page'], 'trash', $item['id'] ),
			);
		}

		return sprintf( '%1$s %2$s', $item['currencyname'], $this->row_actions( $actions ) );
	}
}

function sr_currencies() {
	global $list_table_data, $status, $string_search;
	$helper          = new SR_Helper();
	$list_table_data = new SR_Currencies_Table_Data();
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array(
			'_wp_http_referer',
			'_wpnonce'
		), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$helper->listview( 'sr_currencies', $action, $string_search, $status, $list_table_data );
}