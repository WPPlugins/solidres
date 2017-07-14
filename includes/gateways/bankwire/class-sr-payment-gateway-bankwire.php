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

class Solidres_Payment_Gateway_Bankwire {

	public function __construct() {
		$this->identifier = 'bankwire';
		$this->defaultStatus = 5; // Confirmed
		$this->title = __( 'Bank Wire', 'solidres' );
		$this->description = __( 'Bank Wire', 'solidres' );
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
					<th scope="row"><label for="srform_bankwire_enabled"
					                       title="<?php _e( "Enable Bank Wire payment method. If your client choose this payment method, the reservation's status will be changed to 'Pending'. You will need to manually confirmed the reservation upon receiving the bank wire", 'solidres' ); ?>"><?php _e( 'Enable Bank Wire', 'solidres' ); ?></label>
					</th>
					<td>
						<select name="srform[payments][bankwire_enabled]" class=""
						        id="srform_bankwire_enabled">
							<option value="0" <?php if ( isset( $sr_form_data->payments['bankwire_enabled'] ) ) {
								echo $sr_form_data->payments['bankwire_enabled'] == 0 ? 'selected' : '';
							} ?> ><?php _e( 'No', 'solidres' ); ?></option>
							<option value="1" <?php if ( isset( $sr_form_data->payments['bankwire_enabled'] ) ) {
								echo $sr_form_data->payments['bankwire_enabled'] == 1 ? 'selected' : '';
							} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="srform_bankwire_is_default"
					                       title="<?php _e( 'Specify whether this payment method is the default one (pre-selected).', 'solidres' ); ?>"><?php _e( 'Default', 'solidres' ); ?></label>
					</th>
					<td>
						<select name="srform[payments][bankwire_is_default]" class=""
						        id="srform_bankwire_is_default">
							<option value="0" <?php if ( isset( $sr_form_data->payments['bankwire_is_default'] ) ) {
								echo $sr_form_data->payments['bankwire_is_default'] == 0 ? 'selected' : '';
							} ?> ><?php _e( 'No', 'solidres' ); ?></option>
							<option value="1" <?php if ( isset( $sr_form_data->payments['bankwire_is_default'] ) ) {
								echo $sr_form_data->payments['bankwire_is_default'] == 1 ? 'selected' : '';
							} ?> ><?php _e( 'Yes', 'solidres' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="srform_bankwire_accountname"
					                       title="<?php _e( 'Enter your bank account name to be used with the Bank Wire payment method', 'solidres' ); ?>"><?php _e( 'Account name', 'solidres' ); ?></label>
					</th>
					<td><input type="text" name="srform[payments][bankwire_accountname]"
					           value="<?php echo isset( $sr_form_data->payments['bankwire_accountname'] ) ? $sr_form_data->payments['bankwire_accountname'] : ''; ?>"
					           id="srform_bankwire_accountname" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="srform_bankwire_accountdetails"
					                       title="<?php _e( 'Enter your bank account details like Bank name, Bank branch, Bank address, Bank account number, CIF, Code etc. Please contact your bank if you do not know these information.', 'solidres' ); ?>"><?php _e( 'Account details', 'solidres' ); ?></label>
					</th>
					<td><textarea class="srform_textarea" rows="5" name="srform[payments][bankwire_accountdetails]"
					              id="srform_bankwire_accountdetails"><?php echo isset( $sr_form_data->payments['bankwire_accountdetails'] ) ? $sr_form_data->payments['bankwire_accountdetails'] : ''; ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="srform_bankwire_frontend_message"
					                       title="<?php _e( 'Enter the message that will be show in the front end, this field can be used to explain this payment method to your clients', 'solidres' ); ?>"><?php _e( 'Front-end message', 'solidres' ); ?></label>
					</th>
					<td><textarea class="srform_textarea" rows="5"
					              name="srform[payments][bankwire_frontend_message]"
					              id="srform_bankwire_frontend_message"><?php echo isset( $sr_form_data->payments['bankwire_frontend_message'] ) ? $sr_form_data->payments['bankwire_frontend_message'] : ''; ?></textarea>
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
			'bankwire_enabled' => $solidres_config->get( 'payments/bankwire/bankwire_enabled' ),
			'bankwire_is_default' => $solidres_config->get( 'payments/bankwire/bankwire_is_default' ),
			'bankwire_frontend_message' => $solidres_config->get( 'payments/bankwire/bankwire_frontend_message' ),
			'bankwire_accountname' => $solidres_config->get( 'payments/bankwire/bankwire_accountname' ),
			'bankwire_accountdetails' => $solidres_config->get( 'payments/bankwire/bankwire_accountdetails' )
		) );

		return $sr_form_data;
	}

	public function save_data($sr_form_data, $context, $id) {
		if ( $context != 'solidres.edit.asset.data') {
			return;
		}
		$solidres_config = new SR_Config( array(
			'scope_id'       => (int) $id,
			'data_namespace' => 'payments/bankwire'
		) );
		$solidres_config->set( array(
			'bankwire_enabled'          => $sr_form_data->payments['bankwire_enabled'],
			'bankwire_is_default'       => $sr_form_data->payments['bankwire_is_default'],
			'bankwire_frontend_message' => $sr_form_data->payments['bankwire_frontend_message'],
			'bankwire_accountname'      => $sr_form_data->payments['bankwire_accountname'],
			'bankwire_accountdetails'   => $sr_form_data->payments['bankwire_accountdetails'],
		) );
	}
}