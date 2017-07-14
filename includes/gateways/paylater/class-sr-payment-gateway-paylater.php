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

class Solidres_Payment_Gateway_Paylater {

	public function __construct() {
		$this->identifier = 'paylater';
		$this->defaultStatus = 5; // Confirmed
		$this->title = __( 'Pay Later', 'solidres' );
		$this->description = __( 'Pay Later', 'solidres' );
		$this->useCurl = true;
		$this->response = NULL;
		$this->responseStatus = '';
		$this->no_process = true;
		add_filter( 'sr_prepare_data', array($this, 'prepare_data'), 10, 2 );
		add_action( 'sr_after_save', array($this, 'save_data'), 10, 3);

	}

	public function prepare_form_html( $sr_form_data ) {
		ob_start();
		?>

		<h3>
			<?php echo $this->title; ?>
		</h3>

		<div>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label for="srform_paylater_enabled"
					                         title="<?php _e( "Enable Pay Later payment method. If your client choose this payment method, the reservation's status will be changed to 'Pending'. You will need to manually confirm the reservation when the client go to your hotel", 'solidres' ); ?>"><?php _e( 'Enable Pay Later', 'solidres' ); ?></label>
					</th>
					<td>
						<select name="srform[payments][paylater_enabled]" class=""
						        id="srform_paylater_enabled">
							<option value="0" <?php if ( isset( $sr_form_data->payments['paylater_enabled'] ) ) {
								echo $sr_form_data->payments['paylater_enabled'] == 0 ? 'selected' : '';
							} ?> ><?php _e( 'No', 'solidres' ); ?></option>
							<option value="1" <?php if ( isset( $sr_form_data->payments['paylater_enabled'] ) ) {
								echo $sr_form_data->payments['paylater_enabled'] == 1 ? 'selected' : '';
							} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="srform_paylater_is_default"
					                         title="<?php _e( 'Specify whether this payment method is the default one (pre-selected).', 'solidres' ); ?>"><?php _e( 'Default', 'solidres' ); ?></label>
					</th>
					<td>
						<select name="srform[payments][paylater_is_default]" class=""
						        id="srform_paylater_is_default">
							<option value="0" <?php if ( isset( $sr_form_data->payments['paylater_is_default'] ) ) {
								echo $sr_form_data->payments['paylater_is_default'] == 0 ? 'selected' : '';
							} ?> ><?php _e( 'No', 'solidres' ); ?></option>
							<option value="1" <?php if ( isset( $sr_form_data->payments['paylater_is_default'] ) ) {
								echo $sr_form_data->payments['paylater_is_default'] == 1 ? 'selected' : '';
							} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="srform_paylater_frontend_message"
					                         title="<?php _e( 'Enter the message that will be show in the front end, this field can be used to explain this payment method to your clients', 'solidres' ); ?>"><?php _e( 'Front-end message', 'solidres' ); ?></label>
					</th>
					<td><textarea class="srform_textarea" rows="5"
					              name="srform[payments][paylater_frontend_message]"
					              id="srform_paylater_frontend_message"><?php echo isset( $sr_form_data->payments['paylater_frontend_message'] ) ? $sr_form_data->payments['paylater_frontend_message'] : ''; ?></textarea>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
			
		<?php
		return ob_get_clean();
	}

	public function prepare_data($sr_form_data, $context) {
		if ( $context != 'solidres.edit.asset.data') {
			return;
		}

		if (!is_object($sr_form_data)) {
			return;
		}

		$solidres_config = new SR_Config( array( 'scope_id' => (int) $sr_form_data->id ) );
		if (!isset($sr_form_data->payments)) {
			$sr_form_data->payments = array();
		}

		$sr_form_data->payments     = array_merge( $sr_form_data->payments, array(
			'paylater_enabled'          => $solidres_config->get( 'payments/paylater/paylater_enabled' ),
			'paylater_is_default'       => $solidres_config->get( 'payments/paylater/paylater_is_default' ),
			'paylater_frontend_message' => $solidres_config->get( 'payments/paylater/paylater_frontend_message' ),
		) );

		return $sr_form_data;
	}

	public function save_data($sr_form_data, $context, $id) {
		if ( $context != 'solidres.edit.asset.data') {
			return;
		}
		$solidres_config = new SR_Config( array(
			'scope_id'       => (int) $id,
			'data_namespace' => 'payments/paylater'
		) );
		$solidres_config->set( array(
			'paylater_enabled'          => $sr_form_data->payments['paylater_enabled'],
			'paylater_is_default'       => $sr_form_data->payments['paylater_is_default'],
			'paylater_frontend_message' => $sr_form_data->payments['paylater_frontend_message'],
		) );
	}
}