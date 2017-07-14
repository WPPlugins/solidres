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

class SR_Reservations_Table_Data extends Solidres_List_Table {
	public $total;

	public $statuses;

	function __construct() {
		global $wpdb, $status, $state, $string_search, $query_default;
		$reservations   = new SR_Reservation();
		$status         = isset( $_GET['status'] ) ? $_GET['status'] : null;
		$string_search  = isset( $_GET['s'] ) ? $_GET['s'] : null;
		$reservation_id = isset( $_GET['id'] ) ? $_GET['id'] : null;
		$ids            = (array) $reservation_id;
		$action         = isset( $_GET['action'] ) && $_GET[ 'action' ] != -1 ? $_GET['action'] : ( isset( $_GET['action2'] ) ? $_GET['action2'] : NULL) ;
		$query_default  = "SELECT * FROM {$wpdb->prefix}sr_reservations";
		$state          = SR_Helper::get_listview_state( $status );
		wp_enqueue_script( 'solidres_editable' );
		wp_enqueue_style( 'solidres_editable', false );
		if ( isset( $action ) && $action == 'edit' && isset( $reservation_id ) && $reservation_id != null ) {
			sr_edit_reservation_item( $reservation_id );
		}

		if ( isset( $action ) && $action == 'amend' ) {
			sr_amend_reservation_item( $reservation_id );
		}

		if ( isset( $action ) && $action == 'export_csv' && isset( $reservation_id ) && $reservation_id != null ) {
			sr_export_reservation_to_csv( $ids );
		}

		if ( $action == 'trash' || $action == 'untrash' ) {
			$reservations->update_states( $action, $reservation_id, $ids );
		}

		if ( isset( $action ) && $action == 'delete' && isset( $reservation_id ) && $reservation_id != null ) {
			foreach ( $ids as $id ) {
				$reservations->delete( $id );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 reservation permanently deleted.', 'solidres' );
				SR_Helper::show_message( $message, 'error' );
			} else {
				$message = __( count( $ids ) . ' reservations permanently deleted.', 'solidres' );
				SR_Helper::show_message( $message );
			}
		}
		$filter_reservation          = isset( $_GET['filter_reservation'] ) ? $_GET['filter_reservation'] : null;
		$filter_reservation_asset_id = isset( $_GET['filter_reservation_asset_id'] ) ? $_GET['filter_reservation_asset_id'] : null;
		$filter_published            = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : '';
		$filter_payment_status       = isset( $_GET['filter_payment_status'] ) ? $_GET['filter_payment_status'] : null;
		$filter_customer_fullname    = isset( $_GET['filter_customer_fullname'] ) ? $_GET['filter_customer_fullname'] : '';
		$filter_checkin_from         = isset( $_GET['filter_checkin_from'] ) ? $_GET['filter_checkin_from'] : null;
		$filter_checkin_to           = isset( $_GET['filter_checkin_to'] ) ? $_GET['filter_checkin_to'] : '';
		$filter_checkout_from        = isset( $_GET['filter_checkout_from'] ) ? $_GET['filter_checkout_from'] : null;
		$filter_checkout_to          = isset( $_GET['filter_checkout_to'] ) ? $_GET['filter_checkout_to'] : null;
		$query_filter                = array();
		$results                     = '';
		if ( is_numeric( $filter_published ) ) {
			$query_filter[] = ' state = ' . $filter_published;
		}
		if ( $filter_reservation_asset_id > 0 ) {
			$query_filter[] = ' reservation_asset_id = ' . $filter_reservation_asset_id;
		}
		if ( is_numeric( $filter_payment_status ) ) {
			$query_filter[] = ' payment_status = ' . $filter_payment_status;
		}
		if ( $filter_customer_fullname != '' ) {
			$query_filter[] = ' customer_firstname LIKE "%' . $filter_customer_fullname . '%" OR customer_middlename LIKE "%' . $filter_customer_fullname . '%" OR customer_lastname LIKE "%' . $filter_customer_fullname . '%"';
		}
		if ( $filter_checkin_from != '' && $filter_checkin_to != '' ) {
			$query_filter[] = ' checkin >= "' . date( 'Y-m-d', strtotime( $filter_checkin_from ) ) . '" AND checkin <= "' . date( 'Y-m-d', strtotime( $filter_checkin_to ) ) . '"';
		}
		if ( $filter_checkout_from != '' && $filter_checkout_to != '' ) {
			$query_filter[] = ' checkout >= "' . date( 'Y-m-d', strtotime( $filter_checkout_from ) ) . '" AND checkout <= "' . date( 'Y-m-d', strtotime( $filter_checkout_to ) ) . '"';
		}
		if ( ( $string_search != '' && is_null( $filter_reservation ) ) || ( $string_search != '' && isset( $filter_reservation ) && $filter_reservation_asset_id == '' && $filter_published == '' && $filter_payment_status == '' && $filter_customer_fullname == '' && $filter_checkin_from == '' && $filter_checkin_to == '' && $filter_checkout_from == '' && $filter_checkout_to == '' ) ) {
			if ( stripos( $string_search, 'id:' ) === 0 ) {
				$query_default = $query_default . ' WHERE id = ' . (int) substr( $string_search, 3 );
			} else {
				$query_default = $query_default . ' WHERE code LIKE "%' . $string_search . '%"';
			}
		} else if ( ( is_null( $state ) && is_null( $filter_reservation ) ) || ( isset ( $filter_reservation ) && $filter_reservation_asset_id == '' && $filter_published == '' && $filter_payment_status == '' && $filter_customer_fullname == '' && $filter_checkin_from == '' && $filter_checkin_to == '' && $filter_checkout_from == '' && $filter_checkout_to == '' ) ) {
			$query_default = $query_default . " WHERE state = 0 OR state = 1 OR state = 2 OR state = 3 OR state = 4 OR state = 5";
		} else if ( $state != null && is_null( $filter_reservation ) ) {
			$query_default = $query_default . " WHERE state = " . $state;
		} else if ( isset ( $filter_reservation ) && ( $filter_reservation_asset_id != '' || $filter_published != '' || $filter_payment_status != '' || $filter_customer_fullname != '' || $filter_checkin_from != '' || $filter_checkin_to != '' || $filter_checkout_from != '' || $filter_checkout_to != '' ) ) {
			if ( $string_search == '' ) {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter );
			} else {
				$query_default = $query_default . ' WHERE ' . implode( ' AND', $query_filter ) . ' AND code LIKE "%' . $string_search . '%"';
			}
		}

