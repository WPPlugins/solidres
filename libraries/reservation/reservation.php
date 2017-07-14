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

/**
 * Reservation handler class
 * @package    Solidres
 * @subpackage    Reservation
 */
class SR_Reservation {
	/**
	 * The database object
	 * @var object
	 */
	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * Update states for listview
	 *
	 * @param $action
	 * @param $reservation_id
	 * @param $ids
	 */
	public function update_states( $action, $reservation_id, $ids ) {
		$states = array(
			'trash'   => array( 'state' => - 2, 'action' => 'moved', 'title' => 'Trash' ),
			'untrash' => array( 'state' => 0, 'action' => 'restored', 'title' => 'Trash' ),
		);

		if ( isset( $action ) && array_key_exists( $action, $states ) && isset( $reservation_id ) && $reservation_id != null ) {
			foreach ( $ids as $id ) {
				$this->wpdb->update( $this->wpdb->prefix . 'sr_reservations', array( 'state' => $states[ $action ]['state'] ), array( 'id' => $id ) );
			}
			if ( count( $ids ) == 1 ) {
				$message = __( '1 reservation ' . $states[ $action ]['action'] . ' to the ' . $states[ $action ]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			} else {
				$message = __( count( $ids ) . ' reservations ' . $states[ $action ]['action'] . ' to the ' . $states[ $action ]['title'], 'solidres' );
				SR_Helper::show_message( $message );
			}
		}
	}

	/**
	 * Delete permanently action
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete( $id ) {
		$reservation_room_xref_ids = $this->wpdb->get_results( "SELECT id FROM {$this->wpdb->prefix}sr_reservation_room_xref WHERE reservation_id = $id" );
		foreach ( $reservation_room_xref_ids as $reservation_room_xref_id ) {
			$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_room_details', array( 'reservation_room_id' => $reservation_room_xref_id->id ) );
		}
		$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_room_xref', array( 'reservation_id' => $id ) );
		$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_room_extra_xref', array( 'reservation_id' => $id ) );
		$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_notes', array( 'reservation_id' => $id ) );
		$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_extra_xref', array( 'reservation_id' => $id ) );
		if (defined('SR_PLUGIN_INVOICE_ENABLED') && SR_PLUGIN_INVOICE_ENABLED) {
			$this->wpdb->delete( $this->wpdb->prefix . 'sr_invoices' , array( 'reservation_id' => $id ) );
		}

		if (defined('SR_PLUGIN_FEEDBACK_ENABLED') && SR_PLUGIN_FEEDBACK_ENABLED) {
			$feedback_id = (int)$this->wpdb->get_var( 'SELECT id FROM '.$this->wpdb->prefix.'sr_feedbacks WHERE reservation_id = '.(int)$id);
			if($feedback_id > 0){
				$this->wpdb->delete( $this->wpdb->prefix . 'sr_feedback_attribute_xref', array('feedback_id' => $feedback_id));
				$this->wpdb->delete( $this->wpdb->prefix . 'sr_feedbacks', array('reservation_id' => (int)$id));
			}
		}

		$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservations', array( 'id' => $id ) );
	}

	/**
	 * View status and code style item of listview
	 *
	 * @param $state
	 * @param $code
	 *
	 * @return array
	 */
	public static function view_status( $state, $code ) {
		switch ( $state ) {
			case 0:
				$reservationstatus = __( 'Pending arrival', 'solidres' );
				$codename          = '<span class="pending_code">' . $code . '</span>';
				break;
			case 1:
				$reservationstatus = __( 'Checked-in', 'solidres' );
				$codename          = '<span class="checkin_code">' . $code . '</span>';
				break;
			case 2:
				$reservationstatus = __( 'Checked-out', 'solidres' );
				$codename          = '<span class="checkout_code">' . $code . '</span>';
				break;
			case 3:
				$reservationstatus = __( 'Closed', 'solidres' );
				$codename          = '<span class="closed_code">' . $code . '</span>';
				break;
			case 4:
				$reservationstatus = __( 'Canceled', 'solidres' );
				$codename          = '<span class="canceled_code">' . $code . '</span>';
				break;
			case 5:
				$reservationstatus = __( 'Confirmed', 'solidres' );
				$codename          = '<span class="confirmed_code">' . $code . '</span>';
				break;
			case - 2:
				$reservationstatus = __( 'Trashed', 'solidres' );
				$codename          = '<span class="trashed_code">' . $code . '</span>';
				break;
		}

		return array( $reservationstatus, $codename );
	}

	/**
	 * View payment status
	 *
	 * @param $payment_status
	 *
	 * @return string|void
	 */
	public static function payment_status( $payment_status ) {
		switch ( $payment_status ) {
			case 0:
				$paymentstatus = __( 'Unpaid', 'solidres' );
				break;
			case 1:
				$paymentstatus = __( 'Completed', 'solidres' );
				break;
			case 2:
				$paymentstatus = __( 'Cancelled', 'solidres' );
				break;
			case 3:
				$paymentstatus = __( 'Pending', 'solidres' );
				break;
		}

		return $paymentstatus;
	}

	/**
	 * View ListView
	 *
	 * @param $action
	 * @param $string_search
	 * @param $status
	 * @param $list_table_data
	 */
	public function listview( $action, $string_search, $status, $list_table_data ) {
		$current_user = wp_get_current_user();
		$author_id    = $current_user->ID;
		if ( current_user_can( 'solidres_partner' ) ) {
			$query_default = "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_reservations r LEFT JOIN {$this->wpdb->prefix}sr_reservation_assets ra ON r.reservation_asset_id = ra.id WHERE ra.partner_id = " . $author_id . " AND r.state";
			$count_publish = $this->wpdb->get_var( $query_default . ' != -2' );
			$count_trash   = $this->wpdb->get_var( $query_default . ' = -2' );
			$page          = 'sr-hub-reservations';
		} else {
			$query_default = "SELECT COUNT(*) FROM {$this->wpdb->prefix}sr_reservations WHERE state";
			$count_publish = $this->wpdb->get_var( $query_default . ' != -2' );
			$count_trash   = $this->wpdb->get_var( $query_default . ' = -2' );
			$page          = 'sr-reservations';
		}
		if ( $action != 'edit' && $action != 'amend' ) { ?>
			<div class="srtable">
				<div class="wrap">
					<div id="icon-users" class="icon32"><br/></div>
					<h2><?php _e( 'Reservations', 'solidres' ); ?>
						<a href="<?php echo admin_url('admin.php?page=sr-reservations&action=amend'); ?>"
						   class="add-new-h2"><?php _e('Add New', 'solidres'); ?></a>
						<?php if ( $string_search != '' ) { ?>
							<span
								class="subtitle"><?php printf( __( 'Search results for "%s"', 'solidres' ), $string_search ); ?></span>
						<?php } ?>
					</h2>
					<ul class="subsubsub">
						<li class="publish">
							<a href="<?php echo admin_url( 'admin.php?page=' . $page ); ?>" <?php echo $status == '' ? 'class="current"' : ''; ?>>
								<?php _e( 'Publish', 'solidres' ); ?>
								<span class="count">(<?php if ( $count_publish > 0 ) {
										echo $count_publish;
									} else {
										echo '0';
									} ?>)</span>
							</a>
						</li>
						<?php if ( $count_trash > 0 ) { ?>
							|
							<li class="trash">
								<a href="<?php echo admin_url( 'admin.php?page=' . $page . '&status=trash' ); ?>" <?php echo $status == 'trash' ? 'class="current"' : ''; ?>>
									<?php _e( 'Trash', 'solidres' ); ?>
									<span class="count">(<?php echo $count_trash; ?>)</span>
								</a>
							</li>
						<?php } ?>
					</ul>
					<form id="plugins-filter" method="get" action="<?php echo admin_url( 'admin.php?page=' . $page ); ?>">
						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
						<?php
						$list_table_data->search_box( __( 'Search', 'solidres' ), 'search_reservations' );
						$list_table_data->display();
						?>
					</form>
				</div>
			</div>
		<?php }
	}


	/**
	 * Get a single reservation by id
	 *
	 * @param $id
	 * @param $output
	 * @param $userid
	 *
	 * @return mixed
	 */
	public function load( $id, $output = OBJECT, $userid = null ) {
		$by_user = '';
		if ( current_user_can( 'solidres_partner' ) ) {
			$user      = wp_get_current_user();
			$userid = $user->ID;
			$result = $this->wpdb->get_row( $this->wpdb->prepare(
			"SELECT r.* FROM {$this->wpdb->prefix}sr_reservations as r
			LEFT JOIN {$this->wpdb->prefix}sr_reservation_assets as ra ON r.reservation_asset_id = ra.id AND ra.partner_id = %d
			WHERE r.id = %d ", $userid, $id ), $output );
		} else {
			if ( $userid != null ) {
				$by_user = ' AND customer_id = ' . $userid;
			}
			$result = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservations WHERE id = %d" . $by_user, $id ), $output );
		}

		return $result;
	}

	public function load_reserved_rooms( $reservation_id ) {
		$reserved_room_details = $this->wpdb->get_results( $this->wpdb->prepare(
			"
			SELECT x.*, rtype.id as room_type_id, rtype.name as room_type_name, room.label as room_label
			FROM {$this->wpdb->prefix}sr_reservation_room_xref as x
			INNER JOIN {$this->wpdb->prefix}sr_rooms as room ON room.id = x.room_id
			INNER JOIN {$this->wpdb->prefix}sr_room_types as rtype ON rtype.id = room.room_type_id
			WHERE reservation_id = %d
			", $reservation_id ) );

		foreach ( $reserved_room_details as $reserved_room_detail ) {
			$result = $this->wpdb->get_results( $this->wpdb->prepare(
				"
				SELECT x.*, extra.id as extra_id, extra.name as extra_name
				FROM {$this->wpdb->prefix}sr_reservation_room_extra_xref as x
				INNER JOIN {$this->wpdb->prefix}sr_extras as extra ON extra.id = x.extra_id
				WHERE reservation_id = %d AND room_id = %d
				", $reservation_id, $reserved_room_detail->room_id ) );

			if ( ! empty( $result ) ) {
				$reserved_room_detail->extras = $result;
			}

			$result = $this->wpdb->get_results( $this->wpdb->prepare(
				"
				SELECT * FROM {$this->wpdb->prefix}sr_reservation_room_details
				WHERE reservation_room_id = %d
				", $reserved_room_detail->id
			) );

			$reserved_room_detail->other_info = array();
			if ( ! empty( $result ) ) {
				$reserved_room_detail->other_info = $result;
			}
		}

		return $reserved_room_details;
	}

	public function load_reserved_extras( $reservation_id ) {
		return $this->wpdb->get_results( $this->wpdb->prepare(
			"SELECT * FROM {$this->wpdb->prefix}sr_reservation_extra_xref WHERE reservation_id = %d", $reservation_id
		) );
	}

	/**
	 * Generate unique string for Reservation
	 *
	 * @param string $srcString The string that need to be calculate checksum
	 *
	 * @return string The unique string for each Reservation
	 */
	public function get_code( $srcString ) {
		return hash( 'crc32', (string) $srcString . uniqid() );
	}

	/**
	 * Check a room to see if it is allowed to be booked in the period from $checkin -> $checkout
	 *
	 * @param int       $roomId
	 * @param string    $checkin
	 * @param string    $checkout
	 * @param int       $bookingType 0 is booking per night and 1 is booking per day
	 *
	 * @return boolean  True if the room is ready to be booked, False otherwise
	 */
	public function is_room_available( $roomId = 0, $checkin, $checkout, $bookingType = 0 ) {
		// By default, a room is occupied only with status = "Checked In" or "Confirmed"
		// Full list of reservation status
		// 0 : Pending arrival
		// 1 : Checked in
		// 2 : Checkout out
		// 3 : Closed
		// 4 : Cancelled
		// 5 : Confirmed
		$result = $this->wpdb->get_results( "SELECT checkin, checkout 
			FROM {$this->wpdb->prefix}sr_reservations as res 
			INNER JOIN {$this->wpdb->prefix}sr_reservation_room_xref as room ON res.id = room.reservation_id AND room.room_id = $roomId 
			WHERE res.checkout >= '" . date( 'Y-m-d' ) . "' 
			AND res.state = 1 or res.state = 5 
			ORDER BY res.checkin" );

		if ( is_array( $result ) ) {
			foreach ( $result as $currentReservation ) {
				$currentCheckin  = $currentReservation->checkin;
				$currentCheckout = $currentReservation->checkout;

				if ($checkin != $checkout) { // Normal check
					if (0 == $bookingType) { // Per night
						if (
							( $checkin <= $currentCheckin && $checkout > $currentCheckin ) ||
							( $checkin >= $currentCheckin && $checkout <= $currentCheckout ) ||
							( $checkin < $currentCheckout && $checkout >= $currentCheckout )
						) {
							return false;
						}
					} else { // Per day
						if (
							( $checkin <= $currentCheckin && $checkout >= $currentCheckin ) ||
							( $checkin >= $currentCheckin && $checkout <= $currentCheckout ) ||
							( $checkin <= $currentCheckout && $checkout >= $currentCheckout )
						) {
							return false;
						}
					}
				} else {  // Edge case when we check for available of a single date (checkin == checkout)
					if (0 == $bookingType) { // Per night
						if (
							($checkin <= $currentCheckin && $checkout > $currentCheckin) ||
							($checkin >= $currentCheckin && $checkout < $currentCheckout) ||
							($checkin < $currentCheckout && $checkout >= $currentCheckout)
						) {
							return false;
						}
					} else {
						if (
							($checkin <= $currentCheckin && $checkout > $currentCheckin) ||
							($checkin >= $currentCheckin && $checkout < $currentCheckout) ||
							($checkin <= $currentCheckout && $checkout >= $currentCheckout)
						) {
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Store reservation data and related data like children ages or other room preferences
	 *
	 * @param   int $reservationId
	 * @param   array $room Room information
	 *
	 * @return void
	 */
	public function storeRoom( $reservationId, $room ) {
		$sr_room = array(
			'reservation_id'      => (int) $reservationId,
			'room_id'             => (int) $room['room_id'],
			'room_label'          => $room['room_label'],
			'adults_number'       => isset( $room['adults_number'] ) ? (int) $room['adults_number'] : 0,
			'children_number'     => isset( $room['children_number'] ) ? (int) $room['children_number'] : 0,
			'guest_fullname'      => isset( $room['guest_fullname'] )? $room['guest_fullname'] : '',
			'room_price'          => isset( $room['room_price']) ? $room['room_price'] : 0,
			'room_price_tax_incl' => isset( $room['room_price_tax_incl']) ? $room['room_price_tax_incl'] : 0,
			'room_price_tax_excl' => isset( $room['room_price_tax_excl'] ) ? $room['room_price_tax_excl'] : 0,
			);

		$format = array(
			'%d',
			'%d',
			'%s',
			'%d',
			'%d',
			'%s',
			'%f',
			'%f',
			'%f'
		);
		if (isset($room['tariff_id']) && !is_null($room['tariff_id'])) {
			$format = array_merge( $format, array(
				'%d',
				'%s',
				'%s'
			) );
			$sr_room['tariff_id']           = isset( $room['tariff_id'] ) ? $room['tariff_id'] : 0;
			$sr_room['tariff_title']        = isset( $room['tariff_title'] ) ? $room['tariff_title'] : '';
			$sr_room['tariff_description']  = isset( $room['tariff_description'] ) ? $room['tariff_description'] : '';
		}
		$result1 = $this->wpdb->insert( $this->wpdb->prefix . 'sr_reservation_room_xref', $sr_room, $format);

		$recentInsertedId = $this->wpdb->insert_id;
		if ( isset ( $room['children_number'] ) ) {
			for ( $i = 0; $i < $room['children_number']; $i ++ ) {
				$result2 = $this->wpdb->insert( $this->wpdb->prefix . 'sr_reservation_room_details', array(
					'reservation_room_id' => (int) $recentInsertedId,
					'key'                 => 'child' . ( $i + 1 ),
					'value'               => $room['children_ages'][ $i ]
				) );
			}
		}

		if ( isset( $room['preferences'] ) ) {
			foreach ( $room['preferences'] as $key => $value ) {
				$result3 = $this->wpdb->insert( $this->wpdb->prefix . 'sr_reservation_room_details', array(
					'reservation_room_id' => (int) $recentInsertedId,
					'key'                 => $key,
					'value'               => $value
				) );
			}
		}
	}

	/**
	 * Store extra information
	 *
	 * @param  int $reservationId
	 * @param  int $roomId
	 * @param  string $roomLabel
	 * @param  int $extraId
	 * @param  string $extraName
	 * @param  int $extraQuantity The extra quantity or NULL if extra does not have quantity
	 * @param  int $price
	 *
	 * @return void
	 */
	public function storeExtra( $reservationId, $roomId, $roomLabel, $extraId, $extraName, $extraQuantity = null, $price = 0 ) {
		$this->wpdb->insert( $this->wpdb->prefix . 'sr_reservation_room_extra_xref', array(
			'reservation_id' => $reservationId,
			'room_id'        => $roomId,
			'room_label'     => $roomLabel,
			'extra_id'       => $extraId,
			'extra_name'     => $extraName,
			'extra_quantity' => ( $extraQuantity === null ? null : $extraQuantity ),
			'extra_price'    => $price
		) );
	}

	/**
	 * Check for the validity of check in and check out date
	 * Conditions
	 * + Number of days a booking must be made in advance
	 * + Maximum length of stay
	 *
	 * @param string $checkIn
	 * @param string $checkOut
	 * @param array $conditions
	 *
	 * @throws Exception
	 * @return Boolean
	 */
	public function isCheckInCheckOutValid( $checkIn, $checkOut, $conditions ) {

		$checkIn  = new DateTime( $checkIn );
		$checkOut = new DateTime( $checkOut );
		$today    = new DateTime( date( 'Y-m-d' ) );

		if ( $checkOut <= $checkIn ) {
			throw new Exception( 'Invalid. Check out date must be after check in date.', 50001 );
		}

		// Interval between check in and check out date
		$interval1 = $checkOut->diff( $checkIn )->format( '%a' );

		if ( $conditions['min_length_of_stay'] > 0 ) {
			if ( $interval1 < $conditions['min_length_of_stay'] ) // count nights, not days
			{
				throw new Exception( 'Invalid. Minimum length of stay is %d nights.', 50002 );
			}
		}

		// Interval between checkin and today
		$interval2 = $checkIn->diff( $today )->format( '%a' );

		if ( $conditions['min_days_book_in_advance'] > 0 ) {
			if ( $interval2 < $conditions['min_days_book_in_advance'] ) {
				throw new Exception( 'Invalid. You have to book at least %d days in advance of your arrival.', 50003 );
			}
		}

		if ( $conditions['max_days_book_in_advance'] > 0 ) {
			if ( $interval2 > $conditions['max_days_book_in_advance'] ) {
				throw new Exception( 'Invalid. You are not allowed to book more than %d days in advance of your arrival.', 50004 );
			}
		}

		return true;
	}

	public function process_room( $data ) {
		// Get the extra price to display in the confirmmation screen
		$solidres_extra                      = new SR_Extra();
		$total_room_type_extra_cost_tax_excl = 0;
		$total_room_type_extra_cost_tax_incl = 0;
		$total_adults = 0;
		$total_children = 0;
		$stayLength = SR_Utilities::calculate_date_diff(solidres()->session->get( 'sr_checkin' ), solidres()->session->get( 'sr_checkout') );

		foreach ( $data['room_types'] as $room_type_id => &$booked_tariffs ) {
			foreach ( $booked_tariffs as $tariffId => &$rooms ) {
				foreach ( $rooms as &$room ) {
					$total_adults    += isset($room['adults_number']) ? $room['adults_number'] : 0;
					$total_children  += !empty($room['children_number']) ? $room['children_number'] : 0;

					if ( isset( $room['extras'] ) ) {
						foreach ( $room['extras'] as $extra_id => &$extra_details ) {
							$extra                                 = $solidres_extra->load( $extra_id );
							$extra_details['price']                = $extra->price;
							$extra_details['price_tax_incl']       = $extra->price_tax_incl;
							$extra_details['price_tax_excl']       = $extra->price_tax_excl;
							$extra_details['price_adult']          = $extra->price_adult;
							$extra_details['price_adult_tax_incl'] = $extra->price_adult_tax_incl;
							$extra_details['price_adult_tax_excl'] = $extra->price_adult_tax_excl;
							$extra_details['price_child']          = $extra->price_child;
							$extra_details['price_child_tax_incl'] = $extra->price_child_tax_incl;
							$extra_details['price_child_tax_excl'] = $extra->price_child_tax_excl;
							$extra_details['name']                 = $extra->name;
							$extra_details['charge_type']          = $extra->charge_type;
							$extra_details['adults_number']        = isset( $room['adults_number'] ) ? $room['adults_number'] : 0;
							$extra_details['children_number']      = isset( $room['children_number'] ) ? $room['children_number'] : 0;
							$extra_details['stay_length']          = $stayLength;
							$extra_details['booking_type']         = solidres()->session->get( 'sr_booking_type' );

							$solidres_extra = new SR_Extra($extra_details);
							$costs = $solidres_extra->calculate_extra_cost();

							$total_room_type_extra_cost_tax_incl += $costs['total_extra_cost_tax_incl'];
							$total_room_type_extra_cost_tax_excl += $costs['total_extra_cost_tax_excl'];

							$extra_details['total_extra_cost_tax_incl'] = $costs['total_extra_cost_tax_incl'];
							$extra_details['total_extra_cost_tax_excl'] = $costs['total_extra_cost_tax_excl'];
						}
					}
				}
			}
		}

		// manually unset those referenced instances
		unset( $rooms );
		unset( $room );
		unset( $extra_details );

		$data['total_extra_price_per_room']          = $total_room_type_extra_cost_tax_incl;
		$data['total_extra_price_tax_incl_per_room'] = $total_room_type_extra_cost_tax_incl;
		$data['total_extra_price_tax_excl_per_room'] = $total_room_type_extra_cost_tax_excl;

		solidres()->session->set( 'sr_room', $data );
		solidres()->session->set( 'sr_total_adults', $total_adults );
		solidres()->session->set( 'sr_total_children', $total_children );
		solidres()->session->set( 'sr_booking_conditions', $data['bookingconditions'] );
		solidres()->session->set( 'sr_privacy_policy', $data['privacypolicy'] );

		// Store all selected tariffs
		solidres()->session->set( 'sr_current_selected_tariffs', $data['selected_tariffs'] );

		// If error happened, output correct error message in json format so that we can handle in the front end
		$response = array( 'status' => 1, 'message' => '', 'next_step' => $data['next_step'] );

		wp_send_json( $response );
	}

	/**
	 * Process submitted guest information: guest personal information and their payment method
	 *
	 * @param $data
	 *
	 * @return json
	 */
	public function process_guest_info( $data ) {
		$solidres_country              = new SR_Country();
		$solidres_state                = new SR_State();
		$solidres_extra                = new SR_Extra();
		$country                       = $solidres_country->load( $data['customer_country_id'] );
		$totalPerBookingExtraCostTaxIncl = 0;
		$totalPerBookingExtraCostTaxExcl = 0;
		$stayLength = SR_Utilities::calculate_date_diff(solidres()->session->get( 'sr_checkin' ), solidres()->session->get( 'sr_checkout' ));

		// Query country and geo state name
		if ( ! empty( $data['customer_geo_state_id'] ) ) {
			$geoState               = $solidres_state->load( $data['customer_geo_state_id'] );
			$data['geo_state_name'] = $geoState->name;
		}
		$data['country_name'] = $country->name;

		// Process customer group
		$customerId          = null;
		if ( defined( 'SR_PLUGIN_USER_ENABLED' ) && SR_PLUGIN_USER_ENABLED) {
			//$user = JFactory::getUser();
			$current_user = wp_get_current_user();
			if ($current_user->ID > 0) {
				$customerId = $current_user->ID;
			}
		}
		$data['customer_id'] = $customerId;

		// Process extra (Per booking)
		if ( isset( $data['extras'] ) ) {
			foreach ( $data['extras'] as $extraId => &$extra_details ) {
				//$extra                          = $extraModel->getItem( $extraId );
				$extra                          = $solidres_extra->load( $extraId );
				$extra_details['price']          = $extra->price;
				$extra_details['price_tax_incl'] = $extra->price_tax_incl;
				$extra_details['price_tax_excl'] = $extra->price_tax_excl;
				$extra_details['price_adult'] = $extra->price_adult;
				$extra_details['price_adult_tax_incl'] = $extra->price_adult_tax_incl;
				$extra_details['price_adult_tax_excl'] = $extra->price_adult_tax_excl;
				$extra_details['price_child'] = $extra->price_child;
				$extra_details['price_child_tax_incl'] = $extra->price_child_tax_incl;
				$extra_details['price_child_tax_excl'] = $extra->price_child_tax_excl;
				$extra_details['name']           = $extra->name;
				$extra_details['charge_type'] = $extra->charge_type;
				$extra_details['adults_number'] = solidres()->session->get( 'sr_total_adults' ) ;
				$extra_details['children_number'] = solidres()->session->get( 'sr_total_children' ) ;
				$extra_details['stay_length'] = $stayLength;
				$extra_details['booking_type'] = solidres()->session->get( 'sr_booking_type' );

				$solidres_extra = new SR_Extra($extra_details);

				$costs = $solidres_extra->calculate_extra_cost();

				$totalPerBookingExtraCostTaxIncl += $costs['total_extra_cost_tax_incl'];
				$totalPerBookingExtraCostTaxExcl += $costs['total_extra_cost_tax_excl'];

				$extra_details['total_extra_cost_tax_incl'] = $costs['total_extra_cost_tax_incl'];
				$extra_details['total_extra_cost_tax_excl'] = $costs['total_extra_cost_tax_excl'];
			}
		}

		$data['total_extra_price_per_booking']          = $totalPerBookingExtraCostTaxIncl;
		$data['total_extra_price_tax_incl_per_booking'] = $totalPerBookingExtraCostTaxIncl;
		$data['total_extra_price_tax_excl_per_booking'] = $totalPerBookingExtraCostTaxExcl;

		// Bind them to session
		solidres()->session->set( 'sr_guest', $data );

		// If error happened, output correct error message in json format so that we can handle in the front end
		$response = array( 'status' => 1, 'message' => '', 'next_step' => $data['next_step'] );

		wp_send_json( $response );
	}

	/**
	 * Return html to display guest info form in one-page reservation, data is retrieved from user session
	 *
	 * @return string $html The HTML output
	 */
	public function get_html_guest_info() {
		$options = get_option( 'solidres_plugin' );
		$reservation_id = solidres()->session->get( 'sr_id' );
		$currentReservationData = NULL;
		$is_guest_making_reservation = solidres()->session->get( 'sr_is_guest_making_reservation' );

		$guestFields = array(
			'customer_firstname',
			'customer_middlename',
			'customer_lastname',
			'customer_vat_number',
			'customer_company',
			'customer_phonenumber',
			'customer_mobilephone',
			'customer_address1',
			'customer_address2',
			'customer_city',
			'customer_zipcode',
			'customer_country_id',
			'customer_geo_state_id',
		);

		$show_price_with_tax = $options['show_price_with_tax'];
		$customer_titles           = array(
			''                       => '',
			__( 'Mr.', 'solidres' )  => __( 'Mr.', 'solidres' ),
			__( 'Mrs.', 'solidres' ) => __( 'Mrs.', 'solidres' ),
			__( 'Ms.', 'solidres' )  => __( 'Ms.', 'solidres' )
		);

		if ( $reservation_id > 0 ) {
			$guestFields[] = 'customer_title';
			$guestFields[] = 'customer_email';
			$guestFields[] = 'customer_vat_number';
			$guestFields[] = 'payment_method_id';
			$solidres_reservation = new SR_Reservation();
			$currentReservationData = $solidres_reservation->load( $reservation_id );
			$reservation_details_guest = null;
			foreach ( $guestFields as $guestField ) {
				if ( !isset( $guest_data[ $guestField ] ) ) {
					$reservation_details_guest[ $guestField ] = $currentReservationData->{$guestField};
				}
			}

			$asset_id = $currentReservationData->reservation_asset_id;
			$current_reserved_extras = $this->wpdb->get_results( 'SELECT extra_id, extra_quantity FROM ' . $this->wpdb->prefix . 'sr_reservation_extra_xref WHERE reservation_id = ' . (int) $reservation_id );
			foreach ( $current_reserved_extras as $reserved_extra ) {
				$reservation_details_guest[ 'extras' ][ $reserved_extra->extra_id ][ 'quantity'] = $reserved_extra->extra_quantity;
			}
		} else {
			$reservation_details_room  = solidres()->session->get( 'sr_room' );
			$reservation_details_guest = solidres()->session->get( 'sr_guest' );
			$asset_id = $reservation_details_room['raid'];
		}

		$solidres_extra = new SR_Extra();
		$extras         = $solidres_extra->load_by_reservation_asset_id( $asset_id, 1, $show_price_with_tax, array(1,2,3) );

		// Try to get the customer information if he/she logged in
		$selected_country_id = 0;
		if (isset($reservation_details_guest['customer_country_id']) && $reservation_details_guest['customer_country_id'] > 0) {
			$selected_country_id = $reservation_details_guest['customer_country_id'];
		}

		if ( defined( 'SR_PLUGIN_USER_ENABLED' ) && SR_PLUGIN_USER_ENABLED && is_user_logged_in() && $is_guest_making_reservation) {
			$current_user = wp_get_current_user();

			if ( ! empty( $current_user->ID ) && $current_user->ID > 0 ) {
				foreach ( $guestFields as $guestField ) {
					if ( ! isset( $reservation_details_guest[ $guestField ] ) ) {
						$reservation_details_guest[ $guestField ] = get_user_meta( $current_user->ID, substr( $guestField, 9 ), true );
					}
				}

				$reservation_details_guest["customer_email"] = ! isset( $reservation_details_guest["customer_email"] ) ? $current_user->user_email : $reservation_details_guest["customer_email"];
			}

			if (get_user_meta( $current_user->ID, 'country_id', true ) > 0) {
				$selected_country_id = get_user_meta( $current_user->ID, 'country_id', true );
			}
		}
		$countries  = SR_Helper::render_list_country( $selected_country_id );
		$geo_states = $selected_country_id > 0 ? SR_Helper::render_list_geo_state( $selected_country_id, $reservation_details_guest['customer_geo_state_id'] ) : '';
		$solidres_payment_plugins = solidres()->payment_gateways();

		$display_data = array(
			'customer_titles'           => $customer_titles,
			'reservation_details_guest' => $reservation_details_guest,
			'extras'                    => $extras,
			'assetId'                   => $asset_id,
			'countries'                 => $countries,
			'geo_states'                => $geo_states,
			'solidresPaymentPlugins'    => $solidres_payment_plugins,
			'is_front_end'              => solidres()->is_request( 'frontend' ),
		);

		$html = '';
		$path = WP_PLUGIN_DIR . '/solidres/templates/reservation/guestform.php';

		if ( file_exists( $path ) ) {
			ob_start();
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}

		echo $html;
	}

	/**
	 * Return html to display confirmation form in one-page reservation, data is retrieved from user session
	 *
	 * @return string $html The HTML output
	 */
	public function get_html_confirmation() {
		$reservation_details_room  = solidres()->session->get( 'sr_room' );
		$reservation_details_guest = solidres()->session->get( 'sr_guest' );
		$solidres_room_type        = new SR_Room_Type();

		$solidresConfig                = get_option( 'solidres_plugin' );
		$checkin                       = solidres()->session->get( 'sr_checkin' );
		$checkout                      = solidres()->session->get( 'sr_checkout' );
		$raId                          = $reservation_details_room['raid'];
		$currency                      = new SR_Currency( 0, solidres()->session->get( 'sr_currency_id' ) );
		$totalRoomTypeExtraCostTaxIncl = $reservation_details_room['total_extra_price_tax_incl_per_room'] + $reservation_details_guest['total_extra_price_tax_incl_per_booking'];
		$totalRoomTypeExtraCostTaxExcl = $reservation_details_room['total_extra_price_tax_excl_per_room'] + $reservation_details_guest['total_extra_price_tax_excl_per_booking'];
		$stay_length                   = SR_Utilities::calculate_date_diff( $checkin, $checkout );
		$dateFormat                    = get_option( 'date_format', 'd-m-Y' );
		$jsDateFormat                  = SR_Utilities::convert_date_format_pattern( $dateFormat );
		$tzoffset                      = get_option( 'timezone_string' );
		$tzoffset                      = $tzoffset == '' ? 'UTC' : $tzoffset;
		$timezone                      = new DateTimeZone( $tzoffset );
		$isDiscountPreTax              = isset($solidresConfig[ 'discount_pre_tax' ]) ? $solidresConfig[ 'discount_pre_tax' ] : 0;

		// Query for room types data and their associated costs
		$booked_room_types = $reservation_details_room['room_types'];
		$roomTypes         = $solidres_room_type->get_room_type( $raId, $booked_room_types, $checkin, $checkout );

		// Rebind the session data because it has been changed in the previous line
		$reservation_details_room  = solidres()->session->get( 'sr_room' );
		$reservation_details_guest = solidres()->session->get( 'sr_guest' );
		$cost                      = solidres()->session->get( 'sr_cost' );

		$display_data = array(
			'roomTypes'                     => $roomTypes,
			'reservation_details_room'      => $reservation_details_room,
			'reservation_details_guest'     => $reservation_details_guest,
			'totalRoomTypeExtraCostTaxIncl' => $totalRoomTypeExtraCostTaxIncl,
			'totalRoomTypeExtraCostTaxExcl' => $totalRoomTypeExtraCostTaxExcl,
			'assetId'                       => $raId,
			'cost'                          => $cost,
			'stay_length'                   => $stay_length,
			'currency'                      => $currency,
			'dateFormat'                    => $dateFormat, // default format d-m-y
			'jsDateFormat'                  => $jsDateFormat,
			'timezone'                      => $timezone,
			'checkin'                       => $checkin,
			'checkout'                      => $checkout,
			'currency_id'                   => solidres()->session->get( 'sr_currency_id' ),
			'tax_id'                        => solidres()->session->get( 'sr_tax_id' ),
			'deposit_required'              => solidres()->session->get( 'sr_deposit_required' ),
			'deposit_is_percentage'         => solidres()->session->get( 'sr_deposit_is_percentage' ),
			'deposit_include_extra_cost'    => solidres()->session->get( 'sr_deposit_include_extra_cost' ),
			'deposit_amount'                => solidres()->session->get( 'sr_deposit_amount' ),
			'asset_params'                  => solidres()->session->get( 'sr_asset_params' ),
			'page_id'                       => solidres()->session->get( 'sr_wp_page_id' ),
			'isDiscountPreTax'              => $isDiscountPreTax,
			'booking_type'                  => solidres()->session->get( 'sr_booking_type' )
		);

		$html = '';
		$path = WP_PLUGIN_DIR . '/solidres/templates/reservation/confirmationform.php';

		if ( file_exists( $path ) ) {
			ob_start();
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}

		echo $html;
	}

	public function save( $data ) {
		$room_type_data = isset( $data['room_types']) ? $data['room_types'] : '';
		$extra_data     = isset( $data['extras'] ) ? $data['extras'] : array();
		$is_new         = empty( $data['id'] );
		$context = 'solidres.edit.reservation.data';

		$reservation_fields = array();
		$format = array();

		if (isset($data['id']) && $data['id'] > 0) {
			$reservation_fields['id'] = $data['id'];
			$format[] = '%d'; // id
		}

		$reservation_fields = array_merge($reservation_fields, array(
			'state' => 0,
			'customer_id' => NULL,
			'created_date' => '0000-00-00 00:00:00',
			'modified_date' => '0000-00-00 00:00:00',
			'modified_by' => 0,
			'created_by' => 0,
			'payment_method_id' => 0,
			'payment_method_txn_id' => NULL,
			'payment_status' => 0,
			'payment_data' => NULL,
			'code' => '',
			'coupon_id' => NULL,
			'coupon_code' => NULL,
			'customer_title' => NULL,
			'customer_firstname' => NULL,
			'customer_middlename' => NULL,
			'customer_lastname' => NULL,
			'customer_email' => NULL,
			'customer_phonenumber' => NULL,
			'customer_mobilephone' => NULL,
			'customer_company' => NULL,
			'customer_address1' => NULL,
			'customer_address2' => NULL,
			'customer_city' => NULL,
			'customer_zipcode' => NULL,
			'customer_country_id' => NULL,
			'customer_geo_state_id' => NULL,
			'customer_vat_number' => NULL,
			'checkin' => '',
			'checkout' => '',
			'invoice_number' => NULL,
			'currency_id' => NULL,
			'currency_code' => NULL,
			'total_price' => NULL,
			'total_price_tax_incl' => NULL,
			'total_price_tax_excl' => NULL,
			'total_extra_price' => NULL,
			'total_extra_price_tax_incl' => NULL,
			'total_extra_price_tax_excl' => NULL,
			'total_discount' => NULL,
			'note' => NULL,
			'reservation_asset_id' => NULL,
			'reservation_asset_name' => NULL,
			'deposit_amount' => NULL,
			'total_paid' => NULL,
			'discount_pre_tax' => NULL,
			'tax_amount' => NULL,
			'booking_type' => 0,
			'total_single_supplement' => NULL,
			'token' => NULL,
			'origin' => NULL,
			'accessed_date' => '0000-00-00 00:00:00'
		));

		foreach ( $data as $key => $val ) {
			if ( array_key_exists( $key, $reservation_fields ) ) {
				$reservation_fields[ $key ] = $val;
			}
		}

		$reservation_fields['code'] = $this->get_code( $reservation_fields['created_date'] );
		$options = get_option( 'solidres_plugin' );

		if ( solidres()->session->get(  'sr_is_guest_making_reservation'  ) ) {
			if( ! empty( $options['default_reservation_state'] ) ) {
				$reservation_fields['state'] = $options['default_reservation_state'];
			}
		} else { // In the backend, let admin choose which reservation state is needed
			$custom_reservation_state = solidres()->session->get( 'sr_state' );
			$custom_reservation_payment_status = solidres()->session->get( 'sr_payment_status' );
			$reservation_fields['state'] = isset( $custom_reservation_state ) ? $custom_reservation_state : $reservation_fields['state'];
			$reservation_fields['payment_status'] = isset( $custom_reservation_payment_status ) ? $custom_reservation_payment_status : $reservation_fields['payment_status'] ;
		}

		if (isset($data['id']) && $data['id'] > 0) {

		}

		$format = array_merge( $format, array(
			'%s', // state
			'%d', // customer_id
			'%s', // created_date
			'%s', // modified_date
			'%d', // modified_by
			'%d', // created_by
			'%s', // payment_method_id
			'%s', // payment_method_txn_id
			'%d', // payment_status
			'%s', // payment_data
			'%s', // code
			'%d', // coupon_id
			'%s', // coupon_code
			'%s', // customer_title
			'%s', // customer_firstname
			'%s', // customer_middlename
			'%s', // customer_lastname
			'%s', // customer_email
			'%s', // customer_phonenumber
			'%s', // customer_mobilephone
			'%s', // customer_company
			'%s', // customer_address1
			'%s', // customer_address2
			'%s', // cusomer_city
			'%s', // customer_zipcode
			'%d', // customer_country_id
			'%d', // customer_geo_state_id
			'%s', // customer_vat_number
			'%s', // checkin
			'%s', // checkout
			'%s', // invoice_number
			'%d', // currency_id
			'%s', // currency_code
			'%f', // total_price
			'%f', // total_price_tax_incl
			'%f', // total_price_tax_excl
			'%f', // total_extra_price
			'%f', // total_extra_price_tax_incl
			'%f', // total_extra_price_tax_excl
			'%f', // total_discount
			'%s', // note
			'%d', // reservation_asset_id
			'%s', // reservation_asset_name
			'%f', // deposit_amount
			'%f', // total_paid
			'%d', // booking_type
			'%f', // total_single_supplement
			'%s', // token
			'%s', // origin,
			'%s', // accessed_date
		));

		if ( !$is_new ) {
			$result = $this->wpdb->update( $this->wpdb->prefix . 'sr_reservations',
				$reservation_fields,
				array( 'id' => $reservation_fields[ 'id' ] ),
				$format
			);
			$saved_reservation_id = $reservation_fields[ 'id' ];
		} else {
			$result = $this->wpdb->insert( $this->wpdb->prefix . 'sr_reservations', $reservation_fields, $format );
			$saved_reservation_id = $this->wpdb->insert_id;
		}

		do_action( 'sr_after_save', $data, $context, $saved_reservation_id);

		if (!$is_new) {
			$reservation_rooms = $this->wpdb->get_results( 'SELECT id FROM ' . $this->wpdb->prefix . 'sr_reservation_room_xref WHERE reservation_id = ' . $saved_reservation_id );
			foreach ( $reservation_rooms as $reservation_room ) {
				$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_room_details', array( 'reservation_room_id' => $reservation_room->id ) );
			}

			$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_room_xref', array( 'reservation_id' => $saved_reservation_id ) );

			$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_room_extra_xref', array( 'reservation_id' => $saved_reservation_id ) );
		}

		$roomTypePricesMapping = solidres()->session->get( 'sr_room_type_prices_mapping' );
		$solidres_room_type    = new SR_Room_Type();
		$solidres_tariff       = new SR_Tariff();
		$solidres_room         = new SR_Room();

		$isReservationFailed = false;
		if (isset($room_type_data) && is_array($room_type_data)) {
			foreach ( $room_type_data as $roomTypeId => $bookedTariffs ) {
				// If this index not exists, it means this reservation is created by guest
				if ( !isset( $data['reservation_room_select'] ) ) {
					// Find a list of available rooms
					$availableRoomList = $solidres_room_type->getListAvailableRoom( $roomTypeId, $data['checkin'], $data['checkout'], $data['booking_type'] );

					if (empty($availableRoomList))
					{
						$isReservationFailed = true;
						break;
					}
				}

				foreach ( $bookedTariffs as $tariffId => $rooms ) {
					foreach ( $rooms as $roomIndex => $room ) {
						// If this index not exists, it means this reservation is created by guest
						if ( !isset( $data['reservation_room_select'] ) ) {
							// Pick the first and assign it
							$pickedRoom = array_shift( $availableRoomList );
						} else {
							// In this case, the room index is the room ID
							$pickedRoom = $solidres_room->load( $roomIndex );
						}

						// Get the tariff info
						$booked_tariff = $solidres_tariff->load( $tariffId );

						$room['room_id']             = $pickedRoom->id;
						$room['room_label']          = $pickedRoom->label;
						$room['room_price']          = $roomTypePricesMapping[ $roomTypeId ][ $tariffId ][ $roomIndex ]['total_price_tax_incl'];
						$room['room_price_tax_incl'] = $roomTypePricesMapping[ $roomTypeId ][ $tariffId ][ $roomIndex ]['total_price_tax_incl'];
						$room['room_price_tax_excl'] = $roomTypePricesMapping[ $roomTypeId ][ $tariffId ][ $roomIndex ]['total_price_tax_excl'];
						$room['tariff_id']           = $tariffId > 0 ? $tariffId : null;
						$room['tariff_title']        = ! empty( $booked_tariff->title ) ? $booked_tariff->title : '';
						$room['tariff_description']  = ! empty( $booked_tariff->description ) ? $booked_tariff->description : '';

						$this->storeRoom( $saved_reservation_id, $room );

						// Insert new records
						if ( isset( $room['extras'] ) ) {
							foreach ( $room['extras'] as $extraId => $extraDetails ) {
								if ( isset( $extraDetails['quantity'] ) ) {
									$this->storeExtra( $saved_reservation_id, $room['room_id'], $room['room_label'], $extraId, $extraDetails['name'], $extraDetails['quantity'], $extraDetails['price_tax_incl'] );
								} else {
									$this->storeExtra( $saved_reservation_id, $room['room_id'], $room['room_label'], $extraId, $extraDetails['name'], null, $extraDetails['price_tax_incl'] );
								}
							}
						}
					}
				}
			}
		}

		// Store extra items (Per booking)
		if ( isset( $extra_data ) ) {

			if (!$is_new) {
				$this->wpdb->delete( $this->wpdb->prefix . 'sr_reservation_extra_xref', array( 'reservation_id' => $saved_reservation_id ) );
			}

			foreach ( $extra_data as $extraId => $extraDetails ) {
				$reservationExtraData = array(
					'reservation_id' => $saved_reservation_id,
					'extra_id'       => $extraId,
					'extra_name'     => $extraDetails['name'],
					'extra_quantity' => ( isset( $extraDetails['quantity'] ) ? $extraDetails['quantity'] : null ),
					'extra_price'    => $extraDetails['price_tax_incl']
				);

				$this->wpdb->insert( $this->wpdb->prefix . 'sr_reservation_extra_xref', $reservationExtraData );
			}
		}

		// Update the quantity of coupon
		if ( $is_new ) {
			if ( isset( $reservation_fields['coupon_id'] ) && $reservation_fields['coupon_id'] > 0 ) {
				$solidres_coupon = new SR_Coupon();
				$coupon          = $solidres_coupon->load( $reservation_fields['coupon_id'] );
				if ( ! is_null( $coupon->quantity ) && $coupon->quantity > 0 ) {
					$this->wpdb->update( $this->wpdb->prefix . 'sr_coupons', array( 'quantity' => (int) $coupon->quantity -- ), array( 'id' => $reservation_fields['coupon_id'] ) );
				}
			}
		}

		$stored_reservation_info = $this->load( $saved_reservation_id );

		solidres()->session->set( 'sr_saved_reservation_id', $saved_reservation_id );
		solidres()->session->set( 'sr_customer_firstname', $stored_reservation_info->customer_firstname );
		solidres()->session->set( 'sr_code', $stored_reservation_info->code );
		solidres()->session->set( 'sr_payment_method_id', $stored_reservation_info->payment_method_id );
		solidres()->session->set( 'sr_customeremail', $stored_reservation_info->customer_email );
		solidres()->session->set( 'sr_reservation_asset_name', $stored_reservation_info->reservation_asset_name );

		return $result;
	}

	/**
	 * Load all extras belong to this reservation $id
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function load_extras( $id ) {
		$results = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservation_extra_xref WHERE reservation_id = %d", $id ), OBJECT );

		return $results;
	}

	/**
	 * Export selected reservation to CSV format
	 *
	 * @param array $ids
	 */
	public function export( $ids ) {
		$results = array();
		foreach ( $ids as $id ) {
			$results[] = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}sr_reservations WHERE id = %d", $id ), ARRAY_A );
		}
		// disable caching
		$now = gmdate( "D, d M Y H:i:s" );
		header( "Expires: Tue, 03 Jul 2001 06:00:00 GMT" );
		header( "Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate" );
		header( "Last-Modified: {$now} GMT" );

		// force download
		header( "Content-Type: application/force-download" );
		header( "Content-Type: application/octet-stream" );
		header( "Content-Type: application/download" );

		// disposition / encoding on response body
		header( "Content-Disposition: attachment;filename=solidres_reservation_export.csv" );
		header( "Content-Transfer-Encoding: binary" );
		ob_end_clean();
		ob_start();
		$df = fopen( "php://output", 'w' );
		fputcsv( $df, array_keys( reset( $results ) ) );
		foreach ( $results as $row ) {
			fputcsv( $df, $row );
		}
		fclose( $df );
		echo ob_get_clean();
		exit();
	}

	public function update_state( $id, $state ) {
		$result = $this->wpdb->query( $this->wpdb->prepare( "UPDATE {$this->wpdb->prefix}sr_reservations SET state = %d WHERE id = %d", $state, $id ) );
		return $result;
	}

	public function update_transaction_id( $id, $posted ) {
		$result = $this->wpdb->query( $this->wpdb->prepare( "UPDATE {$this->wpdb->prefix}sr_reservations SET payment_method_txn_id = %s WHERE id = %d", $posted['txn_id'], $id ) );
		return $result;
	}

	public function update_payment_status( $id, $posted ) {
		switch($posted['payment_status'])
		{
			case 'canceled_reversal':
			case 'completed':
				$update_payment_status = '1'; // Completed
				break;
			case 'created':
			case 'pending':
			case 'processed':
				$update_payment_status = '3'; // Pending
				break;
			case 'denied':
			case 'expired':
			case 'failed':
			case 'refunded':
			case 'reversed':
			case 'voided':
			default:
				$update_payment_status = '2'; // Cancelled
				break;
		}
		$result = $this->wpdb->query( $this->wpdb->prepare( "UPDATE {$this->wpdb->prefix}sr_reservations SET payment_status = %d WHERE id = %d", $update_payment_status, $id ) );

		return $result;
	}

	public function update_total_paid( $id, $posted ) {

		$mcGross = floatval($posted['mc_gross']);
		$solidres_reservation = new SR_Reservation();
		$reservation = $solidres_reservation->load( $id );

		if ($mcGross > 0) {
			$amountToCheck = ((float) $reservation->deposit_amount > 0 ? (float) $reservation->deposit_amount : $reservation->total_price_tax_incl);
			$totalPaidAmount = 0;
			$isValidCallback = ( $amountToCheck - $mcGross ) < 0.01;
			if (!$isValidCallback) {
				//JLog::add('Invalid mc_gross: the reservation\'s total_price_tax_incl does not match with the paid amount', JLog::DEBUG );
			} else {
				// Record the paid amount for storing back to reservation table
				$totalPaidAmount = $amountToCheck;
			}
		}

		$result = $this->wpdb->query( $this->wpdb->prepare( "UPDATE {$this->wpdb->prefix}sr_reservations SET total_paid = %f WHERE id = %d", $totalPaidAmount, $id ) );

		return $result;
	}

	public function cancel( $id ) {

		$result = $this->wpdb->update( "{$this->wpdb->prefix}sr_reservations", array('state' => 4), array( 'id' => $id ) );

		return $result;
	}

	public function record_access( $id ) {
		return $this->wpdb->update( "{$this->wpdb->prefix}sr_reservations", array( 'accessed_date' => date('Y-m-d h:m:s')), array( 'id' => $id) );
	}

	public function count_unread() {
		return $this->wpdb->get_var( $this->wpdb->prepare( "SELECT count(*) FROM {$this->wpdb->prefix}sr_reservations WHERE accessed_date = %s", '0000-00-00 00:00:00' ) );
	}
}

