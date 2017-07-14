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

class SR_Room_Types_Table_Data extends Solidres_List_Table {
	public $total;

	function __construct() {
		global $wpdb, $status, $state, $string_search, $query_default;
		$room_types    = new SR_Room_Type();
		$status        = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$room_type_id  = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids           = (array) $room_type_id;
		$action        = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;
		$query_default = "SELECT t1.*, t2.name as reservation_assets_name, (SELECT COUNT(*) FROM {$wpdb->prefix}sr_rooms WHERE t1.id = {$wpdb->prefix}sr_rooms.room_type_id) as numofroom, occupancy_adult, occupancy_child FROM {$wpdb->prefix}sr_room_types t1 LEFT JOIN {$wpdb->prefix}sr_reservation_assets t2 ON t1.reservation_asset_id = t2.id";
		$state         = SR_Helper::get_listview_state( $status );

		if ( isset( $action ) && $action == 'edit' && isset( $room_type_id ) && $room_type_id != null ) {
			sr_edit_room_type( $room_type_id );
		}
		if ( $action == 'draft' || $action == 'publish' || $action == 'trash' || $action == 'untrash' ) {
			$room_types->update_states( $action, $room_type_id, $ids );
		}
		if ( isset( $action ) && $action == 'delete' && isset( $room_type_id ) && $room_type_id != null ) {
			foreach ( $ids as $id ) {
				$room_types->delete( $id );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 room types permanently deleted.', 'solidres' );
				SR_Helper::show_message( $message, 'error' );
			} else {
				$message = __( count( $ids ) . ' rooms types permanently deleted.', 'solidres' );
				SR_Helper::show_message( $message );
			}
		}
		$filter_published            = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : '';
		$filter_reservation_asset_id = isset( $_GET['filter_reservation_asset_id'] ) ? $_GET['filter_reservation_asset_id'] : null;
		$filter_roomtypes            = isset( $_GET['filter_roomtypes'] ) ? $_GET['filter_roomtypes'] : null;
		$query_filter                = array();
		$results                     = '';
		if ( is_numeric( $filter_published ) ) {
			$query_filter[] = ' t1.state = ' . $filter_published;
		}
		if ( $filter_reservation_asset_id > 0 ) {
			$query_filter[] = ' t1.reservation_asset_id = ' . $filter_reservation_asset_id;
		}
		if ( ( $string_search != '' && is_null( $filter_roomtypes ) ) || ( $string_search != '' && isset( $filter_roomtypes ) && $filter_published == '' && $filter_reservation_asset_id == '' ) ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_default = $query_default . ' WHERE t1.id = ' . (int) substr( $string_search, 3 );
			} else {
				$query_default = $query_default . ' WHERE t1.name LIKE "%%' . $string_search . '%%"';
			}
		} else if ( ( is_null( $state ) && is_null( $filter_roomtypes ) ) || ( isset ( $filter_roomtypes ) && $filter_published == '' && $filter_reservation_asset_id == '' && $string_search == '' ) ) {
			$query_default = $query_default . " WHERE t1.state = 0 OR t1.state = 1";
		} else if ( $state != null && is_null( $filter_roomtypes ) ) {
			$query_default = $query_default . " WHERE t1.state = " . $state;
		} else if ( isset ( $filter_roomtypes ) && ( $filter_published != '' || $filter_reservation_asset_id != '' ) ) {
			if ( $string_search == '' ) {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter );
			} else {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter ) . ' AND t1.name LIKE "%%' . $string_search . '%%"';
			}
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
			$name               = apply_filters( 'solidres_roomtype_name', $result->name );
			$this->items[] = array(
				'name'      => '<strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="' . $name . '">' . $name . '</a></strong>',
				'published' => $published,
				'asset'     => '<a href="?page=sr-assets&action=edit&id=' . $result->reservation_asset_id . '">' . apply_filters( 'solidres_asset_name', $result->reservation_assets_name ) . '</a>',
				'numofroom' => $result->numofroom,
				'adult'     => $result->occupancy_adult,
				'child'     => $result->occupancy_child,
				'id'        => $result->id,
			);
		}
		parent::__construct( array(
			'singular' => __( 'roomtype' ),
			'plural'   => __( 'roomtypes' ),
			'ajax'     => false,
		) );
	}

	function no_items() {
		_e( 'No room type found!' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'name'      => array( 'name', false ),
			'published' => array( 'published', false ),
			'asset'     => array( 'asset', false ),
			'numofroom' => array( 'numofroom', false ),
			'adult'     => array( 'adult', false ),
			'child'     => array( 'child', false ),
			'id'        => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'name'      => __( 'Name', 'solidres' ),
			'published' => __( 'Published', 'solidres' ),
			'asset'     => __( 'Asset', 'solidres' ),
			'numofroom' => __( 'Rooms', 'solidres' ),
			'adult'     => __( '#Adult(s)', 'solidres' ),
			'child'     => __( '#Child(ren)', 'solidres' ),
			'id'        => __( 'ID', 'solidres' ),
		);

		return $columns;
	}

	function extra_tablenav( $which ) {
		if ( 'top' != $which ) {
			return;
		} ?>
		<div class="alignleft actions bulkactions">
			<?php
			$filter_published = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : '';
			?>
			<select name="filter_published" id="srform_filter_dropdown">
				<option <?php echo $filter_published === '' ? 'selected' : '' ?> value=""><?php _e( 'Filter by status', 'solidres' ); ?></option>
				<option value="1" <?php echo $filter_published !== '' && $filter_published === '1' ? 'selected' : '' ?>><?php _e( 'Published', 'solidres' ); ?></option>
				<option value="0" <?php echo $filter_published !== '' && $filter_published === '0' ? 'selected' : '' ?>><?php _e( 'Unpublished', 'solidres' ); ?></option>
				<option value="-2" <?php echo $filter_published !== '' && $filter_published === '-2' ? 'selected' : '' ?>><?php _e( 'Trashed', 'solidres' ); ?></option>
			</select>
			<select name="filter_reservation_asset_id" id="srform_filter_dropdown">
				<option value=""><?php _e( 'Filter by assets', 'solidres' ); ?></option>
				<?php echo SR_Helper::render_list_asset( isset( $_GET['filter_reservation_asset_id'] ) ? $_GET['filter_reservation_asset_id']  : 0 ); ?>
			</select>
			<?php submit_button( __( 'Filter', 'solidres' ), 'button', 'filter_roomtypes', false ); ?>
		</div>
	<?php }
}

function sr_room_types() {
	global $status, $string_search, $list_table_data;
	$helper          = new SR_Helper();
	$list_table_data = new SR_Room_Types_Table_Data();
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array(
			'_wp_http_referer',
			'_wpnonce'
		), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$helper->listview( 'sr_room_types', $action, $string_search, $status, $list_table_data );
}