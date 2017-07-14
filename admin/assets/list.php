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

class SR_Assets_Table_Data extends Solidres_List_Table {
	public $total;

	function __construct() {
		global $wpdb, $status, $state, $string_search, $query_default;
		$assets        = new SR_Asset();
		$status        = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$asset_id      = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids           = (array) $asset_id;
		$action        = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;

		$query_default = "SELECT t1.*, t2.name as categoryname, t3.name as countryname,
			( SELECT COUNT(*) FROM {$wpdb->prefix}sr_room_types WHERE {$wpdb->prefix}sr_room_types.reservation_asset_id = t1.id ) as ofroomtype, city, access, hits
			FROM {$wpdb->prefix}sr_reservation_assets t1 LEFT JOIN {$wpdb->prefix}sr_categories t2 ON t1.category_id = t2.id LEFT JOIN {$wpdb->prefix}sr_countries t3 ON t1.country_id = t3.id";

		$state = SR_Helper::get_listview_state( $status );
		if ( isset( $action ) && $action == 'edit' && isset( $asset_id ) && $asset_id != null ) {
			sr_edit_asset( $asset_id );
		}
		if ( $action == 'draft' || $action == 'publish' || $action == 'trash' || $action == 'untrash' ) {
			$assets->update_states( $action, $asset_id, $ids );
		}
		if ( isset( $action ) && $action == 'delete' && isset( $asset_id ) && $asset_id != null ) {
			foreach ( $ids as $id ) {
				$asset_name = $wpdb->get_row( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}sr_reservation_assets WHERE id = %d", $id ) );
				$return     = $assets->delete( $id );
				if ( $return === false ) {
					$message = __( 'Error, can not delete <span class="bold"> ' . $asset_name->name . ' </span> because it is containing room types. You must delete all its room types first.', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				} else {
					$message = __( '<span class="bold"> ' . $asset_name->name . ' </span> permanently deleted.', 'solidres' );
					SR_Helper::show_message( $message );
				}
			}
		}

		$filter_asset       = isset( $_GET['filter_asset'] ) ? $_GET['filter_asset'] : null;
		$filter_published   = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : null;
		$filter_category_id = isset( $_GET['filter_category_id'] ) ? $_GET['filter_category_id'] : null;
		$filter_country_id  = isset( $_GET['filter_country_id'] ) ? $_GET['filter_country_id'] : null;
		$filter_city        = isset( $_GET['filter_city_listing'] ) ? $_GET['filter_city_listing'] : '';
		$query_filter = array();
		if ( is_numeric( $filter_published ) ) {
			$query_filter[] = ' t1.state = ' . $filter_published;
		}
		if ( $filter_category_id > 0 ) {
			$query_filter[] = ' t1.category_id = ' . $filter_category_id;
		}
		if ( $filter_country_id > 0 ) {
			$query_filter[] = ' t1.country_id = ' . $filter_country_id;
		}
		if ( $filter_city != '' ) {
			$query_filter[] = ' t1.city LIKE "%' . $filter_city . '%"';
		}

		if ( ( $string_search != '' && is_null( $filter_asset ) ) || ( $string_search != '' && isset( $filter_asset ) && $filter_published == '' && $filter_category_id == '' && $filter_country_id == '' && $filter_city == '' ) ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_default = $query_default . ' WHERE t1.id = ' . (int) substr( $string_search, 3 );
			} else {
				$query_default = $query_default . ' WHERE t1.name LIKE "%' . $string_search . '%"';
			}
		} else if ( ( is_null( $state ) && is_null( $filter_asset ) ) || ( isset ( $filter_asset ) && $filter_published == '' && $filter_category_id == '' && $filter_country_id == '' && $filter_city == '' && $string_search == '' ) ) {
			$query_default = $query_default . " WHERE t1.state = 0 OR t1.state = 1";
		} else if ( $state != null && is_null( $filter_asset ) ) {
			$query_default = $query_default . " WHERE t1.state = " . $state;
		} else if ( isset ( $filter_asset ) && ( $filter_published != '' || $filter_category_id != '' || $filter_country_id != '' || $filter_city != '' ) ) {
			if ( $string_search == '' ) {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter );
			} else {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter ) . ' AND t1.name LIKE "%' . $string_search . '%"';
			}
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
			$name               = apply_filters( 'solidres_asset_name', $result->name );
			$this->items[] = array(
				'name'       => '<strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="' . $name . '">' . $name . '</a></strong>' . ( $result->default == 1 ? ' <span class="dashicons solidres-default-asset dashicons-star-filled"></span>': '' ),
				'published'  => $published,
				'category'   => '<a href="?page=sr-categories&action=edit&id=' . $result->category_id . '">' . apply_filters( 'solidres_category_name', $result->categoryname ) . '</a>',
				'ofroomtype' => '<a href="?page=sr-room-types&action=-1&filter_roomtypes=Filter&filter_reservation_asset_id=' . $result->id . '">' . $result->ofroomtype . '</a>',
				'city'       => $result->city,
				'country'    => '<a href="?page=sr-countries&action=edit&id=' . $result->country_id . '">' . $result->countryname . '</a>',
				'hits'       => $result->hits,
				'id'         => $result->id,
			);
		}
		parent::__construct( array(
			'singular' => __( 'asset', 'solidres' ),
			'plural'   => __( 'assets', 'solidres' ),
			'ajax'     => false,
		) );
	}

	function no_items() {
		_e( 'No asset found.', 'solidres' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'name'       => array( 'name', false ),
			'published'  => array( 'published', false ),
			'category'   => array( 'category', false ),
			'ofroomtype' => array( 'ofroomtype', false ),
			'city'       => array( 'city', false ),
			'country'    => array( 'country', false ),
			'hits'       => array( 'hits', false ),
			'id'         => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'name'       => __( 'Name', 'solidres' ),
			'published'  => __( 'Published', 'solidres' ),
			'category'   => __( 'Category', 'solidres' ),
			'ofroomtype' => __( 'Room type', 'solidres' ),
			'city'       => __( 'City', 'solidres' ),
			'country'    => __( 'Country', 'solidres' ),
			'hits'       => __( 'Hits', 'solidres' ),
			'id'         => __( 'ID', 'solidres' ),
		);

		return $columns;
	}

	function extra_tablenav( $which ) {
		if ( 'top' != $which ) {
			return;
		} ?>
		<div class="alignleft actions bulkactions">
			<?php $filter_published = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : ''; ?>
			<select name="filter_published" id="srform_filter_dropdown">
				<option <?php echo $filter_published === '' ? 'selected' : '' ?> value=""><?php _e( 'Filter by status', 'solidres' ); ?></option>
				<option value="1" <?php echo $filter_published !== '' && $filter_published === '1' ? 'selected' : '' ?>><?php _e( 'Published', 'solidres' ); ?></option>
				<option value="0" <?php echo $filter_published !== '' && $filter_published === '0' ? 'selected' : '' ?>><?php _e( 'Unpublished', 'solidres' ); ?></option>
				<option value="-2" <?php echo $filter_published !== '' && $filter_published === '-2' ? 'selected' : '' ?>><?php _e( 'Trashed', 'solidres' ); ?></option>
			</select>
			<select name="filter_category_id" id="srform_filter_dropdown">
				<option value=""><?php _e( 'Filter by categories', 'solidres' ); ?></option>
				<?php echo SR_Helper::render_list_category( isset($_GET['filter_category_id']) ? $_GET['filter_category_id'] : 0); ?>
			</select>
			<select name="filter_country_id" id="srform_filter_dropdown">
				<option value=""><?php _e( 'Filter by countries', 'solidres' ); ?></option>
				<?php echo SR_Helper::render_list_country( isset( $_GET['filter_country_id'] ) ? $_GET['filter_country_id'] : 0); ?>
			</select>
			<input type="text" name="filter_city_listing" value="<?php if ( isset( $_GET['filter_city_listing'] ) ) {
				echo $_GET['filter_city_listing'];
			} ?>" placeholder="<?php _e( 'Search by city', 'solidres' ); ?>"/>
			<?php submit_button( __( 'Filter', 'solidres' ), 'button', 'filter_asset', false ); ?>
		</div>
	<?php }
}

function sr_assets() {
	global $status, $list_table_data, $string_search;
	$helper          = new SR_Helper();
	$list_table_data = new SR_Assets_Table_Data();

	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array(
			'_wp_http_referer',
			'_wpnonce'
		), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$helper->listview( 'sr_reservation_assets', $action, $string_search, $status, $list_table_data );
}