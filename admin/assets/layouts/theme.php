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

<div id="asset_theme" class="postbox closed open">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Theme', 'solidres' ); ?></span></h3>
	<div class="inside">
		<?php
			if ( is_plugin_inactive( $hub ) ) {
				$message = __( '<strong>Notice:</strong> please install and activate <b>Solidres Hub</b> plugin at <a target="blank" href="' . admin_url() . 'plugins.php"><strong>Plugins manager</strong></a>. ', 'solidres' );
				SR_Helper::show_message( $message, 'trashed' );
			} else {
				$themes = new SR_Theme();
				$id = isset( $id ) ? $id : 0;
				echo $themes->render_list_themes( $id );
			}
		?>
	</div>
</div>