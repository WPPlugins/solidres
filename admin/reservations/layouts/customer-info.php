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

$get_country_name = '';
$get_state_name   = '';
if ( $sr_form_data->customer_country_id != null ) {
	$get_country_name = $wpdb->get_var( $wpdb->prepare( "SELECT c.name as countryname FROM {$wpdb->prefix}sr_reservations r LEFT JOIN {$wpdb->prefix}sr_countries c ON r.customer_country_id = c.id WHERE r.id = %d", $id ) );
}
if ( $sr_form_data->customer_geo_state_id != null ) {
	$get_state_name = $wpdb->get_var( $wpdb->prepare( "SELECT s.name as statename FROM {$wpdb->prefix}sr_reservations r LEFT JOIN {$wpdb->prefix}sr_geo_states s ON r.customer_geo_state_id = s.id WHERE r.id = %d", $id ) );
}
?>
<div id="reservation_customer_info" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Customer info', 'solidres' ); ?></span></h3>

	<div class="inside reservation-details">
		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Customer title', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_title ) ? $sr_form_data->customer_title : ''; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Address 1', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_address1 ) ? $sr_form_data->customer_address1 : ''; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'First name', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_firstname ) ? $sr_form_data->customer_firstname : ''; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Address 2', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_address2 ) ? $sr_form_data->customer_address2 : ''; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Middle name', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_middlename ) ? $sr_form_data->customer_middlename : ''; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'City', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_city ) ? $sr_form_data->customer_city : ''; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Last name', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_lastname ) ? $sr_form_data->customer_lastname : ''; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Zip code', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_zipcode ) ? $sr_form_data->customer_zipcode : ''; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Email', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_email ) ? $sr_form_data->customer_email : ''; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Country', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo $get_country_name; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Phone', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_phonenumber ) ? $sr_form_data->customer_phonenumber : ''; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'State', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo $get_state_name; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Mobile phone', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_mobilephone ) ? $sr_form_data->customer_mobilephone : ''; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'VAT Number', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_vat_number ) ? $sr_form_data->customer_vat_number : ''; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Company', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->customer_company ) ? $sr_form_data->customer_company : ''; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Notes', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo isset( $sr_form_data->note ) ? $sr_form_data->note : ''; ?>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>