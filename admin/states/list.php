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

class SR_States_Table_Data extends Solidres_List_Table {
	public $total;

	function __construct() {
		global $status, $state, $wpdb, $string_search, $query_default;
		$states        = new SR_State();
		$status        = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$state_id      = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids           = (array) $state_id;
		$action        = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;
		$query_default = "SELECT t1.*, t2.name as countryname FROM {$wpdb->prefix}sr_geo_states t1 LEFT JOIN {$wpdb->prefix}sr_countries t2 ON t1.country_id = t2.id";
		$state         = SR_Helper::get_listview_state( $status );

		if ( isset( $action ) && $action == 'edit' && isset( $state_id ) && $state_id != null ) {
			sr_edit_state( $state_id );
		}
		if ( $action == 'draft' || $action == 'publish' || $action == 'trash' || $action == 'untrash' ) {
			$states->update_states( $action, $state_id, $ids );
		}
		if ( isset( $action ) && $action == 'delete' && isset( $state_id ) && $state_id != null ) {
			foreach ( $ids as $id ) {
				$state_name = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}sr_geo_states WHERE id = $id" );
				$return     = $states->delete( $id );
				if ( $return === false ) {
					$message = __( 'Error, can not delete <span class="bold"> ' . $state_name->name . ' </span> because it is containing assets or customers or taxes. You must delete all its assets or customers or taxes first.', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				} else {
					$message = __( '<span class="bold"> ' . $state_name->name . ' </span> permanently deleted.', 'solidres' );
					SR_Helper::show_message( $message );
				}
			}
		}

		$filter_state      = isset( $_GET['filter_state'] ) ? $_GET['filter_state'] : null;
		$filter_published  = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : '';
		$filter_country_id = isset( $_GET['filter_country_id'] ) ? $_GET['filter_country_id'] : null;
		$query_filter      = array();
		$results           = '';
		if ( is_numeric( $filter_published ) ) {
			$query_filter[] = ' t1.state = ' . $filter_published;
		}
		if ( $filter_country_id > 0 ) {
			$query_filter[] = ' t1.country_id = ' . $filter_country_id;
		}
		if ( ( $string_search != '' && is_null( $filter_state ) ) || ( $string_search != '' && isset( $filter_state ) && $filter_published == '' && $filter_country_id == '' && $string_search == '' ) ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_default = $query_default . ' WHERE t1.id = ' . (int) substr( $string_search, 3 );
			} else {
				$query_default = $query_default . ' WHERE t1.name LIKE "%' . $string_search . '%"';
			}
		} else if ( ( is_null( $state ) && is_null( $filter_state ) ) || ( isset ( $filter_state ) && $filter_published == '' && $filter_country_id == '' ) ) {
			$query_default = $query_default . " WHERE t1.state = 0 OR t1.state = 1";
		} else if ( $state != null && is_null( $filter_state ) ) {
			$query_default = $query_default . " WHERE t1.state = " . $state;
		} else if ( isset ( $filter_state ) && ( $filter_published != '' || $filter_country_id != '' ) ) {
			if( $string_search == '' ) {
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

		$results         = $wpdb->get_results( $query_default );
		foreach ( $results as $result ) {
			$published          = SR_Helper::view_status( $result->state );
			$this->items[] = array(
				'statename' => '<strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="' . $result->name . '">' . $result->name . '</a></strong>',
				'published' => $published,
				'country'   => $result->countryname,
				'code_2'    => $result->code_2,
				'code_3'    => $result->code_3,
				'id'        => $result->id,
			);
		}
		parent::__construct( array(
			'singular' => __( 'state' ),
			'plural'   => __( 'states' ),
			'ajax'     => false,
		) );
	}

	function no_items() {
		_e( 'No state found!' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'statename' => array( 'statename', false ),
			'published' => array( 'published', false ),
			'country'   => array( 'country', false ),
			'code_2'    => array( 'code_2', false ),
			'code_3'    => array( 'code_3', false ),
			'id'        => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'statename' => __( 'State Name', 'solidres' ),
			'published' => __( 'Published', 'solidres' ),
			'country'   => __( 'Country', 'solidres' ),
			'code_2'    => __( 'Code 2', 'solidres' ),
			'code_3'    => __( 'Code 3', 'solidres' ),
			'id'        => __( 'ID', 'solidres' ),
		);

		return $columns;
	}

	function column_statename( $item ) {
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

		return sprintf( '%1$s %2$s', $item['statename'], $this->row_actions( $actions ) );
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
			<select name="filter_country_id" id="srform_filter_dropdown">
				<option value=""><?php _e( 'Filter by countries', 'solidres' ); ?></option>
				<?php echo SR_Helper::render_list_country( isset( $_GET['filter_country_id'] ) ? $_GET['filter_country_id'] : 0); ?>
			</select>
			<?php submit_button( __( 'Filter', 'solidres' ), 'button', 'filter_state', false ); ?>
		</div>
	<?php }
}

function sr_states() {
	global $list_table_data, $status, $string_search;
	$helper          = new SR_Helper();
	$list_table_data = new SR_States_Table_Data();
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array(
			'_wp_http_referer',
			'_wpnonce'
		), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$helper->listview( 'sr_geo_states', $action, $string_search, $status, $list_table_data );
}