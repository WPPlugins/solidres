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

$address = array( 'address_1', 'address_2', 'city', 'postcode' );
$geocoding_address = array();
if ( ! empty( $sr_form_data ) ) {
	foreach ( $address as $add ) {
		if ( $sr_form_data->$add != null ) {
			$geocoding_address[] = $sr_form_data->$add;
		}
	}
}
$location = ( ! empty( $sr_form_data->lat ) && ! empty( $sr_form_data->lng ) ) ? json_encode( array(
	$sr_form_data->lat,
	$sr_form_data->lng
) ) : 'false';
?>

<script type="text/javascript">
	jQuery(function ($) {
		$("#geocomplete").geocomplete({
			map: ".map_canvas",
			details: "",
			location: <?php echo $location; ?>,
			markerOptions: {
				draggable: true
			}
		});

		$("#geocomplete").bind("geocode:dragged", function (event, latLng) {
			$("#update").attr("data-lat", latLng.lat());
			$("#update").attr("data-lng", latLng.lng());
			$("#update").show();
		});

		$("#geocomplete").bind("geocode:result", function (event, result) {
			var lat = result.geometry.location.lat();
			var lng = result.geometry.location.lng();
			lat = lat.toString().substr(0, 17);
			lng = lng.toString().substr(0, 17);
			$("input#srform_lat").val(lat);
			$("input#srform_lng").val(lng);
			$("#update").attr("data-lat", lat);
			$("#update").attr("data-lng", lng);
			$("#update").show();
		});

		$("#update").click(function () {
			$("input#srform_lat").val($(this).attr("data-lat"));
			$("input#srform_lng").val($(this).attr("data-lng"));
		});

		$("#reset").click(function () {
			$("#geocomplete").geocomplete("resetMarker");
			$("#update").hide();
			return false;
		});

		$("#find").click(function () {
			$("#geocomplete").trigger("geocode");
		});

		$(".geocoding").keyup(function() {
			var str = [];
			$(".geocoding").each(function() {
				var val = $(this).val();
				if (val != "") {
					str.push(val);
				}
			});
			$("#geocomplete").val(str.join(", "));
		});
	});
</script>