		$query_default = $query_default . ' ORDER BY id DESC';

		$this->total = count($wpdb->get_results( $query_default ));

		$options = get_option( 'solidres_plugin' );
		$page_num = $this->get_pagenum();
		$num_per_page = isset( $options['list_limit'] ) ? $options['list_limit'] : 5;
		$start = ($page_num * $num_per_page) - $num_per_page;
		$query_default .= ' LIMIT ' . $start . ', ' . $num_per_page;

		$badges = array(
			0 => 'pending_code',
			1 => 'checkin_code',
			2 => 'checkout_code',
			3 => 'closed_code',
			4 => 'canceled_code',
			5 => 'confirmed_code',
			-2 => 'trashed_code'
		);

		$this->statuses = array(
			0 => __( 'Pending arrival', 'solidres' ),
			1 => __( 'Checked-in', 'solidres' ),
			2 => __( 'Checked-out', 'solidres' ),
			3 => __( 'Closed', 'solidres' ),
			4 => __( 'Canceled', 'solidres' ),
			5 => __( 'Confirmed', 'solidres' ),
			-2 => __( 'Trashed', 'solidres' )
		);

		$results = $wpdb->get_results( $query_default );
		foreach ( $results as $result ) {
			$paymentstatus      = SR_Reservation::payment_status( $result->payment_status );
			$customerfullname   = $result->customer_firstname . ' ' . $result->customer_lastname;
			$this->items[] = array(
				'codename'      => '<span class="' . $badges[ $result->state ] .' reservation-code"><strong><a class="row-title" href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $result->id . '" aria-label="'. $result->code .'">'. $result->code . '</a></strong></span>',
				'asset'         => apply_filters( 'solidres_asset_name', $result->reservation_asset_name ),
				'status'        => $this->statuses[ $result->state ],
				'paymentstatus' => $paymentstatus,
				'customer'      => $customerfullname,
				'checkin'       => date( get_option( 'date_format' ), strtotime( $result->checkin ) ),
				'checkout'      => date( get_option( 'date_format' ), strtotime( $result->checkout ) ) ,
				'createdate'    => date( get_option( 'date_format' ), strtotime( $result->created_date ) ),
				'state'         => $result->state,
				'reservation_asset_id' => $result->reservation_asset_id,
				'accessed_date' => $result->accessed_date,
				'origin'        => $result->origin,
				'id'            => $result->id,
			);
		}
		parent::__construct( array(
			'singular' => __( 'reservation' ),
			'plural'   => __( 'reservations' ),
			'ajax'     => false,
		) );
	}

	function no_items() {
		_e( 'No reservation found!', 'solidres' );
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'status':
				return '<p><a href="#"
				      id="state' . $item[ 'id' ]  .'"
				      class="state_edit"
				      data-type="select"
				      data-name="state"
				      data-pk="'. $item[ 'id' ] .'"
				      data-value="'. $item[ 'state' ] .'"
				      data-assetid="'. $item[ 'reservation_asset_id' ] .'"
				      data-original-title="">'. $this->statuses[ $item[ 'state' ] ] .'</a></p>';
			default:
				return $item[ $column_name ];
		}
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'codename'      => array( 'codename', false ),
			'asset'         => array( 'asset', false ),
			'status'        => array( 'status', false ),
			'paymentstatus' => array( 'paymentstatus', false ),
			'customer'      => array( 'customer', false ),
			'checkin'       => array( 'checkin', false ),
			'checkout'      => array( 'checkout', false ),
			'createdate'    => array( 'createdate', false ),
			'origin'        => array( 'origin', false ),
			'id'            => array( 'id', false ),
		);

		return $sortable_columns;
	}

	function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'codename'      => __( 'Code Name', 'solidres' ),
			'asset'         => __( 'Asset', 'solidres' ),
			'status'        => __( 'Status', 'solidres' ),
			'paymentstatus' => __( 'Payment status', 'solidres' ),
			'customer'      => __( 'Customer', 'solidres' ),
			'checkin'       => __( 'Check-in', 'solidres' ),
			'checkout'      => __( 'Check-out', 'solidres' ),
			'createdate'    => __( 'Created Date', 'solidres' ),
			'origin'        => __( 'Origin', 'solidres' ),
			'id'            => __( 'ID', 'solidres' ),
		);

		return $columns;
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {
		echo '<tr class="'.($item[ 'accessed_date' ] == '0000-00-00 00:00:00' ? 'active' : '' ).'">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	function column_codename( $item ) {
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

		return sprintf( '%1$s %2$s', $item['codename'], $this->row_actions( $actions ) );
	}

	function get_bulk_actions() {
		global $status;
		if ( $status == 'trash' ) {
			$actions = array(
				'untrash' => __( 'Restore', 'solidres' ),
				'delete'  => __( 'Delete Permanently', 'solidres' ),
			);
		} else {
			$actions = array(
				'trash'      => __( 'Move to Trash', 'solidres' ),
				'export_csv' => __( 'Export to CSV', 'solidres' ),
			);
		}

		return $actions;
	}

	function extra_tablenav( $which ) {
		if ( 'top' != $which ) {
			return;
		}
		$filter_checkin_from  = isset( $_GET['filter_checkin_from'] ) ? $_GET['filter_checkin_from'] : null;
		$filter_checkin_to    = isset( $_GET['filter_checkin_to'] ) ? $_GET['filter_checkin_to'] : null;
		$filter_checkout_from = isset( $_GET['filter_checkout_from'] ) ? $_GET['filter_checkout_from'] : null;
		$filter_checkout_to   = isset( $_GET['filter_checkout_to'] ) ? $_GET['filter_checkout_to'] : null;
		?>
		<script>
			jQuery(function($) {
				$.fn.editable.defaults.mode = "inline";
				$( ".state_edit" ).editable({
					source: [
						{value: 0, text: '<?php _e( 'Pending arrival', 'solidres' ) ?>' },
						{value: 1, text: '<?php _e( 'Checked-in', 'solidres' ) ?>' },
						{value: 2, text: '<?php _e( 'Checked-out', 'solidres' ) ?>' },
						{value: 3, text: '<?php _e( 'Closed', 'solidres' ) ?>' },
						{value: 4, text: '<?php _e( 'Canceled', 'solidres' ) ?>' },
						{value: 5, text: '<?php _e( 'Confirmed', 'solidres' ) ?>' },
						{value: -2, text: '<?php _e( 'Trashed', 'solidres' ) ?>' }
					],
					params: function (params) {
						params.action = 'solidres_edit_reservation_field';
						params.security = '<?php echo wp_create_nonce( 'edit-reservation' ) ?>';
						return params;
					},
					url: '<?php echo admin_url( 'admin-ajax.php' ) ?>'
				});
			});
		</script>
		<div class="alignleft actions bulkactions">
			<input type="text" name="filter_customer_fullname"
			       value="<?php if ( isset( $_GET['filter_customer_fullname'] ) ) {
				       echo $_GET['filter_customer_fullname'];
			       } ?>" placeholder="<?php _e( 'Search by customer name', 'solidres' ); ?>"/>
			<select name="filter_reservation_asset_id" id="srform_filter_dropdown">
				<option value=""><?php _e( 'Filter by assets', 'solidres' ); ?></option>
				<?php echo SR_Helper::render_list_asset( isset( $_GET['filter_reservation_asset_id'] ) ? $_GET['filter_reservation_asset_id'] : 0); ?>
			</select>
			<?php $filter_published = isset( $_GET['filter_published'] ) ? $_GET['filter_published'] : ''; ?>
			<select name="filter_published" id="srform_filter_dropdown">
				<option <?php echo $filter_published === '' ? 'selected' : '' ?> value=""><?php _e( 'Filter by status', 'solidres' ); ?></option>
				<option value="0" <?php echo $filter_published !== '' && $filter_published === '0' ? 'selected' : '' ?>><?php _e( 'Pending arrival', 'solidres' ); ?></option>
				<option value="1" <?php echo $filter_published !== '' && $filter_published === '1' ? 'selected' : '' ?>><?php _e( 'Checked-in', 'solidres' ); ?></option>
				<option value="2" <?php echo $filter_published !== '' && $filter_published === '2' ? 'selected' : '' ?>><?php _e( 'Checked-out', 'solidres' ); ?></option>
				<option value="3" <?php echo $filter_published !== '' && $filter_published === '3' ? 'selected' : '' ?>><?php _e( 'Closed', 'solidres' ); ?></option>
				<option value="4" <?php echo $filter_published !== '' && $filter_published === '4' ? 'selected' : '' ?>><?php _e( 'Canceled', 'solidres' ); ?></option>
				<option value="5" <?php echo $filter_published !== '' && $filter_published === '5' ? 'selected' : '' ?>><?php _e( 'Confirmed', 'solidres' ); ?></option>
				<option value="-2" <?php echo $filter_published !== '' && $filter_published === '-2' ? 'selected' : '' ?>><?php _e( 'Trashed', 'solidres' ); ?></option>
			</select>
			<select name="filter_payment_status" id="srform_filter_dropdown">
				<option value=""><?php _e( 'Filter by payment status', 'solidres' ); ?></option>
				<option
					value="0" <?php if ( isset( $_GET['filter_payment_status'] ) && $_GET['filter_payment_status'] == 0 && $_GET['filter_payment_status'] != null ) {
					echo 'selected';
				} ?>><?php _e( 'Unpaid', 'solidres' ); ?></option>
				<option
					value="1" <?php if ( isset( $_GET['filter_payment_status'] ) && $_GET['filter_payment_status'] == 1 ) {
					echo 'selected';
				} ?>><?php _e( 'Completed', 'solidres' ); ?></option>
				<option
					value="2" <?php if ( isset( $_GET['filter_payment_status'] ) && $_GET['filter_payment_status'] == 2 ) {
					echo 'selected';
				} ?>><?php _e( 'Cancelled', 'solidres' ); ?></option>
				<option
					value="3" <?php if ( isset( $_GET['filter_payment_status'] ) && $_GET['filter_payment_status'] == 3 ) {
					echo 'selected';
				} ?>><?php _e( 'Pending', 'solidres' ); ?></option>
			</select>

			<div class="checkin_group">
				<table>
					<tr>
						<td><label for="checkin_from"><?php _e( 'From', 'solidres' ); ?></label></td>
						<td><input type="text" name="filter_checkin_from"
						           value="<?php echo isset( $filter_checkin_from ) ? $filter_checkin_from : '' ?>"
						           id="filter_checkin_from" class="srform_datepicker filter_checkin_checkout"
						           placeholder="<?php _e( 'Check-in from', 'solidres' ); ?>"></td>
					</tr>
					<tr>
						<td><label for="checkin_to"><?php _e( 'To', 'solidres' ); ?></label></td>
						<td><input type="text" name="filter_checkin_to"
						           value="<?php if ( isset( $filter_checkin_to ) ) {
							           echo $filter_checkin_to;
						           } ?>" id="filter_checkin_to" class="srform_datepicker filter_checkin_checkout"
						           placeholder="<?php _e( 'Check-in to', 'solidres' ); ?>"></td>
					</tr>
				</table>
				<div class="clr"></div>
			</div>
			<div class="checkout_group">
				<table>
					<tr>
						<td><label for="checkout_from"><?php _e( 'From', 'solidres' ); ?></label></td>
						<td><input type="text" name="filter_checkout_from"
						           value="<?php echo isset( $filter_checkout_from ) ? $filter_checkout_from : '' ?>"
						           id="filter_checkout_from" class="srform_datepicker filter_checkin_checkout"
						           placeholder="<?php _e( 'Check-out from', 'solidres' ); ?>"></td>
					</tr>
					<tr>
						<td><label for="checkout_to"><?php _e( 'To', 'solidres' ); ?></label></td>
						<td><input type="text" name="filter_checkout_to"
						           value="<?php echo isset( $filter_checkout_to ) ? $filter_checkout_to : '' ?>"
						           id="filter_checkout_to" class="srform_datepicker filter_checkin_checkout"
						           placeholder="<?php _e( 'Check-out to', 'solidres' ); ?>"></td>
					</tr>
				</table>
				<div class="clr"></div>
			</div>
			<?php submit_button( __( 'Filter', 'solidres' ), 'button', 'filter_reservation', false ); ?>
		</div>
	<?php }
}

function sr_reservations() {
	global $status, $list_table_data, $string_search;
	$reservations    = new SR_Reservation();
	$list_table_data = new SR_Reservations_Table_Data();
	if ( ! empty( $_REQUEST['_wp_http_referer'] ) ) {
		wp_redirect( remove_query_arg( array(
			'_wp_http_referer',
			'_wpnonce'
		), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
		exit;
	}
	$list_table_data->prepare_items();
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	$reservations->listview( $action, $string_search, $status, $list_table_data );
}
