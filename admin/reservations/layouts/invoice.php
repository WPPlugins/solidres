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
} ?>

<div id="asset_general_infomation" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Invoice', 'solidres' ); ?></span></h3>

	<div class="inside">
		<?php
		$invoice = 'solidres-invoice/solidres-invoice.php';
		if ( is_plugin_inactive( $invoice ) ) {
			if( ! current_user_can( 'solidres_partner' ) ) {
				$message = __( '<strong>Notice:</strong> please install and activate <b>Solidres Invoice</b> plugin at <a target="blank" href="' . admin_url() . 'plugins.php"><strong>Plugins manager</strong></a>. ', 'solidres' );
			} else {
				$message = __( '<strong>Notice:</strong> please contact with administrator install and activate <b>Solidres Invoice</b> plugin at <strong>Plugins manager</strong>.', 'solidres' );
			}
			SR_Helper::show_message( $message, 'trashed' );
		} else {
			include_once( WP_PLUGIN_DIR.'/solidres-invoice/admin/edit.php' );
		}
		?>
	</div>
</div>