<div id="asset_general_infomation" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'General infomartion', 'solidres' ); ?></span></h3>

	<div class="inside">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label for="srform_name" title="<?php _e( 'For example: Sunny Hotel', 'solidres' ); ?>">
						<?php _e( 'Asset name', 'solidres' ); ?> <span class="required">*</span>
					</label>
				</th>
				<td><input type="text" name="srform[name]" maxlength="255"
				           value="<?php echo isset( $sr_form_data->name ) ? $sr_form_data->name : '' ?>"
				           id="srform_name" required class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="srform_alias" title="<?php _e( 'Alias is used in Search Engine Friendly URL.', 'solidres' ); ?>">
						<?php _e( 'Alias', 'solidres' ); ?> <span class="required">*</span>
					</label>
				</th>
				<td><input type="text" name="srform[alias]" maxlength="255"
				           value="<?php echo isset( $sr_form_data->alias ) ? $sr_form_data->alias : '' ?>"
				           id="srform_alias" required class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="srform_category" title="<?php _e( 'Specify the type of your asset.', 'solidres' ); ?>">
						<?php _e( 'Category', 'solidres' ); ?> <span class="required">*</span>
					</label>
				</th>
				<td>
					<select name="srform[category_id]" id="srform_category" class="" required>
						<option value=""><?php _e( 'Select asset category', 'solidres' ); ?></option>
						<?php echo isset( $sr_form_data->category_id ) ? SR_Helper::render_list_category( $sr_form_data->category_id ) : SR_Helper::render_list_category(); ?>
					</select>
				</td>
			</tr>
			<?php if ( ! current_user_can( 'solidres_partner' ) ) : ?>
			<tr>
				<th scope="row">
					<label for="srform_partner" title="<?php _e( 'Note: this field is enabled for subscribers only. Specify the customer who manages this reservation asset in front end. This field support auto complete, just type either email address or user name or customer code to find.', 'solidres' ); ?>">
						<?php _e( 'Partner', 'solidres' ); ?>
					</label>
				</th>
				<td>
					<select name="srform[partner_id]" class=""
					        id="srform_partner" <?php echo ( defined( 'SR_PLUGIN_USER_ENABLED' ) && ! SR_PLUGIN_USER_ENABLED ) ? 'disabled' : ''; ?> >
						<option value=""><?php _e( 'Select partner', 'solidres' ); ?></option>
						<?php if ( defined('SR_PLUGIN_USER_ENABLED') && SR_PLUGIN_USER_ENABLED ) {
							echo isset( $sr_form_data->partner_id ) ? SR_Partner::render_list_partner( $sr_form_data->partner_id ) : SR_Partner::render_list_partner();
						} ?>
					</select>
				</td>
			</tr>
			<?php endif ?>
			<tr>
				<th scope="row">
					<label for="srform_address1" title="<?php _e( 'The first address', 'solidres' ); ?>">
						<?php _e( 'Address 1', 'solidres' ); ?>
					</label>
				</th>
				<td><input type="text" name="srform[address_1]" maxlength="255"
				           value="<?php echo isset( $sr_form_data->address_1 ) ? $sr_form_data->address_1 : '' ?>"
				           id="srform_address1" class="regular-text geocoding valid"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_address2"
				                         title="<?php _e( 'The second address (optional)', 'solidres' ); ?>"><?php _e( 'Address 2', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[address_2]" maxlength="255"
				           value="<?php echo isset( $sr_form_data->address_2 ) ? $sr_form_data->address_2 : '' ?>"
				           id="srform_address2" class="regular-text geocoding"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_city"
				                         title="<?php _e( 'The city name of your reservation asset', 'solidres' ); ?>"><?php _e( 'City', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[city]" maxlength="45"
				           value="<?php echo isset( $sr_form_data->city ) ? $sr_form_data->city : '' ?>"
				           id="srform_city" class="regular-text geocoding"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_postcode"
				                         title="<?php _e( "The post code of your reservation asset's city", 'solidres' ); ?>"><?php _e( 'Post code', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[postcode]" maxlength="45"
				           value="<?php echo isset( $sr_form_data->postcode ) ? $sr_form_data->postcode : '' ?>"
				           id="srform_postcode" class="regular-text geocoding"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_email"
				                         title="<?php _e( 'This email will be used in From field of automated emails, for example: emails send to customer when they complete their reservations', 'solidres' ); ?>"><?php _e( 'Email', 'solidres' ); ?>
						<span class="required">*</span></label></th>
				<td><input type="email" name="srform[email]" maxlength="50"
				           value="<?php echo isset( $sr_form_data->email ) ? $sr_form_data->email : '' ?>"
				           id="srform_email" class="regular-text" required></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_website"
				                         title="<?php _e( "Your reservation asset's website", 'solidres' ); ?>"><?php _e( 'Website', 'solidres' ); ?></label>
				</th>
				<td><input type="url" name="srform[website]" maxlength="255"
				           value="<?php echo isset( $sr_form_data->website ) ? $sr_form_data->website : '' ?>"
				           id="srform_website" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_phone"
				                         title="<?php _e( 'Phone description', 'solidres' ); ?>"><?php _e( 'Phone', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[phone]" maxlength="30"
				           value="<?php echo isset( $sr_form_data->phone ) ? $sr_form_data->phone : '' ?>"
				           id="srform_phone" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_fax"
				                         title="<?php _e( 'Fax description', 'solidres' ); ?>"><?php _e( 'Fax', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[fax]" maxlength="45"
				           value="<?php echo isset( $sr_form_data->fax ) ? $sr_form_data->fax : '' ?>" id="srform_fax" class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_country"
				                         title="<?php _e( "Your reservation asset's country", 'solidres' ); ?>"> <?php _e( 'Country', 'solidres' ); ?>
						<span class="required">*</span></label></th>
				<td>
					<select name="srform[country_id]" class="srform_select_country" id="srform_country"
					        required>
						<?php echo isset( $sr_form_data->country_id ) ? SR_Helper::render_list_country( $sr_form_data->country_id ) : SR_Helper::render_list_country(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_geo_state"
				                         title="<?php _e( "Your reservation asset's state (optional)", 'solidres' ); ?>"><?php _e( 'State', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[geo_state_id]" class="srform_select_state"
					        id="srform_geo_state">
						<?php
						if ( isset( $sr_form_data->geo_state_id ) && $sr_form_data->geo_state_id > 0 ) {
							echo SR_Helper::render_list_geo_state( $sr_form_data->country_id, $sr_form_data->geo_state_id );
						} else {
							if ( isset( $sr_form_data->country_id ) && $sr_form_data->country_id > 0 ) {
								echo SR_Helper::render_list_geo_state( $sr_form_data->country_id );
							} else {
								echo SR_Helper::render_list_geo_state();
							}
						} ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_currency"
				                         title="<?php _e( 'Select the currency for this reservation asset', 'solidres' ); ?>"><?php _e( 'Currency', 'solidres' ); ?>
						<span class="required">*</span></label></th>
				<td>
					<select name="srform[currency_id]" class="" id="srform_currency" required>
						<option value=""><?php _e( 'Select asset currency', 'solidres' ); ?></option>
						<?php echo isset( $sr_form_data->currency_id ) ? SR_Helper::render_list_currency( $sr_form_data->currency_id ) : SR_Helper::render_list_currency(); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_tax"
				                         title="<?php _e( 'Select the tax that will apply to this asset. The tax list is depend on the selected country above. Please select a country first and the tax list will be displayed accordingly.', 'solidres' ); ?>"><?php _e( 'Tax', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[tax_id]" class="" id="srform_tax">
						<?php
						if ( isset( $sr_form_data->tax_id ) && $sr_form_data->tax_id > 0 ) {
							echo SR_Helper::render_list_tax_by_country( $sr_form_data->country_id, $sr_form_data->tax_id );
						} else {
							if ( isset( $sr_form_data->country_id ) && $sr_form_data->country_id > 0 ) {
								echo SR_Helper::render_list_tax_by_country( $sr_form_data->country_id );
							} else {
								echo SR_Helper::render_list_tax_by_country();
							}
						}

						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_booking_type"
				                         title="<?php _e( 'Select either booking per night (default) or per day. For example if you choose booking per night, then your guest will be charged 1 night for a stay from March 1 to March 2. In case of booking per day, your guest will be charged 2 day for a stay from March 1 to March 2.', 'solidres' ); ?>"><?php _e( 'Booking type', 'solidres' ); ?></label>
				</th>
				<td>
					<select name="srform[booking_type]" class="" id="srform_booking_type">
						<option value="0" <?php if ( isset( $sr_form_data->booking_type ) ) {
							echo $sr_form_data->booking_type == 0 ? 'selected' : '';
						} ?> ><?php _e( 'Per night', 'solidres' ); ?></option>
						<option value="1" <?php if ( isset( $sr_form_data->booking_type ) ) {
							echo $sr_form_data->booking_type == 1 ? 'selected' : '';
						} ?> ><?php _e( 'Per day', 'solidres' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"></th>
				<td>
					<div class="map_canvas"></div>
					<div class="input-append">
						<input id="geocomplete" type="text" placeholder=""
						       value="<?php echo implode( ',', $geocoding_address ); ?>"/>
						<button class="button" id="find"
						        type="button"><?php _e( 'Show address in map', 'solidres' ); ?></button>
						<button class="button"
						        data-lat="<?php echo isset( $sr_form_data->lat ) ? $sr_form_data->lat : '' ?>"
						        data-lng="<?php echo isset( $sr_form_data->lng ) ? $sr_form_data->lng : '' ?>"
						        id="update" type="button"
						        style="display:none;"><?php _e( 'Update coordinates', 'solidres' ); ?></button>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_lat"
				                         title="<?php _e( "Enter your asset's latitude directly or drag the map maker to enter it.", 'solidres' ); ?>"><?php _e( 'Latitude', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[lat]" maxlength="17"
				           value="<?php echo isset( $sr_form_data->lat ) ? $sr_form_data->lat : '' ?>" id="srform_lat" class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_lng"
				                         title="<?php _e( "Enter your asset's longitude directly or drag the map maker to enter it.", 'solidres' ); ?>"><?php _e( 'Longitude', 'solidres' ); ?></label>
				</th>
				<td><input type="text" name="srform[lng]" maxlength="17"
				           value="<?php echo isset( $sr_form_data->lng ) ? $sr_form_data->lng : '' ?>" id="srform_lng" class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="srform_description"
				                         title="<?php _e( 'Describe your reservation asset', 'solidres' ); ?>"><?php _e( 'Description', 'solidres' ); ?></label>
				</th>
				<td>
					<?php
					$settings = array(
						'media_buttons' => false,
						'textarea_name' => 'srform[description]',
						'drag_drop_upload' => true,
						'tabfocus_elements' => 'content-html,save-post',
						'editor_height' => 300,
						'tinymce' => array(
							'resize' => false,
							'wp_autoresize_on' => true,
							'add_unload_trigger' => false,
						)
					);
					wp_editor( isset( $sr_form_data->description ) ? $sr_form_data->description : '', 'content', $settings );
					?>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>