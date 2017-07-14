<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * RoomType handler class
 * @package 	Solidres
 * @subpackage	RoomType
 * @since 		0.1.0
 */
class SR_Tariff {

	public $type_name_mapping = array();

	const PER_ROOM_PER_NIGHT = 0;

	const PER_PERSON_PER_NIGHT = 1;

	const PACKAGE_PER_ROOM = 2;

	const PACKAGE_PER_PERSON = 3;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->type_name_mapping = array(
			0 => __( 'Rate per room per night', 'solidres' ),
			1 => __( 'Rate per person per night', 'solidres' ),
			2 => __( 'Package per room', 'solidres' ),
			3 => __( 'Package per person', 'solidres' ),
		);
	}

	/**
	 * Delete a single tariff
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function delete( $id ) {
		$this->wpdb->query( $this->wpdb->prepare("DELETE FROM {$this->wpdb->prefix}sr_tariff_details WHERE tariff_id = %d", $id) );
		$this->wpdb->query( $this->wpdb->prepare("UPDATE {$this->wpdb->prefix}sr_reservation_room_xref SET tariff_id = NULL WHERE tariff_id = %d", $id) );
		$result = $this->wpdb->query( $this->wpdb->prepare("DELETE FROM {$this->wpdb->prefix}sr_tariffs WHERE id = %d", $id) );

		return $result;
	}

	/**
	 * Get a single tariff by id
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function load( $id ) {
		if (!isset($id) || !is_numeric($id)) {
			$tariff = new stdClass();
			$tariff->id = NULL;
			$tariff->currency_id = NULL;
			$tariff->customer_group_id = NULL;
			$tariff->valid_from = NULL;
			$tariff->valid_to = NULL;
			$tariff->room_type_id = NULL;
			$tariff->title = NULL;
			$tariff->description = NULL;
			$tariff->d_min = 0;
			$tariff->d_max = 0;
			$tariff->p_min = 0;
			$tariff->p_max = 0;
			$tariff->type = 0;
			$tariff->limit_checkin = '["0","1","2","3","4","5","6"]';
			$tariff->state = NULL;
		} else {
			$tariff = $this->wpdb->get_row( "SELECT t.*, cgroup.name as customer_group_name 
												FROM {$this->wpdb->prefix}sr_tariffs as t
												LEFT JOIN {$this->wpdb->prefix}sr_customer_groups as cgroup ON cgroup.id = t.customer_group_id
												WHERE t.id = $id
											");

			$tariff->type_name         = $this->type_name_mapping[ $tariff->type ];
			$tariff->valid_from        = $tariff->valid_from != '0000-00-00' ? date( 'd-m-Y', strtotime( $tariff->valid_from ) ) : '00-00-0000';
			$tariff->valid_to          = $tariff->valid_to != '0000-00-00' ? date( 'd-m-Y', strtotime( $tariff->valid_to ) ) : '00-00-0000';
			$tariff->customer_group_id = is_null( $tariff->customer_group_id ) ? '' : $tariff->customer_group_id;
			$tariff->limit_checkin     = isset( $tariff->limit_checkin ) ? json_decode( $tariff->limit_checkin ) : null;

			if ( (int) $tariff->type == self::PER_ROOM_PER_NIGHT ) {
				$results = $this->load_details( $tariff->id );

				if ( ! empty( $results ) ) {
					$tariff->details['per_room'] = $results;
				} else {
					$tariff->details['per_room'] = $this->get_tariff_details_scaffoldings( array(
						'tariff_id'  => $tariff->id,
						'guest_type' => null,
						'type'       => $tariff->type,
					) );
				}

				$tariff->details['per_room'] = SR_Utilities::translateDayWeekName( $tariff->details['per_room'] );
			} else if ( (int) $tariff->type == self::PER_PERSON_PER_NIGHT ) {
				// Query to get tariff details for each guest type
				// First we need to get the occupancy number
				$solidres_room_type = new SR_Room_Type();
				$room_type          = $solidres_room_type->load( $tariff->room_type_id );
				$occupancy_adult    = $room_type->occupancy_adult;
				$occupancy_child    = $room_type->occupancy_child;

				// Get tariff details for all adults
				for ( $i = 1; $i <= $occupancy_adult; $i ++ ) {
					$results = $this->load_details( $tariff->id, 'adult' . $i );

					if ( ! empty( $results ) ) {
						$tariff->details[ 'adult' . $i ] = $results;
					} else {
						$tariff->details[ 'adult' . $i ] = $this->get_tariff_details_scaffoldings( array(
							'tariff_id'  => $tariff->id,
							'guest_type' => 'adult' . $i,
							'type'       => $tariff->type,
						) );
					}

					$tariff->details[ 'adult' . $i ] = SR_Utilities::translateDayWeekName( $tariff->details[ 'adult' . $i ] );
				}

				// Get tariff details for all children
				for ( $i = 1; $i <= $occupancy_child; $i ++ ) {
					$results = $this->load_details( $tariff->id, 'child' . $i );

					if ( ! empty( $results ) ) {
						$tariff->details[ 'child' . $i ] = $results;
					} else {
						$tariff->details[ 'child' . $i ] = $this->get_tariff_details_scaffoldings( array(
							'tariff_id'  => $tariff->id,
							'guest_type' => 'child' . $i,
							'type'       => $tariff->type,
						) );
					}

					$tariff->details[ 'child' . $i ] = SR_Utilities::translateDayWeekName( $tariff->details[ 'child' . $i ] );
				}
			} else if ( (int) $tariff->type == self::PACKAGE_PER_ROOM ) {
				$results = $this->load_details( $tariff->id );

				if ( ! empty( $results ) ) {
					$tariff->details['per_room'] = $results;
				} else {
					$tariff->details['per_room'] = $this->get_tariff_details_scaffoldings( array(
						'tariff_id'  => $tariff->id,
						'guest_type' => null,
						'type'       => $tariff->type,
					) );
				}
			} else if ( (int) $tariff->type == self::PACKAGE_PER_PERSON ) {
				// Query to get tariff details for each guest type
				// First we need to get the occupancy number
				$solidres_room_type = new SR_Room_Type();
				$room_type          = $solidres_room_type->load( $tariff->room_type_id );
				$occupancy_adult    = $room_type->occupancy_adult;
				$occupancy_child    = $room_type->occupancy_child;

				// Get tariff details for all adults
				for ( $i = 1; $i <= $occupancy_adult; $i ++ ) {
					$results = $this->load_details( $tariff->id, 'adult' . $i );

					if ( ! empty( $results ) ) {
						$tariff->details[ 'adult' . $i ] = $results;
					} else {
						$tariff->details[ 'adult' . $i ] = $this->get_tariff_details_scaffoldings( array(
							'tariff_id'  => $tariff->id,
							'guest_type' => 'adult' . $i,
							'type'       => $tariff->type,
						) );
					}
				}

				// Get tariff details for all children
				for ( $i = 1; $i <= $occupancy_child; $i ++ ) {
					$results = $this->load_details( $tariff->id, 'child' . $i );

					if ( ! empty( $results ) ) {
						$tariff->details[ 'child' . $i ] = $results;
					} else {
						$tariff->details[ 'child' . $i ] = $this->get_tariff_details_scaffoldings( array(
							'tariff_id'  => $tariff->id,
							'guest_type' => 'child' . $i,
							'type'       => $tariff->type,
						) );
					}
				}
			}
		}

		return $tariff;
	}

	/**
	 * Get a single tariff by room type id
	 *
	 * @param        $room_type_id
	 * @param bool   $standard
	 * @param string $output
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @param int    $state
	 * @param int    $customer_group_id
	 *
	 * @return mixed
	 */
	public function load_by_room_type_id( $room_type_id, $standard = true, $output = OBJECT, $checkin = '', $checkout = '', $state = 1, $customer_group_id = NULL ) {
		$query = "SELECT t.*, cgroup.name as customer_group_name FROM {$this->wpdb->prefix}sr_tariffs AS t LEFT JOIN {$this->wpdb->prefix}sr_customer_groups as cgroup ON cgroup.id = t.customer_group_id";
		if ( $standard ) {
			$query .= " WHERE t.valid_from = '0000-00-00' AND t.valid_to = '0000-00-00'";
		} else {
			$query .= " WHERE t.valid_from != '0000-00-00' AND t.valid_to != '0000-00-00'";
			if ( !empty($checkin) && !empty($checkout)) {
				$query .= " AND t.valid_from <= %s AND t.valid_to >= %s";
			}
		}

		$query .= " AND t.room_type_id = %d";

		if ( isset($state) ) {
			$query .= " AND t.state = " . (int) $state;
		}

		// Filter by customer group id
		// -1 means no checking, load them all
		// NULL means load tariffs for Public customer group
		// any other value > 0 means load tariffs belong to specific groups
		if ( $customer_group_id != -1) {
			$query .= " AND t.customer_group_id " . ( $customer_group_id === NULL ? 'IS NULL' : '= ' .(int) $customer_group_id );
		}

		$query .= ' ORDER BY t.valid_from ASC';

		if (!$standard) {
			if ( !empty($checkin) && !empty($checkout)) {
				$tariffs = $this->wpdb->get_results( $this->wpdb->prepare($query, $checkin, $checkout, $room_type_id), $output );
			} else {
				$tariffs = $this->wpdb->get_results( $this->wpdb->prepare($query, $room_type_id), $output );
			}

			foreach ($tariffs as &$tariff) {
				$tariff = $this->load($tariff->id, $standard);
			}
			return $tariffs;
		} else {
			return $this->wpdb->get_results( $this->wpdb->prepare($query, $room_type_id), $output );
		}
	}

	public function load_details( $id, $guest_type = NULL ) {
		if ( isset($guest_type) ) {
			$query = $this->wpdb->prepare(
				"SELECT * FROM {$this->wpdb->prefix}sr_tariff_details
				WHERE tariff_id = %d AND guest_type = %s
				ORDER BY w_day ASC",
				array( $id, $guest_type )
			);
		} else {
			$query = $this->wpdb->prepare(
				"SELECT * FROM {$this->wpdb->prefix}sr_tariff_details
				WHERE tariff_id = %d
				ORDER BY w_day ASC",
				array( $id )
			);
		}

		return $this->wpdb->get_results( $query );
	}

	public function get_tariff_details_scaffoldings($config = array())
	{
		$scaffoldings = array();

		// If this is package per person or package per room
		if ($config['type'] == 2 || $config['type'] == 3 )
		{
			$scaffoldings[0] = new stdClass();
			$scaffoldings[0]->id = null;
			$scaffoldings[0]->tariff_id = $config['tariff_id'];
			$scaffoldings[0]->price = null;
			$scaffoldings[0]->w_day = 8;
			$scaffoldings[0]->guest_type = $config['guest_type'];
			$scaffoldings[0]->from_age = null;
			$scaffoldings[0]->to_age = null;
		}
		else // For normal complex tariff
		{
			for ($i = 0; $i < 7; $i++)
			{
				$scaffoldings[$i] = new stdClass();
				$scaffoldings[$i]->id = null;
				$scaffoldings[$i]->tariff_id = $config['tariff_id'];
				$scaffoldings[$i]->price = null;
				$scaffoldings[$i]->w_day = $i;
				$scaffoldings[$i]->guest_type = $config['guest_type'];
				$scaffoldings[$i]->from_age = null;
				$scaffoldings[$i]->to_age = null;
			}
		}

		return $scaffoldings;
	}

	public function save($tariff) {
		if (!empty($tariff->limit_checkin) && is_array($tariff->limit_checkin)) {
			$tariff->limit_checkin = json_encode($tariff->limit_checkin);
		}

		if ($tariff->customer_group_id === '')
		{
			$tariff->customer_group_id = NULL;
		}

		$tariff->valid_from = date('Y-m-d', strtotime($tariff->valid_from));
		$tariff->valid_to = date('Y-m-d', strtotime($tariff->valid_to));

		if (isset($tariff->id) && $tariff->id > 0) {
			$current_tariff = $this->load($tariff->id, false);

			if ($current_tariff->type != $tariff->type) {
				$this->wpdb->get_results( "DELETE FROM {$this->wpdb->prefix}sr_tariff_details WHERE tariff_id = $current_tariff->id" );
			}

			$this->wpdb->update( $this->wpdb->prefix . 'sr_tariffs',
				array(
					'currency_id' => $tariff->currency_id,
					'customer_group_id' => $tariff->customer_group_id,
					'valid_from'    => $tariff->valid_from,
					'valid_to'      => $tariff->valid_to,
					'room_type_id'  => $tariff->room_type_id,
					'title'       => $tariff->title,
					'description' => $tariff->description,
					'd_min'       => $tariff->d_min,
					'd_max' => $tariff->d_max,
					'p_min'       => $tariff->p_min,
					'p_max' => $tariff->p_max,
					'type'          => $tariff->type,
					'limit_checkin' => $tariff->limit_checkin,
					'state' => $tariff->state,
				),
				array(
					'id' => $tariff->id,
				),
				array(
					'%d',
					'%d',
					'%s',
					'%s',
					'%d',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%d',
				)
			);
		} else {
			$this->wpdb->insert( $this->wpdb->prefix . 'sr_tariffs',
				array(
					'currency_id' => $tariff->currency_id,
					'customer_group_id' => $tariff->customer_group_id,
					'valid_from'    => $tariff->valid_from,
					'valid_to'      => $tariff->valid_to,
					'room_type_id'  => $tariff->room_type_id,
					'title'       => $tariff->title,
					'description' => $tariff->description,
					'd_min'       => $tariff->d_min,
					'd_max' => $tariff->d_max,
					'p_min'       => $tariff->p_min,
					'p_max' => $tariff->p_max,
					'type'          => $tariff->type,
					'limit_checkin' => $tariff->limit_checkin,
					'state' => $tariff->state,
				)
			);

			$insert_id = $this->wpdb->insert_id;
		}

		$tariff_id =  isset($tariff->id) && $tariff->id > 0 ? $tariff->id : $insert_id;

		// Now process the tariff details
		if (isset($tariff->details)) {
			foreach ($tariff->details as $tariff_type => $details) {
				foreach ($details as $detail) {
					$detail->tariff_id = $tariff_id;
					$solidres_tariff_detail = new SR_Tariff_Detail();
					$solidres_tariff_detail->save($detail);
				}
			}
		}

		return $tariff_id ;
	}
}