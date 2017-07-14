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

function sr_systems() {
	wp_enqueue_style( 'solidres_skeleton' );

	$solidres_plugins = solidres_get_plugins();

	$solidres_widgets = solidres_get_widgets();

	wp_enqueue_script( 'jquery-ui-accordion' );

	if ( isset($_POST['solidres_check_updates']) ) :
		solidres_request_updates();
	endif;

	$available_updates = solidres_get_updates();

	?>

	<div id="wpbody">
		<h2 class="system-page-title"><?php _e( 'Solidres System', 'solidres' ); ?>

		<form action="" method="post" id="check_update_form">
			<button type="submit" name="solidres_check_updates" class="button">
				<span class="dashicons dashicons-update"></span>
				<?php _e( 'Check updates', 'solidres' ) ?>
			</button>
		</form>

		</h2>

		<div id="post-body" class="metabox-holder columns-2">
			<div class="sr_row">
				<div class="four columns">
					<img src="<?php echo plugins_url( 'solidres/assets/images/logo425x90.png' ); ?>"
					     alt="Solidres Logo" />
				</div>
				<div class="eight columns">
					<?php
						$message_version = __( 'Version ' . solidres_check_version( 'solidres/solidres.php' ) . '.Stable', 'solidres' );
						SR_Helper::show_message( $message_version );
					?>
				</div>
			</div>

			<h3>Sample data</h3>
			<?php
				if ( isset( $_POST['install_simple_data'] ) ) :
					solidres_install_simpledata();
					$message_update = __( 'Sample data installed success.', 'solidres' );
					SR_Helper::show_message( $message_update );
				endif;
				?>

				<form name="srform_install_simple_data" action="" method="post" id="srform">
					<?php
					global $wpdb;
					$asset_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}sr_reservation_assets" );
					if ( $asset_count > 0 ) :
						$message_error = __( 'Your Solidres tables already have data.', 'solidres' );
						SR_Helper::show_message( $message_error, 'error' );
					else : ?>
						<h4><?php _e( 'Warning', 'solidres' ); ?></h4>
						<?php _e( "You are about to install Solidres's sample data into your website. Sample data is the easiest way for you to get started and learn how to use Solidres. Before proceed please read the following notices:", 'solidres' ); ?>
						<ul>
							<li><?php _e( 'Always make a backup of your website first.', 'solidres' ); ?></li>
							<li><?php _e( 'Please make sure that you only install sample data right after the initial installation Solidres (when Solidres has no data).', 'solidres' ); ?></li>
							<li><?php _e( 'Do not install sample data twice because it will create duplicated entries in your databases.', 'solidres' ); ?></li>
						</ul>
						<input type="submit" name="install_simple_data" value="I understand and want to install the sample data"
						       class="srform_button install_simple_data">
					<?php endif ?>
				</form>

				<h3><?php _e( 'Plugins status', 'solidres' ); ?></h3>

				<div class="sr_row">
					<?php
					$breaking_p = 1;
					$plugin_total = 13;

					foreach ($solidres_plugins as $solidres_plugin) :
						if ( 1 == $breaking_p || round($plugin_total / 2) + 1 == $breaking_p) :
							echo '<div class="six columns"><table class="form-table widefat striped system-table"><tbody>';
						endif;
						$url         = admin_url('plugins.php');
						$plugin_path_absolute = WP_PLUGIN_DIR . '/solidres-' . $solidres_plugin . '/solidres-' . $solidres_plugin . '.php';
						$plugin_path_relative = 'solidres-' . $solidres_plugin . '/solidres-' . $solidres_plugin . '.php';
						$is_installed = false;
						if ( file_exists($plugin_path_absolute) ) :
							$is_installed = true;
						endif;
						?>
						<tr>
							<td>
								<a href="<?php echo $url; ?>">
									<?php echo 'solidres-' . $solidres_plugin ?>
								</a>
							</td>
							<td>
								<?php
								if ( $is_installed ) :
									$plugin_version = solidres_check_version( $plugin_path_relative );
									$is_active  = (bool) is_plugin_active( $plugin_path_relative );
									echo $is_active ? '<span class="label label-success">Version ' . $plugin_version . ' is enabled</span>' : '<span class="label label-warning">Version ' . $plugin_version . ' is not enabled</span>';
									if ( isset( $available_updates[ 'solidres-' . $solidres_plugin ] )
									     && version_compare( $available_updates[ 'solidres-' . $solidres_plugin ], $plugin_version, 'gt' )
									) :
										echo '<span class="new-update"> ' . sprintf( __( '<a href="%s" target="_blank">Version %s is available</a>', 'solidres' ), 'https://www.solidres.com/download/show-all-downloads', $available_updates[ 'solidres-' . $solidres_plugin ] ) . '</span>';
									endif;
								else :
									echo '<span class="label label-important">Not installed</span>';
								endif
								?>
							</td>
						</tr>
						<?php
						if ( (round($plugin_total / 2)) == $breaking_p || $plugin_total == $breaking_p) :
							echo '</tbody></table></div>';
						endif;
						$breaking_p ++;
					endforeach;

					?>
				</div>

				<h3><?php _e( 'Widget status', 'solidres' ); ?></h3>

				<div class="sr_row">
					<?php
					$breaking_p = 1;
					$widget_total = 7;

					foreach ($solidres_widgets as $solidres_widget) :

						if ( 1 == $breaking_p || round($widget_total / 2) + 1 == $breaking_p) :
							echo '<div class="six columns"><table class="form-table widefat striped system-table"><tbody>';
						endif;

						$url                 = admin_url('plugins.php');;
						if ( in_array( $solidres_widget, array( 'checkavailability', 'currency' ) ) ) {
							$plugin_path_absolute = WP_PLUGIN_DIR . '/solidres/solidres.php';
							$plugin_path_relative = 'solidres/solidres.php';
						} else {
							$plugin_path_absolute = WP_PLUGIN_DIR . '/solidres-' . $solidres_widget . '/solidres-' . $solidres_widget . '.php';
							$plugin_path_relative = 'solidres-' . $solidres_widget . '/solidres-' . $solidres_widget . '.php';
						}

						$is_installed = false;
						if ( file_exists($plugin_path_absolute) ) :
							$is_installed = true;
						endif;
					?>
						<tr>
							<td>
								<a href="<?php echo $url ?>">
									solidres-<?php echo $solidres_widget ?>
								</a>
							</td>
							<td>
								<?php
								if ( $is_installed ) :
									$plugin_version = solidres_check_version( $plugin_path_relative );
									$is_active  = (bool) is_plugin_active( $plugin_path_relative );
									echo $is_active ? '<span class="label label-success">Version ' . $plugin_version . ' is enabled</span>' : '<span class="label label-warning">Version ' . $plugin_version . ' is not enabled</span>';
									if ( isset( $available_updates[ 'solidres-' . $solidres_widget ] )
									     && version_compare( $available_updates[ 'solidres-' . $solidres_widget ], $plugin_version, 'gt' )
									) :
										echo '<span class="new-update"> ' . sprintf( __( '<a href="%s" target="_blank">Version %s is available</a>', 'solidres' ), 'https://www.solidres.com/download/show-all-downloads', $available_updates[ 'solidres-' . $solidres_widget ] ) . '</span>';
									endif;
								else :
									echo '<span class="label label-important">Not installed</span>';
								endif;
								?>
							</td>
						</tr>
					<?php
						if ( (round($widget_total / 2)) == $breaking_p || $widget_total == $breaking_p) :
							echo '</tbody></table></div>';
						endif;
						$breaking_p ++;
					endforeach; ?>
				</div>

				<h3><?php _e( 'System check list', 'solidres' ); ?></h3>

				<div class="sr_row">
					<table class="form-table widefat striped">

						<tbody>
						<tr>
							<td><?php _e( 'GD is enabled in your server', 'solidres' ); ?></td>
							<td><?php extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ? _e( '<span class="sr_enable">YES</span>', 'solidres' ) : _e( '<span class="sr_warning">NO</span>', 'solidres' ); ?>
							</td>
						</tr>
						<tr>
							<td><?php _e( '/wp-content/upload is writable?', 'solidres' ); ?></td>
							<td><?php
								$upload_dir = wp_upload_dir();
								is_writable( $upload_dir['basedir'] ) ? _e( '<span class="sr_enable">YES</span>', 'solidres' ) : _e( '<span class="sr_warning">NO</span>', 'solidres' ); ?></td>
						</tr>
						<?php if ( defined('SR_PLUGIN_INVOICE_ENABLED') ) : ?>
						<tr>
							<td><?php _e( '/wp-content/plugins/solidres-invoice/libraries/invoice is writable?', 'solidres' ); ?></td>
							<td><?php

								is_writable( ABSPATH . '/wp-content/plugins/solidres-invoice/libraries/invoice' ) ? _e( '<span class="sr_enable">YES</span>', 'solidres' ) : _e( '<span class="sr_warning">NO</span>', 'solidres' ); ?></td>
						</tr>
						<?php endif ?>
						<?php if (function_exists('curl_version')) : ?>
						<tr>
							<td>
								(Optional) Does my server support <a href="https://www.paypal-knowledge.com/infocenter/index?page=content&id=FAQ1914&expand=true&locale=en_US" target="_blank">the new Paypal's protocols</a> (TLS 1.2 and HTTP1.1)? If you don't use Paypal, just skip it.
							</td>
							<td>
								<?php
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, "https://tlstest.paypal.com/");
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								$result = curl_exec($ch);
								echo $result == 'PayPal_Connection_OK'
									? '<span class="sr_enable">YES</span>'
									: '<span class="sr_warning">NO</span>';
								curl_close($ch);
								?>
							</td>
						</tr>
						<?php endif ?>
						</tbody>
					</table>
				</div>


				<h3><?php _e( 'Database check list', 'solidres' ); ?></h3>

				<div class="sr_row">
					<table class="form-table widefat striped">

						<tbody>
						<tr>
							<td>
								Current Solidres database schema version
							</td>
							<td>
								<?php
								$schemaVersion = get_option( 'solidres_db_version' );
								if ( !empty( $schemaVersion ) && $schemaVersion == solidres()->version ) :
									echo '<span class="label label-success">' . $schemaVersion . '</span> Your database is in good state.';
								else :
									echo '<span class="label label-warning">No version found</span> If you are using Solidres pre-installed in some template\'s quickstart package, your quickstart package database could have missing entries which leads to this issue. You should contact them so that they can fix it for you. More info can be found in our <a href="http://www.solidres.com/support/frequently-asked-questions">FAQ - #30</a>';
								endif;
								?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>


				<h3><?php _e( 'Theme override check list', 'solidres' ); ?></h3>

				<div class="sr_row">

				<?php
				$theme_folder_iter = new DirectoryIterator(get_theme_root());
				foreach ($theme_folder_iter as $file_info) {
					if ($file_info->isDir() && !$file_info->isDot()) {
						$theme_list[] = $file_info->getBasename();
					}
				}
				$override_paths = array();
				foreach ( $theme_list as $theme_name ) {
					$theme_override_folder = get_theme_root() . '/' . $theme_name . '/solidres';
					if ( is_dir( $theme_override_folder ) ) {
						$theme_override_iter = new DirectoryIterator( $theme_override_folder );

						foreach ( $theme_override_iter as $override_folder ) {
							if ( $override_folder->isDir() && !$override_folder->isDot() ) {
								$override_paths[$theme_name][] = $override_folder->getRealPath();
							}
						}
					}
				}

				if ( !empty( $override_paths ) ) { ?>
				<div id="theme-override">
					<?php foreach ( $override_paths as $theme_name => $theme_override_paths ) { ?>
					<h3><?php echo $theme_name ?></h3>
					<div>
						<?php foreach ($theme_override_paths as $theme_override_path ) { ?>
							<p><?php echo $theme_override_path ?></p>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
				<script>
					jQuery(function($) {
						$( "#theme-override" ).accordion({
							heightStyle: "content",
							collapsible: true
						});
					});
				</script>
				<?php } else { ?>
					<div class="updated"><p><?php _e( 'You have no theme override for Solidres', 'solidres' ) ?></p></div>
				<?php }	?>

				</div>


		</div>
	</div>


<?php }