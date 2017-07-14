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

class SR_Coupons_Table_Data extends Solidres_List_Table {
	public $total;

	function __construct() {
		global $status, $state, $string_search, $query_default, $wpdb;
		$coupons       = new SR_Coupon();
		$status        = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$coupon_id     = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids           = (array) $coupon_id;
		$action        = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;
		$query_default = "SELECT t1.*, t2.name as assetname FROM {$wpdb->prefix}sr_coupons t1  LEFT JOIN {$wpdb->prefix}sr_reservation_assets t2 ON t1.reservation_asset_id = t2.id";
		$state         = SR_Helper::get_listview_state( $status );

		if ( isset( $action ) && $action == 'edit' && isset( $coupon_id ) && $coupon_id != null ) {
			sr_edit_coupon( $coupon_id );
		}
		if ( $action == 'draft' || $action == 'publish' || $action == 'trash' || $action == 'untrash' ) {
			$coupons->update_states( $action, $coupon_id, $ids );
		}
		if ( isset( $action ) && $action == 'delete' && isset( $coupon_id ) && $coupon_id != null ) {
			foreach ( $ids as $id ) {
				$coupons->delete( $id );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 coupon permanently deleted.', 'solidres' );
				SR_Helper::show_message( $message, 'error' );
			} else {
				$message = __( count( $ids ) . ' coupons permanently deleted.', 'solidres' );
				SR_Helper::show_message( $message );
			}
		}

		$filter_coupons              = isset( $_GET['filter_coupons'] ) ? $_GET['filter_coupons'] : null;
		$filter_published            = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : '';
		$filter_reservation_asset_id = isset( $_GET['filter_reservation_asset_id'] ) ? $_GET['filter_reservation_asset_id'] : null;
		$query_filter                = array();
		$results                     = '';
		if ( is_numeric( $filter_published ) ) {
			$query_filter[] = ' t1.state = ' . $filter_published;
		}
		if ( $filter_reservation_asset_id > 0 ) {
			$query_filter[] = ' t1.reservation_asset_id = ' . $filter_reservation_asset_id;
		}
		if ( ( $string_search != '' && is_null( $filter_coupons ) ) || ( $string_search != '' && isset( $filter_coupons ) && $filter_published == '' && $filter_reservation_asset_id == '' ) ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_default = $query_default . ' WHERE t1.id = ' . (int) substr( $string_search, 3 );
			} else {
				$query_default = $query_default . ' WHERE t1.coupon_name LIKE "%%' . $string_search . '%%"';
			}
		} else if ( ( is_null( $state ) && is_null( $filter_coupons ) ) || ( isset ( $filter_coupons ) && $filter_published == '' && $filter_reservation_asset_id == '' && $string_search == '' ) ) {
			$query_default = $query_default . " WHERE t1.state = 0 OR t1.state = 1";
		} else if ( $state != null && is_null( $filter_coupons ) ) {
			$query_default = $query_default . " WHERE t1.state = " . $state;
		} else if ( isset ( $filter_coupons ) && ( $filter_published != '' || $filter_reservation_asset_id != '' ) ) {
			if ( $string_search == '' ) {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter );
			} else {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter ) . ' AND t1.coupon_name LIKE "%%' . $string_search . '%%"';
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
			$quantity           = ( $result->quantity == '' ) ? __( 'Unlimited', 'solidres' ) : $result->quantity;
			$percent            = ( $result->is_percent == 1 ) ? __( 'Yes', 'solidres' ) : __( 'No', 'solidres' );
			$name               = apply_filters( 'solidres_coupon_name', $result->coupon_name );
			$this->items [] = array(
				'name'       => '<strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="' . $name . '">' . $name . '</a>',
				'published'  => $published,
				'asset'      => '<a href="?page=sr-assets&action=edit&id=' . $result->reservation_asset_id . '">' . apply_filters( 'solidres_asset_name', $result->assetname ) . '</a>',
				'couponcode' => '<span class="couponcode">' . $result->coupon_code . '</span>',
				'amount'     => $result->amount,
				'percent'    => $percent,
				'quantity'   => $quantity,
				'validfrom'  => $result->valid_from_checkin,
				'validto'    => $result->valid_to_checkin,
				'id'         => $result->id,
			);
		}
		parent::__construct( array(
			'singular' => __( 'coupon' ),
			'plural'   => __( 'coupons' ),
			'ajax'     => false,
		) );
	}

	function no_items() {
		_e( 'No coupon found!', 'solidres' );
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'name'       => array( 'name', false ),
			'published'  => array( 'published', false ),
			'asset'      => array( 'asset', false ),
			'couponcode' => array( 'couponcode', false ),
			'amount'     => array( 'amount', false ),
			'percent'    => array( 'percent', false ),
			'quantity'   => array( 'quantity', false ),
			'validfrom'  => array( 'validfrom', false ),
			'validto'    => array( 'validto', false ),
			'id'         => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'name'       => __( 'Coupon name', 'solidres' ),
			'published'  => __( 'Published', 'solidres' ),
			'asset'      => __( 'Asset', 'solidres' ),
			'couponcode' => __( 'Coupon code', 'solidres' ),
			'amount'     => __( 'Amount', 'solidres' ),
			'percent'    => __( 'Percent', 'solidres' ),
			'quantity'   => __( 'Quantity', 'solidres' ),
			'validfrom'  => __( 'Valid from', 'solidres' ),
			'validto'    => __( 'Valid to', 'solidres' ),
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
			<select name="filter_reservation_asset_id" id="srform_filter_dropdown">
				<option value=""><?php _e( 'Filter by assets', 'solidres' ); ?></option>
				<?php echo SR_Helper::render_list_asset( isset( $_GET['filter_reservation_asset_id'] ) ? $_GET['filter_reservation_asset_id'] : 0 ); ?>
			</select>
			<?php submit_button( __( 'Filter', 'solidres' ), 'button', 'filter_coupons', false ); ?>
		</div>
	<?php }
}

function sr_coupons() {
	global $list_table_data, $string_search, $status;
	$helper          = new SR_Helper();
	$list_table_data = new SR_Coupons_Table_Data();
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array(
			'_wp_http_referer',
			'_wpnonce'
		), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$helper->listview( 'sr_coupons', $action, $string_search, $status, $list_table_data );
}