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

class SR_Taxes_Table_Data extends Solidres_List_Table {
	public $total;

	function __construct() {
		global $wpdb, $state, $status, $string_search, $query_default;
		$taxes         = new SR_Tax();
		$status        = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$tax_id        = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids           = (array) $tax_id;
		$action        = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;
		$query_default = "SELECT * FROM {$wpdb->prefix}sr_taxes";
		$state         = SR_Helper::get_listview_state( $status );

		if ( isset( $action ) && $action == 'edit' && isset( $tax_id ) && $tax_id != null ) {
			sr_edit_tax( $tax_id );
		}
		if ( $action == 'draft' || $action == 'publish' || $action == 'trash' || $action == 'untrash' ) {
			$taxes->update_states( $action, $tax_id, $ids );
		}
		if ( isset( $action ) && $action == 'delete' && isset( $tax_id ) && $tax_id != null ) {
			foreach ( $ids as $id ) {
				$tax_name = $wpdb->get_row( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}sr_taxes WHERE id = %d", $id ) );
				$return   = $taxes->delete( $id );
				if ( $return === false ) {
					$message = __( 'Error, can not delete <span class="bold"> ' . $tax_name->name . ' </span> because it is containing states. You must delete all its states first.', 'solidres' );
					SR_Helper::show_message( $message, 'error' );
				} else {
					$message = __( '<span class="bold"> ' . $tax_name->name . ' </span> permanently deleted.', 'solidres' );
					SR_Helper::show_message( $message );
				}
			}
		}

		$filter_taxes     = isset( $_GET['filter_taxes'] ) ? $_GET['filter_taxes'] : null;
		$filter_published = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : '';
		$query_filter     = array();
		$results          = '';
		if ( is_numeric( $filter_published ) ) {
			$query_filter[] = ' state = ' . $filter_published;
		}
		if ( ( $string_search != '' && is_null( $filter_taxes ) ) || ( $string_search != '' && isset( $filter_taxes ) && $filter_published == '' ) ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_default = $query_default . ' WHERE id = ' . (int) substr( $string_search, 3 );
			} else {
				$query_default = $query_default . ' WHERE name LIKE "%' . $string_search . '%"';
			}
		} else if ( ( is_null( $state ) && is_null( $filter_taxes ) ) || ( isset ( $filter_taxes ) && $filter_published == '' && $string_search == '' ) ) {
			$query_default = $query_default . " WHERE state = 0 OR state = 1";
		} else if ( $state != null && is_null( $filter_taxes ) ) {
			$query_default = $query_default . " WHERE state = " . $state;
		} else if ( isset ( $filter_taxes ) && ( $filter_published != '' ) ) {
			if ( $string_search == '' ) {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter );
			} else {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter ) . ' AND name LIKE "%' . $string_search . '%"';
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
				'name'      => '<strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="' . $result->name . '">' . $result->name . '</a></strong>',
				'rate'      => $result->rate,
				'published' => $published,
				'id'        => $result->id,
			);
		}
		parent::__construct( array(
			'singular' => __( 'tax' ),
			'plural'   => __( 'taxes' ),
			'ajax'     => false,
		) );
	}

	function no_items() {
		_e( 'No tax found!', 'solidres' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'name'      => array( 'name', false ),
			'rate'      => array( 'rate', false ),
			'published' => array( 'published', false ),
			'id'        => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'name'      => __( 'Name', 'solidres' ),
			'rate'      => __( 'Rate', 'solidres' ),
			'published' => __( 'Published', 'solidres' ),
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
			<?php submit_button( __( 'Filter', 'solidres' ), 'button', 'filter_taxes', false ); ?>
		</div>
	<?php }
}

function sr_taxes() {
	global $list_table_data, $status, $string_search;
	$helper          = new SR_Helper();
	$list_table_data = new SR_Taxes_Table_Data();
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array(
			'_wp_http_referer',
			'_wpnonce'
		), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$helper->listview( 'sr_taxes', $action, $string_search, $status, $list_table_data );
}