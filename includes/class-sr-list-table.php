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

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Solidres_List_Table extends WP_List_Table {

	function column_name( $item ) {
		global $status;
		if ( $status == 'trash' ) {
			$actions = array(
				'untrash' => sprintf( __( '<a href="?page=%s&action=%s&id=%s">Restore</a>', 'solidres' ), $_REQUEST['page'],'untrash',$item['id'] ),
				'delete' => sprintf( __( '<a href="?page=%s&action=%s&id=%s">Delete Permanently</a>', 'solidres' ), $_REQUEST['page'],'delete',$item['id'] ),
			);
		} else {
			$actions = array(
				'edit' => sprintf( __( '<a href="?page=%s&action=%s&id=%s">Edit</a>', 'solidres' ), $_REQUEST['page'],'edit',$item['id'] ),
				'trash' => sprintf( __( '<a href="?page=%s&action=%s&id=%s">Trash</a>', 'solidres' ), $_REQUEST['page'],'trash',$item['id'] ),
			);
		}
		return sprintf( '%1$s %2$s', $item['name'], $this->row_actions( $actions ) );
	}

	function usort_reorder( $a, $b ) {
		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : (get_class($this) == 'SR_Reservations_Table_Data' ? 'desc' : 'asc');
		if ( !in_array($orderby, array('id', 'hit', 'ofroomtype', 'numofroom', 'adult', 'child'))) {
			$result = strcmp( $a[ $orderby ], $b[ $orderby ] );
			return ( $order === 'asc' ) ? $result : -$result;
		} else {
			$result = ($a[ $orderby ] < $b[ $orderby ]) ? -1 : 1;
			return ( $order === 'asc' ) ? $result : -$result;
		}
	}

	function get_bulk_actions() {
		global $status;
		if ( $status == 'trash' ) {
			$actions = array(
				'untrash' => __( 'Restore', 'solidres' ),
				'delete' => __( 'Delete Permanently', 'solidres' ),
			);
		}
		else if ( $status == 'draft' ) {
			$actions = array(
				'publish' => __( 'Move to Publish', 'solidres' ),
				'trash' => __( 'Move to Trash', 'solidres' ),
			);
		} else if ( $status == 'publish' ) {
			$actions = array(
				'draft' => __( 'Move to Draft', 'solidres' ),
				'trash' => __( 'Move to Trash', 'solidres' ),
			);
		} else {
			$actions = array(
				'trash' => __( 'Move to Trash', 'solidres' ),
			);
		}
		return $actions;
	}


	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
	}

	function prepare_items() {
		$options = get_option( 'solidres_plugin' );
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		if (count($this->items) > 0) {
			usort( $this->items, array( &$this, 'usort_reorder' ) );
		}
		$per_page = isset( $options['list_limit'] ) ? $options['list_limit'] : 5;
		$this->set_pagination_args( array(
			'total_items' => $this->total,
			'per_page'    => $per_page,
		));
	}

	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}
}