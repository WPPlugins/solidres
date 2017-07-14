<?php
/**
 * Solidres Admin.
 *
 * @class       SR_Admin
 * @author      Solidres
 * @category    Admin
 * @package     Solidres/Admin
 * @version     0.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Solidres_Admin_Menus' ) ) :

class Solidres_Admin_Menus {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_assets') );
		add_action( 'admin_menu', array( $this, 'add_menu_customers') );
		add_action( 'admin_menu', array( $this, 'add_menu_reservations') );
		add_action( 'admin_menu', array( $this, 'add_menu_reservations_user') );
		add_action( 'admin_menu', array( $this, 'add_menu_coupons_extras') );
		add_action( 'admin_menu', array( $this, 'add_menu_system') );
		add_action( 'admin_init', array( $this, 'add_nav_menu_meta_boxes' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
		$options_plugin = get_option('solidres_plugin');
		if (isset($options_plugin['enable_reservation_live_refresh']) && $options_plugin['enable_reservation_live_refresh'] == 1) {
			add_action( 'admin_print_footer_scripts', array( $this, 'count_unread_reservation' ) );
		}
		add_action( 'admin_print_footer_scripts', array( $this, 'count_updates' ) );
	}


	public function add_menu_assets() {
		global $menu;

		$menu[] = array( '', 'read', 'separator-solidres', '', 'wp-menu-separator solidres' );
		add_menu_page( 'Solidres Assets', 'Assets', 'edit_users', 'sr-assets', 'sr_assets', '', '25.26' );
		add_submenu_page( 'sr-assets', "Categories", "Categories", 'edit_users', 'sr-categories', 'sr_categories' );
		//add_submenu_page( 'sr-assets', 'Assets', 'Assets', 'edit_users', 'sr-assets', 'sr_assets' );
		add_submenu_page( 'sr-assets', 'Room types', 'Room types', 'edit_users', 'sr-room-types', 'sr_room_types' );
		add_submenu_page( 'sr-assets', 'Add new asset', 'Add new asset', 'edit_users', 'sr-add-new-asset', 'sr_edit_asset' );
		add_submenu_page( 'sr-assets', 'Add new category', 'Add new category', 'edit_users', 'sr-add-new-category', 'sr_edit_category' );
		add_submenu_page( 'sr-assets', 'Add new room type', 'Add new room type', 'edit_users', 'sr-add-new-room-type', 'sr_edit_room_type' );
	}


	public function add_menu_customers() {
		$users = 'solidres-user/solidres-user.php';
		if ( is_plugin_inactive( $users ) ) {
			//add_menu_page( 'Solidres Customers', 'Customers', 'edit_users', 'sr-customers', 'sr_customers_inactive', 'dashicons-groups', '25.27' );
		} else {
			add_users_page( 'User Groups', 'User Groups', 'edit_users', 'sr-user-groups', 'sr_user_groups' );
			add_submenu_page( 'sr-user-group', 'Add new user group', 'Add new user group', 'edit_users', 'sr-add-new-user-group', 'sr_edit_user_group' );
		}
	}


	public function add_menu_reservations() {
		add_menu_page( 'Solidres Reservations', 'Reservations', 'edit_users', 'sr-reservations', 'sr_reservations', 'dashicons-admin-network', '25.28' );
	}


	public function add_menu_reservations_user() {
		$users = 'solidres-user/solidres-user.php';
		if ( is_plugin_active( $users ) ) {
			if ( current_user_can( 'solidres_user' ) ) {
				add_menu_page( 'My Reservations', 'My Reservations', 'solidres_user', 'my-reservations', 'my_reservations', 'dashicons-admin-network', '25.29' );
			}
		}
	}


	public function add_menu_coupons_extras() {
		add_menu_page( 'Solidres Coupons & Extras', 'Coupons & Extras', 'edit_users', 'sr-coupons', 'sr_coupons', 'dashicons-list-view', '25.30' );
		add_submenu_page( 'sr-coupons', 'Coupons', 'Coupons', 'edit_users', 'sr-coupons', 'sr_coupons' );
		add_submenu_page( 'sr-coupons', 'Extras', 'Extras', 'edit_users', 'sr-extras', 'sr_extras' );
		add_submenu_page( 'sr-extras', 'Add new coupon', 'Add new coupon', 'edit_users', 'sr-add-new-coupon', 'sr_edit_coupon' );
		add_submenu_page( 'sr-extras', 'Add new extra', 'Add new extra', 'edit_users', 'sr-add-new-extra', 'sr_edit_extra' );
	}


	public function add_menu_system() {
		$limit_booking = 'solidres-limitbooking/solidres-limitbooking.php';
		$discount      = 'solidres-discount/solidres-discount.php';
		$users = 'solidres-user/solidres-user.php';
		$sr_currency = 'solidres-currency/solidres-currency.php';
		add_menu_page( 'Solidres System', 'System', 'edit_users', 'sr-currencies', 'sr_currencies', 'dashicons-admin-generic', '25.31' );
		add_submenu_page( 'sr-currencies', 'Currencies', 'Currencies', 'edit_users', 'sr-currencies', 'sr_currencies' );
		add_submenu_page( 'sr-currencies', 'Countries', 'Countries', 'edit_users', 'sr-countries', 'sr_countries' );
		add_submenu_page( 'sr-currencies', 'States', 'States', 'edit_users', 'sr-states', 'sr_states' );
		add_submenu_page( 'sr-currencies', 'Taxes', 'Taxes', 'edit_users', 'sr-taxes', 'sr_taxes' );

		if ( is_plugin_inactive( $users ) ) {
			add_submenu_page( 'sr-currencies', 'Employees', 'Employees', 'edit_users', 'sr-employees', 'sr_employees_inactive' );
		} else {
			add_submenu_page( 'sr-currencies', 'Employees', 'Employees', 'edit_users', 'users.php' );
		}

		if ( is_plugin_inactive( $limit_booking ) ) {
			add_submenu_page( 'sr-currencies', 'Limit bookings', 'Limit bookings', 'edit_users', 'sr-limit-bookings', 'sr_limit_bookings_inactive' );
		} else {
			add_submenu_page( 'sr-currencies', 'Limit bookings', 'Limit bookings', 'edit_users', 'sr-limit-bookings', 'sr_limit_bookings' );
			add_submenu_page( 'sr-countries', 'Add new limit booking', 'Add new limit booking', 'edit_users', 'sr-add-new-limit-booking', 'sr_edit_limit_booking' );
		}
		if ( is_plugin_active( $discount ) ) {
			add_submenu_page( 'sr-currencies', 'Discounts', 'Discounts', 'edit_users', 'sr-discounts', 'sr_discounts' );
			add_submenu_page( 'sr-countries', 'Add new discount', 'Add new discount', 'edit_users', 'sr-add-new-discount', 'sr_edit_discount' );
		}

		$update_info  = solidres_get_updates();
		$update_count = 0;
		if ( is_array( $update_info ) && ! empty( $update_info ) && ! isset( $update_info['data'] ) ) {
			$update_count = count( $update_info );
		}

		add_submenu_page( 'sr-currencies', 'System Info', 'System Info' . ($update_count > 0 ? ' <span class="update-plugins"><span title="'. sprintf( _n( '%s update found', '%s updates found', $update_count, 'solidres' ), $update_count )  .'" class="plugin-count">' . $update_count . '</span></span>' : ''), 'edit_users', 'sr-systems', 'sr_systems' );
		add_submenu_page( 'sr-currencies', 'Solidres Settings', 'Settings', 'edit_users', 'sr-options', 'sr_options' );
		add_submenu_page( 'sr-currencies', 'Add new currency', 'Add new currency', 'edit_users', 'sr-add-new-currency', 'sr_edit_currency' );
		if(is_plugin_active($sr_currency)){
			add_submenu_page( 'sr-currencies', 'Update currency', 'Update currency', 'edit_users', 'sr-update-currency', 'sr_currency_update' );
		}
		add_submenu_page( 'sr-countries', 'Add new country', 'Add new country', 'edit_users', 'sr-add-new-country', 'sr_edit_country' );
		add_submenu_page( 'sr-countries', 'Add new state', 'Add new state', 'edit_users', 'sr-add-new-state', 'sr_edit_state' );
		add_submenu_page( 'sr-countries', 'Add new tax', 'Add new tax', 'edit_users', 'sr-add-new-tax', 'sr_edit_tax' );
	}

	/**
	 * Add custom nav meta box
	 *
	 */
	public function add_nav_menu_meta_boxes() {
		add_meta_box( 'solidres_assets_nav_link', __( 'Assets', 'solidres' ), array( $this, 'nav_menu_assets' ), 'nav-menus', 'side', 'low' );
	}

	public function nav_menu_assets() {

		$solidres_assets = new SR_Asset();
		$assets = $solidres_assets->load_all();
	?>
		<div id="posttype-solidres-assets" class="posttypediv">
			<div id="tabs-panel-solidres-assets" class="tabs-panel tabs-panel-active">
				<ul id="solidres-assets-checklist" class="categorychecklist form-no-clear">
					<?php
					$i = -1;
					foreach ($assets as $asset) { ?>
					<li>
						<label class="menu-item-title">
							<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" /> <?php echo esc_html( apply_filters( 'solidres_asset_name', $asset->name ) ); ?>
						</label>
						<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
						<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_html( apply_filters( 'solidres_asset_name', $asset->name ) ); ?>" />
						<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="<?php echo esc_url( get_site_url() . '/' . $asset->alias ); ?>" />
						<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" />
					</li>
					<?php
						$i --;
					}
					?>
				</ul>
			</div>
			<p class="button-controls">
				<span class="list-controls">
					<a href="<?php echo admin_url( 'nav-menus.php?page-tab=all&selectall=1#posttype-solidres-endpoints' ); ?>" class="select-all"><?php _e( 'Select All', 'solidres' ); ?></a>
				</span>
				<span class="add-to-menu">
					<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'solidres' ); ?>" name="add-post-type-menu-item" id="submit-posttype-solidres-assets">
					<span class="spinner"></span>
				</span>
			</p>
		</div>
	<?php
	}

	public function menu_order( $menu_order ) {
		$solidres_menu_order = array();

		$solidres_separator = array_search( 'separator-solidres', $menu_order );

		foreach ( $menu_order as $index => $item ) {

			if ( ( ( 'sr-assets' ) == $item ) ) {
				$solidres_menu_order[] = 'separator-solidres';
				$solidres_menu_order[] = $item;
				unset( $menu_order[ $solidres_separator ] );
			} elseif ( !in_array( $item, array( 'separator-solidres' ) ) ) {
				$solidres_menu_order[] = $item;
			}

		}

		return $solidres_menu_order;
	}

	public function custom_menu_order() {
		return current_user_can( 'manage_options' );
	}

	public function count_unread_reservation() {
		$options_plugin = get_option('solidres_plugin');
		if ( wp_script_is( 'jquery', 'done' ) ) {
		?>
		<script>
			jQuery(function($) {
				intervalId = setInterval(function () {
					$.ajax({
						type: "GET",
						url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
						data: {
							action: "solidres_reservation_count_unread",
							security: '<?php echo wp_create_nonce( 'reservation-count-unread' ) ?>'
						},
						success: function (data) {
							if (data.count > 0) {
								$( "#toplevel_page_sr-reservations .wp-menu-name span" ).remove();
								$( "#toplevel_page_sr-reservations .wp-menu-name" ).append( ' <span class="update-plugins count-6"><span class="plugin-count">' + data.count + '</span></span>' );
							}
						},
						dataType: "JSON"
					});
				}, <?php echo (isset($options_plugin['reservation_live_refresh_interval']) ? $options_plugin['reservation_live_refresh_interval'] : 15) * 1000  ?>);
			});
		</script>
		<?php
		}
	}

	public function count_updates() {

		$update_info  = solidres_get_updates();
		$update_count = 0;
		if ( is_array( $update_info ) && ! empty( $update_info ) && ! isset( $update_info['data'] ) ) {
			$update_count = count( $update_info );
		}

		if ( $update_count > 0 && wp_script_is( 'jquery', 'done' ) ) {
			?>
			<script>
				jQuery(function ($) {
					console.log('abc');
					console.log($('#toplevel_page_sr-currencies'));
					$("#toplevel_page_sr-currencies .wp-menu-name ").append(' <span class="update-plugins"><span title="<?php echo sprintf( _n( '%s update found', '%s updates found', $update_count, 'solidres' ), $update_count )  ?>" class="plugin-count"><?php echo $update_count ?></span></span>');
				});
			</script>
			<?php
		}
	}
}

endif;

return new Solidres_Admin_Menus();