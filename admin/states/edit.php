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

function sr_edit_state( $id ) {
	global $wpdb;
	$action     = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$message = isset( $_GET['message'] ) ? $_GET['message'] : '';
	$states = new SR_State();
	if ( ! isset( $_POST['save_state'] ) ) {
		$sr_form_data = $states->load( $id );
	} else {
		$sr_form_data = (object) $_POST['srform'];
		$columns      = array(
			'country_id' => $sr_form_data->country_id,
			'name'       => $sr_form_data->name,
			'code_2'     => $sr_form_data->code_2,
			'code_3'     => $sr_form_data->code_3,
			'state'      => $sr_form_data->state,
		);

		$format = array(
			'%d',
			'%s',
			'%s',
			'%s',
			'%d',
		);

		if ( $action == 'edit' ) {
			$save_result = $wpdb->update( $wpdb->prefix . 'sr_geo_states', $columns, array( 'id' => $id ), $format );
		} else {
			$save_result = $wpdb->insert( $wpdb->prefix . 'sr_geo_states', $columns, $format );
		}

		if ( $save_result == true ) {
			if ( $action != 'edit' ) {
				$id = $wpdb->insert_id;
			}
			$message = $action == 'edit' ? 2 : 1;
			wp_redirect( admin_url( 'admin.php?page=sr-states&action=edit&id=' . $id . '&message=' . $message ) );
			exit;
		}
	}
	?>
	<div class="wrap">
		<div id="wpbody">
			<?php
			if ( isset( $save_result ) && $save_result == false ) {
				$message = $action == 'edit' ? __( 'Update state failed', 'solidres' ) : __( 'Add new state failed', 'solidres' );
				SR_Helper::show_message( $message, 'error' );
			}
			if ( $action == 'edit' ) :
				$text_message = $message == 1 ? __( 'State published.', 'solidres' ) : __( 'State updated.', 'solidres' );
				if ( $message != '' ) {
				SR_Helper::show_message( $text_message );
				} ?>
				<h2><?php _e( 'Edit state', 'solidres' ); ?> <a
						href="<?php echo admin_url( 'admin.php?page=sr-add-new-state' ); ?>"
						class="add-new-h2"><?php _e( 'Add New', 'solidres' ); ?></a>
				</h2>
			<?php else: ?>
				<h2><?php _e( 'Add new state', 'solidres' ); ?></h2>
			<?php endif; ?>
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" class="edit-form-section">
					<div id="namediv" class="stuffbox">
						<h3><label for="name"><?php _e( 'General infomartion', 'solidres' ); ?></label></h3>

						<div class="inside">
							<form name="srform_edit_state" action="" method="post" id="srform">
								<?php require( 'layouts/general.php' ); ?>
								<input type="submit" name="save_state" value="<?php _e( 'Save', 'solidres' ); ?>"
								       class="srform_button button button-primary button-large save_state">
							</form>
							<br>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }