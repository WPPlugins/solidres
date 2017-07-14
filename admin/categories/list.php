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

class SR_Categories_Table_Data extends Solidres_List_Table {

	public $total;

	function __construct() {
		global $wpdb, $status, $state, $string_search, $query_default;
		$categories    = new SR_Category();
		$status        = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$category_id   = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids           = (array) $category_id;
		$action        = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;
		$query_default = "SELECT * FROM {$wpdb->prefix}sr_categories";
		$state         = SR_Helper::get_listview_state( $status );

		if ( isset( $action ) && $action == 'edit' && isset( $category_id ) && $category_id != null ) {
			sr_edit_category( $category_id );
		}
		if ( $action == 'draft' || $action == 'publish' || $action == 'trash' || $action == 'untrash' ) {
			$categories->update_states( $action, $category_id, $ids );
		}
		if ( isset( $action ) && $action == 'delete' && isset( $category_id ) && $category_id != null ) {
			foreach ( $ids as $id ) {
				$categories->delete( $id );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 asset category permanently deleted.', 'solidres' );
				SR_Helper::show_message( $message, 'error' );
			} else {
				$message = __( count( $ids ) . ' asset categories permanently deleted.', 'solidres' );
				SR_Helper::show_message( $message );
			}
		}
		if ( $string_search != '' ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_default = $query_default . ' WHERE id = ' . (int) substr( $string_search, 3 );
			} else {
				$query_default = $query_default . ' WHERE name LIKE "%%' . $string_search . '%%"';
			}
		} else if ( is_null( $state ) ) {
			$query_default = $query_default . " WHERE state = 0 OR state = 1";
		} else {
			$query_default = $query_default . " WHERE state = " . $state;
		}

		$this->total = count($wpdb->get_results( $query_default ));

		$options = get_option( 'solidres_plugin' );
		$page_num = $this->get_pagenum();
		$num_per_page = isset( $options['list_limit'] ) ? $options['list_limit'] : 5;
		$start = ($page_num * $num_per_page) - $num_per_page;
		$query_default .= ' LIMIT ' . $start . ', ' . $num_per_page;

		$results         = $wpdb->get_results( $query_default );
		foreach ( $results as $result ) {
			$published          = SR_Helper::view_status( $result->state );
			$parent_name        = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}sr_categories WHERE id = %d", $result->parent_id ) );
			$name               = apply_filters( 'solidres_asset_category', $result->name );
			$this->items[] = array(
				'name'      => '<strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="' . $name . '">' . $name . '</a></strong>',
				'slug'      => $result->slug,
				'state'     => $published,
				'parent_id' => $parent_name,
				'id'        => $result->id,
			);
		}

		parent::__construct(
			array(
				'singular' => __( 'categogy' ),
				'plural'   => __( 'categogies' ),
				'ajax'     => false,
			)
		);
	}

	function no_items() {
		_e( 'No category found!', 'solidres' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'name'      => array( 'name', false ),
			'slug'      => array( 'slug', false ),
			'state'     => array( 'state', false ),
			'parent_id' => array( 'parent_id', false ),
			'id'        => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'name'      => __( 'Name', 'solidres' ),
			'slug'      => __( 'Slug', 'solidres' ),
			'state'     => __( 'State', 'solidres' ),
			'parent_id' => __( 'Parent ID', 'solidres' ),
			'id'        => __( 'ID', 'solidres' ),
		);

		return $columns;
	}
}

function sr_categories() {
	global $status, $string_search, $list_table_data;
	$helper          = new SR_Helper();
	$list_table_data = new SR_Categories_Table_Data();
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array(
			'_wp_http_referer',
			'_wpnonce'
		), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$helper->listview( 'sr_categories', $action, $string_search, $status, $list_table_data );
}