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

function sr_edit_category( $id ) {
	global $wpdb;
	$action     = isset( $_GET['action'] ) ? $_GET['action'] : '';
	$message    = isset( $_GET['message'] ) ? $_GET['message'] : '';
	$categories = new SR_Category();

	if ( ! isset( $_POST['save_category'] ) ) {
		$sr_form_data = $categories->load( $id );
	} else {
		$sr_form_data = (object) $_POST['srform'];
		if ( $action == 'edit' ) {
			$check_slug = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_categories WHERE slug = %s AND id != %d", $sr_form_data->slug, $id ) );
		} else {
			$check_slug = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_categories WHERE slug = %s", $sr_form_data->alias ) );
		}

		if ( $check_slug <= 0 ) {
			$columns = array(
				'name'      => $sr_form_data->name,
				'slug'      => $sr_form_data->slug,
				'state'     => $sr_form_data->state,
				'parent_id' => $sr_form_data->parent_id,
			);

			$format = array(
				'%s',
				'%s',
				'%d',
				'%d'
			);

			if ( $action == 'edit' ) {
				$save_result = $wpdb->update( $wpdb->prefix . 'sr_categories', $columns, array( 'id' => $id ), $format );
			} else {
				$save_result = $wpdb->insert( $wpdb->prefix . 'sr_categories', $columns, $format );
			}

			if ( $save_result == true ) {
				if ( $action != 'edit' ) {
					$id = $wpdb->insert_id;
				}
			}

			$message = $action == 'edit' ? 2 : 1;
			wp_redirect( admin_url( 'admin.php?page=sr-categories&action=edit&id=' . $id . '&message=' . $message ) );
			exit;
		}
	} ?>
	<div class="wrap">
		<div id="wpbody">
			<?php if( isset( $check_slug ) && $check_slug > 0 ) {
				$text_message = __( 'Category slug already exists.', 'solidres' );
				SR_Helper::show_message( $text_message, 'error' );
			}
			if( isset( $save_result ) && $save_result == false ) {
				$message = $action == 'edit' ? __( 'Update category failed', 'solidres' ) : __( 'Add new category failed', 'solidres' );
				SR_Helper::show_message( $message, 'error' );
			}
			if ( $action == 'edit' ) :
				$text_message = $message == 1 ? __( 'Category published.', 'solidres' ) : __( 'Category updated.', 'solidres' );
				if ( $message != '' ) {
					SR_Helper::show_message( $text_message );
				} ?>
				<h2><?php _e( 'Edit category', 'solidres' ); ?> <a
						href="<?php echo admin_url( 'admin.php?page=sr-add-new-category' ); ?>"
						class="add-new-h2"><?php _e( 'Add New', 'solidres' ); ?></a></h2>
			<?php else: ?>
				<h2><?php _e( 'Add new category', 'solidres' ); ?></h2>
			<?php endif; ?>

			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" class="edit-form-section">
					<div id="namediv" class="stuffbox">
						<h3><label for="name"><?php _e( 'General infomartion', 'solidres' ); ?></label></h3>

						<div class="inside">
							<form name="srform_edit_asset_category" action="" method="post" id="srform">
								<?php require( 'layouts/general.php' ); ?>
								<input type="submit" name="save_category"
								       value="<?php _e( 'Save', 'solidres' ); ?>"
								       class="srform_button button button-primary button-large save_category">
							</form>
							<br>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }