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

function sr_edit_country( $id ) {
	global $wpdb;
	$current_user = wp_get_current_user();
	$author_id    = $current_user->ID;
	$today        = date( 'Y-m-d H:i:s' );
	$action       = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$message      = isset( $_GET['message'] ) ? $_GET['message'] : '';
	$countries    = new SR_Country();

	if ( ! isset( $_POST['save_country'] ) ) {
		$sr_form_data = $countries->load( $id );
	} else {
		$sr_form_data = (object) $_POST['srform'];
		$columns      = array(
			'name'          => $sr_form_data->name,
			'code_2'        => $sr_form_data->code_2,
			'code_3'        => $sr_form_data->code_3,
			'state'         => $sr_form_data->state,
			'modified_by'   => $action == 'edit' ? $author_id : '0',
			'modified_date' => $action == 'edit' ? $today : '0000-00-00 00:00:00.000000',
		);

		$format = array(
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%s'
		);

		if ( $action != 'edit' ) {
			$columns['checked_out']      = 0;
			$columns['checked_out_time'] = '0000-00-00 00:00:00.000000';
			$columns['created_by']       = $author_id;
			$columns['created_date']     = $today;

			$format = array_merge( $format, array( '%d', '%s', '%d', '%s' ) );
		}

		if ( $action == 'edit' ) {
			$save_result = $wpdb->update( $wpdb->prefix . 'sr_countries', $columns, array( 'id' => $id ), $format );
		} else {
			$save_result = $wpdb->insert( $wpdb->prefix . 'sr_countries', $columns, $format );
		}

		if ( $save_result == true ) {
			if ( $action != 'edit' ) {
				$id = $wpdb->insert_id;
			}
			$message = $action == 'edit' ? 2 : 1;
			wp_redirect( admin_url( 'admin.php?page=sr-countries&action=edit&id=' . $id . '&message=' . $message ) );
			exit;
		}
	}
	?>

	<div class="wrap">
		<div id="wpbody">
			<?php
			if( isset( $save_result ) && $save_result == false ) {
				$message = $action == 'edit' ? __( 'Update country failed', 'solidres' ) : __( 'Add new country failed', 'solidres' );
				SR_Helper::show_message( $message, 'error' );
			}
			if ( $action == 'edit' ) :
				$text_message = $message == 1 ? __( 'Country published.', 'solidres' ) : __( 'Country updated.', 'solidres' );
				if ( $message != '' ) {
					SR_Helper::show_message( $text_message );
				} ?>
				<h2><?php _e( 'Edit country', 'solidres' ); ?> <a
						href="<?php echo admin_url( 'admin.php?page=sr-add-new-country' ); ?>"
						class="add-new-h2"><?php _e( 'Add New', 'solidres' ); ?></a>
				</h2>
			<?php else: ?>
				<h2><?php _e( 'Add new country', 'solidres' ); ?></h2>
			<?php endif; ?>
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" class="edit-form-section">
					<div id="namediv" class="stuffbox">
						<h3><label for="name"><?php _e( 'General infomartion', 'solidres' ); ?></label></h3>

						<div class="inside">
							<form name="srform_edit_country" action="" method="post" id="srform">
								<?php require( 'layouts/general.php' ); ?>
								<input type="submit" name="save_country" value="<?php _e( 'Save', 'solidres' ); ?>"
								       class="srform_button button button-primary button-large save_country">
							</form>
							<br>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }