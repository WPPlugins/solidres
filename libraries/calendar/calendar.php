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
 * CodeIgniter Calendar Class
 *
 * This class enables the creation of calendars
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/calendar.html
 */

class SR_Calendar {
	var $local_time;
	var $template		= '';
	var $start_day		= 'sunday';
	var $month_type		= 'long';
	var $day_type		= 'abr';
	var $show_next_prev	= FALSE;
	var $next_prev_url	= '';

	public function __construct( $config = array() ) {
		$this->local_time = time();
		if ( count( $config ) > 0 ) {
			$this->initialize( $config );
		}
	}

	function initialize( $config = array() ) {
		foreach ( $config as $key => $val ) {
			if ( isset( $this->$key ) ) {
				$this->$key = $val;
			}
		}
	}

	/**
	 * Generate the calendar
	 *
	 * @access    public
	 * @param     string   $year
	 * @param     string   $month
	 * @param     integer  the month
	 * @param     array    $data
	 * @internal param \the $array data to be shown in the calendar cells
	 * @return    string
	 */
	function generate( $year = '', $month = '', $roomTypeID, $data = array() ) {
		$solidresRoomType = new SR_Room_Type();
		$booking_type = $solidresRoomType->get_booking_type($roomTypeID);
		// Set and validate the supplied month/year
		if ( $year == '' )
			$year  = date( "Y", $this->local_time );
		if ( $month == '' )
			$month = date( "m", $this->local_time );
		if ( strlen( $year ) == 1 )
			$year = '200'.$year;
		if ( strlen( $year ) == 2 )
			$year = '20'.$year;
		if ( strlen( $month ) == 1 )
			$month = '0'.$month;

		$adjusted_date = $this->adjust_date( $month, $year );
		$month	= $adjusted_date['month'];
		$year	= $adjusted_date['year'];

		// Determine the total days in the month
		$total_days = $this->get_total_days( $month, $year );

		// Set the starting day of the week
		$start_days	= array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
		$start_day = ( ! isset( $start_days[$this->start_day] ) ) ? 0 : $start_days[$this->start_day];

		// Set the starting day number
		$local_date = mktime( 12, 0, 0, $month, 1, $year );
		$date = getdate( $local_date );
		$day  = $start_day + 1 - $date["wday"];

		while ( $day > 1 ) {
			$day -= 7;
		}

		// Set the current month/year/day
		// We use this to determine the "today" date
		$cur_year	= date( "Y", $this->local_time );
		$cur_month	= date( "m", $this->local_time );
		$cur_day	= date( "j", $this->local_time );

		$is_current_month = ( $cur_year == $year AND $cur_month == $month ) ? TRUE : FALSE;

		// Generate the template data array
		$this->parse_template();

		// Begin building the calendar output
		$out = str_replace('{booking_type}', $booking_type, $this->temp['table_open']);
		$out .= "\n";

		$out .= "\n";
		$out .= $this->temp['heading_row_start'];
		$out .= "\n";

		// "previous" month link
		if ( $this->show_next_prev == TRUE ) {
			// Add a trailing slash to the  URL if needed
			$this->next_prev_url = preg_replace( "/(.+?)\/*$/", "\\1/",  $this->next_prev_url );
			$adjusted_date = $this->adjust_date( $month - 1, $year );
			$out .= str_replace( '{previous_url}', $this->next_prev_url.$adjusted_date['year'].'/'.$adjusted_date['month'], $this->temp['heading_previous_cell'] );
			$out .= "\n";
		}

		// Heading containing the month/year
		$colspan = ( $this->show_next_prev == TRUE ) ? 5 : 7;
		$this->temp['heading_title_cell'] = str_replace('{colspan}', $colspan, $this->temp['heading_title_cell']);
		$this->temp['heading_title_cell'] = str_replace('{heading}', $this->get_month_name( $month )."&nbsp;".$year, $this->temp['heading_title_cell']);
		$out .= $this->temp['heading_title_cell'];
		$out .= "\n";

		// "next" month link
		if ($this->show_next_prev == TRUE) {
			$adjusted_date = $this->adjust_date($month + 1, $year);
			$out .= str_replace('{next_url}', $this->next_prev_url.$adjusted_date['year'].'/'.$adjusted_date['month'], $this->temp['heading_next_cell']);
		}
		$out .= "\n";
		$out .= $this->temp['heading_row_end'];
		$out .= "\n";

		// Write the cells containing the days of the week
		$out .= "\n";
		$out .= $this->temp['week_row_start'];
		$out .= "\n";
		$day_names = $this->get_day_names();
		for ( $i = 0; $i < 7; $i++ ) {
			if ( isset( $day_names[( $start_day + $i ) %7] ) ) {
				$out .= str_replace( '{week_day}', $day_names[( $start_day + $i ) %7], $this->temp['week_day_cell'] );
			}
		}
		$out .= "\n";
		$out .= $this->temp['week_row_end'];
		$out .= "\n";

		// Build the main body of the calendar
		global $wpdb;
		$totalAvailableRooms = $wpdb->get_var(
			$wpdb->prepare( "SELECT count(*) FROM {$wpdb->prefix}sr_rooms WHERE room_type_id = %d", $roomTypeID )
		);
		while ( $day <= $total_days ) {
			$out .= "\n";
			$out .= $this->temp['cal_row_start'];
			$out .= "\n";
			for ( $i = 0; $i < 7; $i++ ) {
				if ( $day > 0 AND $day <= $total_days ) {
					$checkin = date('Y-m-d', strtotime($year.'-'.$month.'-'.$day));
					$cell_class = '';
					if ($booking_type == 0)
					{
						$checkout = date('Y-m-d', strtotime('+1 day', strtotime($checkin)));
					}
					else
					{
						$checkout = $checkin;
					}
					$availableRooms = $solidresRoomType->getListAvailableRoom( $roomTypeID, $checkin, $checkout, $booking_type );

					$temp = ($is_current_month == TRUE AND $day == $cur_day) ? $this->temp['cal_cell_start_today_busy'] : $this->temp['cal_cell_start_busy'];
					$data[$day]['cell_class'] = '';

					if ( $month <= $cur_month && $day < $cur_day && $year <= $cur_year ) {
						$data[$day]['cell_class'] .= 'past';
					}

					if ( ! $availableRooms || count( $availableRooms ) == 0 ) {
						$cell_class .= 'busy';
						$previous_state = 'busy';
						$tmp_reservation_count = $wpdb->get_var(
							$wpdb->prepare( "SELECT count(*) 
								FROM {$wpdb->prefix}sr_reservations AS a
								INNER JOIN {$wpdb->prefix}sr_rooms AS b ON b.room_type_id = %d
								INNER JOIN {$wpdb->prefix}sr_reservation_room_xref AS c ON c.room_id = b.id AND c.reservation_id = a.id
								WHERE a.checkin = %s",
								$roomTypeID, $checkin )
						);
						$has_check_in = $tmp_reservation_count > 0;
						$cell_class .= $has_check_in ? ' ci' : '';
						$data[$day]['cell_link'] = 'javascript:void(0)';
					}

					$tmp_reservation_count = $wpdb->get_var(
						$wpdb->prepare( "SELECT count(*) FROM {$wpdb->prefix}sr_reservations AS a
							INNER JOIN {$wpdb->prefix}sr_rooms AS b ON b.room_type_id = %d
							INNER JOIN {$wpdb->prefix}sr_reservation_room_xref AS c ON c.room_id = b.id AND c.reservation_id = a.id
							WHERE a.checkout = %s", $roomTypeID, $checkin )
					);
					$has_check_out = $tmp_reservation_count > 0;
					if ($has_check_out) {
						$data[$day]['cell_link'] = 'javascript:void(0)';
					}
					$cell_class .= $has_check_out && isset($previous_state) && $previous_state == 'busy' ? ' co' : '';
					if (!empty($availableRooms))
					{
						$previous_state = '';
					}
					$data[$day]['cell_class'] .= ' ' . $cell_class;


					$out .= str_replace( '{cell_class}', $data[$day]['cell_class'], $temp );
					if ( isset( $data[$day] ) && isset( $data[$day]['cell_link'] ) ) {
						// Cells with content
						$temp = ( $is_current_month == TRUE AND $day == $cur_day ) ? $this->temp['cal_cell_content_today'] : $this->temp['cal_cell_content'];
						$out .= str_replace( '{cell_class}', $data[$day]['cell_class'], str_replace( '{day}', $day, str_replace('{cell_link}', $data[$day]['cell_link'], $temp ) ) );
					} else {
						// Cells with no content
						$temp = ( $is_current_month == TRUE AND $day == $cur_day ) ? $this->temp['cal_cell_no_content_today'] : $this->temp['cal_cell_no_content'];
						$out .= str_replace('{day}', $day, $temp);
					}
				} else {
					// Blank cells
					$out .= ( $is_current_month == TRUE AND $day == $cur_day ) ? $this->temp['cal_cell_start_today'] : $this->temp['cal_cell_start'];
					$out .= $this->temp['cal_cell_blank'];
				}
				$out .= ( $is_current_month == TRUE AND $day == $cur_day ) ? $this->temp['cal_cell_end_today'] : $this->temp['cal_cell_end'];
				$day++;
			}
			$out .= "\n";
			$out .= $this->temp['cal_row_end'];
			$out .= "\n";
		}
		$out .= "\n";
		$out .= $this->temp['table_close'];
		return $out;
	}

	/**
	 * Get Month Name
	 *
	 * Generates a textual month name based on the numeric
	 * month provided.
	 *
	 * @access	public
	 * @param	integer	the month
	 * @return	string
	 */
	function get_month_name( $month ) {
		if ( $this->month_type == 'short' ) {
			$month_names = array('01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec');
		} else {
			$month_names = array('01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
		}
		$month = $month_names[$month];
		return __( $month, 'solidres' );
	}

	/**
	 * Get Day Names
	 *
	 * Returns an array of day names (Sunday, Monday, etc.) based
	 * on the type.  Options: long, short, abrev
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	function get_day_names( $day_type = '' ) {
		if ( $day_type != '' )
			$this->day_type = $day_type;

		if ( $this->day_type == 'long' ) {
			$day_names = array( 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' );
		}
		elseif ( $this->day_type == 'short' ) {
			$day_names = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );
		}
		else {
			$day_names = array( 'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa' );
		}
		$days = array();
		foreach ( $day_names as $val ) {
			$days[] = ucfirst( __( $val, 'solidres' ) );
		}
		return $days;
	}

	/**
	 * Adjust Date
	 *
	 * This function makes sure that we have a valid month/year.
	 * For example, if you submit 13 as the month, the year will
	 * increment and the month will become January.
	 *
	 * @access	public
	 * @param	integer	the month
	 * @param	integer	the year
	 * @return	array
	 */
	function adjust_date( $month, $year ) {
		$date = array();
		$date['month']	= $month;
		$date['year']	= $year;

		while ( $date['month'] > 12 ) {
			$date['month'] -= 12;
			$date['year']++;
		}
		while ( $date['month'] <= 0 ) {
			$date['month'] += 12;
			$date['year']--;
		}
		if ( strlen( $date['month'] ) == 1 ) {
			$date['month'] = '0'.$date['month'];
		}
		return $date;
	}

	/**
	 * Total days in a given month
	 *
	 * @access	public
	 * @param	integer	the month
	 * @param	integer	the year
	 * @return	integer
	 */
	function get_total_days( $month, $year ) {
		$days_in_month	= array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
		if ( $month < 1 OR $month > 12 ) {
			return 0;
		}

		// Is the year a leap year?
		if ( $month == 2 ) {
			if ( $year % 400 == 0 OR ( $year % 4 == 0 AND $year % 100 != 0 ) ) {
				return 29;
			}
		}
		return $days_in_month[$month - 1];
	}

	/**
	 * Set Default Template Data
	 *
	 * This is used in the event that the user has not created their own template
	 *
	 * @access	public
	 * @return array
	 */
	function default_template() {
		return  array (
			'table_open'				=> '<table border="0" cellpadding="4" cellspacing="0" class="btype-{booking_type}">',
			'heading_row_start'			=> '<tr>',
			'heading_previous_cell'		=> '<th><a href="{previous_url}">&lt;&lt;</a></th>',
			'heading_title_cell'		=> '<th colspan="{colspan}">{heading}</th>',
			'heading_next_cell'			=> '<th><a href="{next_url}">&gt;&gt;</a></th>',
			'heading_row_end'			=> '</tr>',
			'week_row_start'			=> '<tr class="row-week-day">',
			'week_day_cell'				=> '<td>{week_day}</td>',
			'week_row_end'				=> '</tr>',
			'cal_row_start'				=> '<tr class="row-month-day">',
			'cal_cell_start'			=> '<td>',
			'cal_cell_start_today'		=> '<td>',
			'cal_cell_start_busy'		=> '<td class="{cell_class}">',
			'cal_cell_start_today_busy'	=> '<td class="{cell_class}">',
			'cal_cell_content'			=> '<a href="{cell_link}">{day}</a>',
			'cal_cell_content_today'	=> '<a class="today" href="{cell_link}">{day}</a>',
			'cal_cell_no_content'		=> '{day}',
			'cal_cell_no_content_today'	=> '<span class="today">{day}</span>',
			'cal_cell_blank'			=> '&nbsp;',
			'cal_cell_end'				=> '</td>',
			'cal_cell_end_today'		=> '</td>',
			'cal_row_end'				=> '</tr>',
			'table_close'				=> '</table>'
		);
	}

	/**
	 * Parse Template
	 *
	 * Harvests the data within the template {pseudo-variables}
	 * used to display the calendar
	 *
	 * @access	public
	 * @return	void
	 */
	function parse_template() {
		$this->temp = $this->default_template();

		if ( $this->template == '' ) {
			return;
		}

		$today = array( 'cal_cell_start_today', 'cal_cell_content_today', 'cal_cell_no_content_today', 'cal_cell_end_today' );
		foreach ( array( 'table_open', 'table_close', 'heading_row_start', 'heading_previous_cell', 'heading_title_cell', 'heading_next_cell', 'heading_row_end', 'week_row_start', 'week_day_cell', 'week_row_end', 'cal_row_start', 'cal_cell_start', 'cal_cell_content', 'cal_cell_no_content',  'cal_cell_blank', 'cal_cell_end', 'cal_row_end', 'cal_cell_start_today', 'cal_cell_content_today', 'cal_cell_no_content_today', 'cal_cell_end_today' ) as $val )
		{
			if ( preg_match("/\{".$val."\}(.*?)\{\/".$val."\}/si", $this->template, $match ) ) {
				$this->temp[$val] = $match['1'];
			}
			else {
				if ( in_array($val, $today, TRUE) ) {
					$this->temp[$val] = $this->temp[str_replace( '_today', '', $val )];
				}
			}
		}
	}
}