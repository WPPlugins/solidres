<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if (!defined('ABSPATH'))
{
	exit;
}

class SR_Helper
{

	public function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	/**
	 * View message
	 *
	 * @param        $message
	 * @param string $type
	 */
	public static function show_message($message, $type = 'updated')
	{
		echo '<div id="message" class="' . $type . ' below-h2"><p>' . $message . '</p></div>';
	}

	/**
	 * Get Status For ListView
	 *
	 * @param $status
	 *
	 * @return null|string
	 */
	public static function get_listview_state($status)
	{
		switch ($status)
		{
			case 'draft':
				$state = '0';
				break;
			case 'publish':
				$state = '1';
				break;
			case 'trash':
				$state = '-2';
				break;
			default:
				$state = null;
		}

		return $state;
	}

	/**
	 * View Tabel ListView
	 *
	 * @param $table
	 * @param $action
	 * @param $string_search
	 * @param $status
	 * @param $list_table_data
	 */
	public function listview($table, $action, $string_search, $status, $list_table_data)
	{
		$tables = array(
			'sr_reservation_assets' => array(
				'title'  => 'Assets',
				'addnew' => 'sr-add-new-asset',
				'page'   => 'sr-assets',
				'search' => 'search_assets'
			),
			'sr_categories'         => array(
				'title'  => 'Categories',
				'addnew' => 'sr-add-new-category',
				'page'   => 'sr-categories',
				'search' => 'search_asset_caterogies'
			),
			'sr_room_types'         => array(
				'title'  => 'Room types',
				'addnew' => 'sr-add-new-room-type',
				'page'   => 'sr-room-types',
				'search' => 'search_room_types'
			),
			'sr_coupons'            => array(
				'title'  => 'Coupons',
				'addnew' => 'sr-add-new-coupon',
				'page'   => 'sr-coupons',
				'search' => 'search_coupons'
			),
			'sr_extras'             => array(
				'title'  => 'Extras',
				'addnew' => 'sr-add-new-extra',
				'page'   => 'sr-extras',
				'search' => 'search_extras'
			),
			'sr_countries'          => array(
				'title'  => 'Countries',
				'addnew' => 'sr-add-new-country',
				'page'   => 'sr-countries',
				'search' => 'search_countries'
			),
			'sr_currencies'         => array(
				'title'  => 'Currencies',
				'addnew' => 'sr-add-new-currency',
				'update' => 'sr-update-currency',
				'page'   => 'sr-currencies',
				'search' => 'search_currencies'
			),
			'sr_geo_states'         => array(
				'title'  => 'States',
				'addnew' => 'sr-add-new-state',
				'page'   => 'sr-states',
				'search' => 'search_states'
			),
			'sr_taxes'              => array(
				'title'  => 'Taxes',
				'addnew' => 'sr-add-new-tax',
				'page'   => 'sr-taxes',
				'search' => 'search_taxes'
			),
			'sr_limit_bookings'     => array(
				'title'  => 'Limit bookings',
				'addnew' => 'sr-add-new-limit-booking',
				'page'   => 'sr-limit-bookings',
				'search' => 'search_limit_bookings'
			),
			'sr_customer_groups'    => array(
				'title'  => 'User Groups',
				'addnew' => 'sr-add-new-user-group',
				'page'   => 'sr-user-groups',
				'search' => 'search_user_groups'
			),
			'sr_discounts'          => array(
				'title'  => 'Discounts',
				'addnew' => 'sr-add-new-discount',
				'page'   => 'sr-discounts',
				'search' => 'search_discounts'
			),
			'sr_facilities'         => array(
				'title'  => 'Facilities',
				'addnew' => 'sr-add-new-facility',
				'page'   => 'sr-facilities',
				'search' => 'search_facilites'
			),
			'sr_themes'             => array(
				'title'  => 'Themes',
				'addnew' => 'sr-add-new-theme',
				'page'   => 'sr-themes',
				'search' => 'search_themes'
			),
			'sr_reservations'       => array(
				'title'  => 'Reservations',
				'addnew' => 'sr-reservations&action=amend',
				'page'   => 'sr-themes',
				'search' => 'search_themes'
			),
		);
		if (array_key_exists($table, $tables))
		{
			$query_default = "SELECT COUNT(*) FROM {$this->wpdb->prefix}$table WHERE state = ";
			$count_all     = $this->wpdb->get_var($query_default . '0 OR state = 1');
			$count_draft   = $this->wpdb->get_var($query_default . '0');
			$count_publish = $this->wpdb->get_var($query_default . '1');
			$count_trash   = $this->wpdb->get_var($query_default . '-2');
			if ($action != 'edit')
			{ ?>
				<div class="srtable">
					<div class="wrap">
						<div id="icon-users" class="icon32"><br/></div>
						<h2><?php _e($tables[$table]['title'], 'solidres'); ?>
							<a href="<?php echo admin_url('admin.php?page=' . $tables[$table]['addnew']); ?>"
							   class="add-new-h2"><?php _e('Add New', 'solidres'); ?></a>
							<?php if ($table == 'sr_currencies') : ?>
							<a href="<?php
								if(is_plugin_active('solidres-currency/solidres-currency.php')) :
									echo admin_url( 'admin.php?page=' . $tables[ $table ]['update'] ); ?>"
								   class="add-new-h2"><?php _e( 'Update exchange rate', 'solidres' ); ?></a>
								<?php else :
									echo 'https://www.solidres.com/'; ?>"
									class="add-new-h2"><?php _e( 'Update exchange rate (require Solidres Currency plugin)', 'solidres' ); ?></a>
								<?php endif;
								endif;
							if ($string_search != '')
							{ ?>
								<span
									class="subtitle"><?php printf(__('Search results for "%s"', 'solidres'), $string_search); ?></span>
							<?php } ?>
						</h2>

						<ul class="subsubsub">
							<li class="all">
								<a href="<?php echo admin_url('admin.php?page=' . $tables[$table]['page']); ?>" <?php echo $status == '' ? 'class="current"' : ''; ?>>
									<?php _e('All', 'solidres'); ?>
									<span class="count">(<?php echo $count_all; ?>)</span>
								</a>
							</li>
							<?php if ($count_publish > 0)
							{ ?>
								|
								<li class="publish">
									<a href="<?php echo admin_url('admin.php?page=' . $tables[$table]['page'] . '&status=publish'); ?>" <?php echo $status == 'publish' ? 'class="current"' : ''; ?>>
										<?php _e('Publish', 'solidres'); ?>
										<span class="count">(<?php echo $count_publish; ?>)</span>
									</a>
								</li>
							<?php }
							if ($count_draft > 0)
							{ ?>
								|
								<li class="draft">
									<a href="<?php echo admin_url('admin.php?page=' . $tables[$table]['page'] . '&status=draft'); ?>" <?php echo $status == 'draft' ? 'class="current"' : ''; ?>>
										<?php _e('Draft', 'solidres'); ?>
										<span class="count">(<?php echo $count_draft; ?>)</span>
									</a>
								</li>
							<?php }
							if ($count_trash > 0)
							{ ?>
								|
								<li class="trash">
									<a href="<?php echo admin_url('admin.php?page=' . $tables[$table]['page'] . '&status=trash'); ?>" <?php echo $status == 'trash' ? 'class="current"' : ''; ?>>
										<?php _e('Trash', 'solidres'); ?>
										<span class="count">(<?php echo $count_trash; ?>)</span>
									</a>
								</li>
							<?php } ?>
						</ul>

						<form id="plugins-filter" method="get"
						      action="<?php echo admin_url('admin.php?page=' . $tables[$table]['page']); ?>">
							<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
							<?php
							$list_table_data->search_box(__('Search', 'solidres'), $tables[$table]['search']);
							$list_table_data->display();
							?>
						</form>
					</div>
				</div>
			<?php }
		} ?>
	<?php }

	/**
	 * View status item of listview
	 *
	 * @param $status
	 *
	 * @return string|void
	 */
	public static function view_status($status)
	{
		switch ($status)
		{
			case 0:
				$published = __('Unpublished', 'solidres');
				break;
			case 1:
				$published = __('Published', 'solidres');
				break;
			default:
				$published = __('Trashed', 'solidres');
		}

		return $published;
	}

	/**
	 * Get asset category options with selected
	 *
	 * @param int $category_id
	 *
	 * @return string
	 */
	public static function render_list_category($category_id = 0)
	{
		global $wpdb;
		$asset_categories_options = '';
		$asset_categories         = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sr_categories");
		foreach ($asset_categories as $asset_category)
		{
			$selected_category = '';
			if ($asset_category->id == $category_id)
			{
				$selected_category = 'selected';
			}
			$asset_categories_options .= '<option ' . $selected_category . ' value="' . $asset_category->id . '">' . apply_filters( 'solidres_category_name', $asset_category->name ) . '</option>';
		}

		return $asset_categories_options;
	}

	/**
	 * Get country options with selected
	 *
	 * @param int $country_id
	 *
	 * @return string
	 */
	public static function render_list_country($country_id = 0)
	{
		global $wpdb;
		$countries         = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sr_countries WHERE state = 1");
		$countries_options = '';
		$countries_options .= '<option value="">' . __('Select country', 'solidres') . '</option>';
		foreach ($countries as $country)
		{
			$selected_country = '';
			if ($country->id == $country_id)
			{
				$selected_country = 'selected';
			}
			$countries_options .= '<option ' . $selected_country . ' value="' . $country->id . '">' . $country->name . '</option>';
		}

		return $countries_options;
	}

	/**
	 * Get geo state options with country selected
	 *
	 * @param int $country_id
	 * @param int $geo_state_id
	 *
	 * @return string
	 */
	public static function render_list_geo_state($country_id = 0, $geo_state_id = 0)
	{
		global $wpdb;
		$states             = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_geo_states WHERE country_id = %d", $country_id));
		$geo_states_options = '';
		$geo_states_options .= '<option value="">' . __('Select state', 'solidres') . '</option>';
		foreach ($states as $state)
		{
			$selected_state = '';
			if ($state->id == $geo_state_id)
			{
				$selected_state = 'selected';
			}
			$geo_states_options .= '<option ' . $selected_state . ' value="' . $state->id . '">' . $state->name . '</option>';
		}

		return $geo_states_options;
	}

	/**
	 * Get currency options with selected
	 *
	 * @param int $currency_id
	 *
	 * @return string
	 */
	public static function render_list_currency($currency_id = 0)
	{
		global $wpdb;
		$currencies         = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sr_currencies");
		$currencies_options = '';
		foreach ($currencies as $currency)
		{
			$selected_currency = '';
			if ($currency->id == $currency_id)
			{
				$selected_currency = 'selected';
			}
			$currencies_options .= '<option ' . $selected_currency . ' value="' . $currency->id . '">' . apply_filters( 'solidres_currency_name', $currency->currency_name ) . '</option>';
		}

		return $currencies_options;
	}

	/**
	 * Get tax options with selected
	 *
	 * @param int $tax_id
	 *
	 * @return string
	 */
	public static function render_list_tax($tax_id = 0)
	{
		global $wpdb;
		$taxes         = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sr_taxes");
		$taxes_options = '';
		foreach ($taxes as $tax)
		{
			$selected_tax = '';
			if ($tax->id == $tax_id)
			{
				$selected_tax = 'selected';
			}
			$taxes_options .= '<option ' . $selected_tax . ' value="' . $tax->id . '">' . $tax->name . '</option>';
		}

		return $taxes_options;
	}

	/**
	 * Get taxes options with country selected
	 *
	 * @param int $country_id
	 * @param int $tax_id
	 *
	 * @return string
	 */
	public static function render_list_tax_by_country($country_id = 0, $tax_id = 0)
	{
		global $wpdb;
		$taxes         = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_taxes WHERE country_id = %d", $country_id));
		$taxes_options = '';
		$taxes_options .= '<option value="">' . __('Select tax', 'solidres') . '</option>';
		foreach ($taxes as $tax)
		{
			$tax_rate     = $tax->rate * 100;
			$selected_tax = '';
			if ($tax->id == $tax_id)
			{
				$selected_tax = 'selected';
			}
			$taxes_options .= '<option ' . $selected_tax . ' value="' . $tax->id . '">' . $tax->name . ' (' . $tax_rate . '%)</option>';
		}

		return $taxes_options;
	}

	/**
	 * Get taxes options with country selected
	 *
	 * @param int $user_group_id
	 *
	 * @return string
	 */
	public static function render_list_usergroup($user_group_id = 0)
	{
		global $wpdb;
		$groups         = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sr_customer_groups");
		$groups_options = '';
		foreach ($groups as $group)
		{
			$selected_group = '';
			if ($group->id == $user_group_id)
			{
				$selected_group = 'selected';
			}
			$groups_options .= '<option ' . $selected_group . ' value="' . $group->id . '">' . $group->name . '</option>';
		}

		return $groups_options;
	}

	/**
	 * Get Asset Rating with selected
	 *
	 * @param int $rating
	 *
	 * @return string
	 */
	public static function render_list_rating($rating = 0)
	{
		$rating_options = '';
		for ($r = 0; $r <= 5; $r++)
		{
			$selected = '';
			$star     = sprintf(_n('1 star', '%d stars', $r, 'solidres'), $r);
			if ($rating == $r)
			{
				$selected = 'selected';
			}
			$rating_options .= '<option value="' . $r . '" ' . $selected . ' >' . $star . '</option>';
		}

		return $rating_options;
	}

	/**
	 * Get created by with selected
	 *
	 * @param int $created_by
	 *
	 * @return string
	 */
	public static function render_created_by($created_by = 0)
	{
		//get user list
		$users = get_users();
		//Get Created by options
		$created_by_options = '';
		foreach ($users as $user)
		{
			$selected = '';
			if ($created_by == $user->ID)
			{
				$selected = 'selected';
			}
			$created_by_options .= '<option value="' . $user->ID . '" ' . $selected . '>' . $user->display_name . '</option>';
		}

		return $created_by_options;
	}

	/**
	 * Get images from Gallery with reservation asset_id
	 *
	 * @param int $reservation_asset_id
	 *
	 * @return string
	 */
	public static function get_images_gallery_asset($reservation_asset_id = 0)
	{
		global $wpdb;
		$images_gallery_view = '';
		$get_images_gallery  = $wpdb->get_results('SELECT t1.*, t2.guid as img_url FROM ' . $wpdb->prefix . 'sr_media_reservation_assets_xref t1 LEFT JOIN ' . $wpdb->posts . ' t2 ON t1.media_id = t2.ID WHERE t1.reservation_asset_id = ' . $reservation_asset_id . ' ORDER BY t1.weight ASC');
		foreach ($get_images_gallery as $image)
		{
			$images_gallery_view .= '<div class="gallery_img_wrap">';
			$images_gallery_view .= '<img src="' . $image->img_url . '" id="images_' . $image->media_id . '" alt="' . $image->media_id . '" />';
			$images_gallery_view .= '<input type="hidden" name="srform[mediaId][]" value="' . $image->media_id . '" >';
			$images_gallery_view .= '<div href="#" class="delete_img" title="Delete image" id="' . $image->media_id . '"></div>';
			$images_gallery_view .= '</div>';
		}

		return $images_gallery_view;
	}

	/**
	 * Get data from Room types
	 *
	 * @param int $reservation_asset_id
	 *
	 * @return string
	 */
	public static function get_room_type_asset($reservation_asset_id = 0)
	{
		global $wpdb;
		$room_type_data = '';
		$get_room_types = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_room_types WHERE reservation_asset_id = %d", $reservation_asset_id));
		if ($get_room_types == null)
		{
			$room_type_data .= '<tr><td>' . __('No room type available', 'solidres') . '</td></tr>';
		}
		else
		{
			$room_type_data .= '<thead>';
			$room_type_data .= '<tr>';
			$room_type_data .= '<th>' . __('State', 'solidres') . '</th>';
			$room_type_data .= '<th>' . __('Name', 'solidres') . '</th>';
			$room_type_data .= '<th>#</th>';
			$room_type_data .= '</tr>';
			$room_type_data .= '</thead>';
			foreach ($get_room_types as $room_type)
			{
				$occupancy = $room_type->occupancy_adult + $room_type->occupancy_child;
				$checked   = $room_type->state == 1 ? 'checked' : '';
				$room_type_data .= '<tr>';
				$room_type_data .= '<td><input type="checkbox" ' . $checked . ' disabled ></td>';
				$room_type_data .= '<td>' . apply_filters( 'solidres_roomtype_name', $room_type->name ) . '</td>';
				$room_type_data .= '<td>' . $occupancy . '</td>';
				$room_type_data .= '</tr>';
			}
		}

		return $room_type_data;
	}

	/**
	 * Get data from Extras
	 *
	 * @param int $reservation_asset_id
	 * @param int $currency_id
	 *
	 * @return string
	 */
	public static function get_extra_asset($reservation_asset_id = 0, $currency_id = 0)
	{
		global $wpdb;
		$extra_data = '';
		$get_extras = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_extras WHERE reservation_asset_id = %d", $reservation_asset_id));
		if ($get_extras == null)
		{
			$extra_data .= '<tr><td>' . __('No extra available', 'solidres') . '</td></tr>';
		}
		else
		{
			$extra_data .= '<thead>';
			$extra_data .= '<tr>';
			$extra_data .= '<th>' . __('State', 'solidres') . '</th>';
			$extra_data .= '<th>' . __('Name', 'solidres') . '</th>';
			$extra_data .= '<th>' . __('Price', 'solidres') . '</th>';
			$extra_data .= '</tr>';
			$extra_data .= '</thead>';
			foreach ($get_extras as $extra)
			{
				$extra_price = new SR_Currency(0, $currency_id);
				$extra_price->set_value(isset ($extra->price) ? $extra->price : 0);
				$checked = $extra->state == 1 ? 'checked' : '';
				$extra_data .= '<tr>';
				$extra_data .= '<td><input type="checkbox" ' . $checked . ' disabled ></td>';
				$extra_data .= '<td>' . apply_filters( 'solidres_extra_name', $extra->name ) . '</td>';
				$extra_data .= '<td>' . $extra_price->format() . '</td>';
				$extra_data .= '</tr>';
			}
		}

		return $extra_data;
	}

	/**
	 * Get parent category data from asset category with selected
	 *
	 * @param int $parent_id
	 *
	 * @return string
	 */
	public static function render_list_parent_caterogy($parent_id = 0)
	{
		global $wpdb;
		$asset_categories_options = '';
		if (isset($_GET['id']))
		{
			$asset_categories = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_categories WHERE id != %d", $_GET['id']));
		}
		else
		{
			$asset_categories = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sr_categories");
		}
		foreach ($asset_categories as $asset_category)
		{
			$selected_asset_category = '';
			if ($asset_category->id == $parent_id)
			{
				$selected_asset_category = 'selected';
			}
			$asset_categories_options .= '<option value="' . $asset_category->id . '" ' . $selected_asset_category . ' >' . $asset_category->name . '</option>';
		}

		return $asset_categories_options;
	}

	/**
	 * Get reservation asset with selected
	 *
	 * @param int $reservation_asset_id
	 * @param int $partner_id
	 *
	 * @return string
	 */
	public static function render_list_asset($reservation_asset_id = 0, $partner_id = 0)
	{
		global $wpdb;
		$reservation_assets_options = '';
		if ($partner_id > 0)
		{
			$reservation_assets = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_reservation_assets WHERE partner_id = %d", $partner_id));
		}
		else
		{
			$reservation_assets = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sr_reservation_assets");
		}
		foreach ($reservation_assets as $reservation_asset)
		{
			$selected_reservation_asset = '';
			if ($reservation_asset->id == $reservation_asset_id)
			{
				$selected_reservation_asset = 'selected';
			}
			$reservation_assets_options .= '<option ' . $selected_reservation_asset . ' value="' . $reservation_asset->id . '">' . apply_filters( 'solidres_asset_name', $reservation_asset->name ) . '</option>';
		}

		return $reservation_assets_options;
	}


	/**
	 * Get occupancy max options with selected
	 *
	 * @param int $occupancy_max
	 *
	 * @return string
	 */
	public static function render_list_occupancy_max($occupancy_max = 0)
	{
		$occupancy_max_options = '';
		for ($i = 0; $i <= 10; $i++)
		{
			$selected_max_room = '';
			if ($occupancy_max == $i)
			{
				$selected_max_room = 'selected';
			}
			$occupancy_max_options .= '<option ' . $selected_max_room . ' value="' . $i . '">' . $i . '</option>';
		}

		return $occupancy_max_options;
	}

	/**
	 * Get occupancy adult options with selected
	 *
	 * @param $occupancy_adult
	 *
	 * @return string
	 */
	public static function render_list_occupancy_adult($occupancy_adult = 0)
	{
		$occupancy_adult_options = '';
		for ($i = 0; $i <= 10; $i++)
		{
			$selected_adult_room = '';
			if ($occupancy_adult == $i)
			{
				$selected_adult_room = 'selected';
			}
			$occupancy_adult_options .= '<option ' . $selected_adult_room . ' value="' . $i . '">' . $i . '</option>';
		}

		return $occupancy_adult_options;
	}

	/**
	 * Get occupancy child options with selected
	 *
	 * @param $occupancy_child
	 *
	 * @return string
	 */
	public static function render_list_occupancy_child($occupancy_child = 0)
	{
		$occupancy_child_options = '';
		for ($j = 0; $j <= 10; $j++)
		{
			$selected_child_room = '';
			if ($occupancy_child == $j)
			{
				$selected_child_room = 'selected';
			}
			$occupancy_child_options .= '<option ' . $selected_child_room . ' value="' . $j . '">' . $j . '</option>';
		}

		return $occupancy_child_options;
	}

	/**
	 * Get standard tariff data
	 *
	 * @param null $room_type_id
	 *
	 * @return string
	 */
	public static function get_standard_tariff($room_type_id = null)
	{
		global $wpdb;
		$get_tariff_details = array();
		$get_tariffs_info   = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_tariffs WHERE valid_from = '0000-00-00' AND valid_to = '0000-00-00' AND room_type_id = %d", $room_type_id));
		if (!empty($get_tariffs_info))
		{
			$get_tariff_details = $wpdb->get_results($wpdb->prepare("SELECT price FROM {$wpdb->prefix}sr_tariff_details WHERE tariff_id = %d", $get_tariffs_info->id));
		}
		$standard_tariff_box = '';
		$dayMapping          = array(
			'0' => 'sun',
			'1' => 'mon',
			'2' => 'tue',
			'3' => 'wed',
			'4' => 'thu',
			'5' => 'fri',
			'6' => 'sat'
		);
		for ($i = 0; $i < 7; $i++)
		{
			$standard_tariff_box .= '<div class="stariff_item">';
			$standard_tariff_box .= '<p>' . ucfirst($dayMapping[$i]) . '</p>';
			$standard_tariff_box .= '<input type="number" name="srform[default_tariff][' . $i . ']" size="30" value="' . (!empty($get_tariff_details) ? $get_tariff_details[$i]->price : '') . '" id="srform_default_tariff_' . $i . '" required >';
			$standard_tariff_box .= '</div>';
		}

		return $standard_tariff_box;
	}

	/**
	 * Get coupons group with selected
	 *
	 * @param int $reservation_asset_id
	 * @param int $id
	 *
	 * @return string
	 */
	public static function render_coupons_group($reservation_asset_id = 0, $id = 0)
	{
		global $wpdb;
		$get_page                       = $_GET['page'];
		$check_reservation_exist_coupon = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}sr_coupons WHERE reservation_asset_id = %d", $reservation_asset_id));
		$coupon_groupbox                = '';
		if ($get_page == 'add_new_room_type')
		{
			$coupon_groupbox .= __( 'Select a reservation asset to see available coupons.', 'solidres' );
		}
		else
		{
			if ($check_reservation_exist_coupon > 0)
			{
				$get_coupons         = $wpdb->get_results($wpdb->prepare("SELECT id, coupon_name FROM {$wpdb->prefix}sr_coupons WHERE reservation_asset_id = %d", $reservation_asset_id));
				$get_checked_coupons = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_room_type_coupon_xref WHERE room_type_id = %d", $id));
				if (current_user_can('solidres_partner'))
				{
					$page = 'sr-hub-coupons';
				}
				else
				{
					$page = 'sr-coupons';
				}
				foreach ($get_coupons as $get_coupon)
				{
					$checked_coupons = '';
					foreach ($get_checked_coupons as $get_checked_coupon)
					{
						if ($get_checked_coupon->coupon_id == $get_coupon->id)
						{
							$checked_coupons = 'checked';
						}
					}
					$coupon_groupbox .= '<input type="checkbox" ' . $checked_coupons . ' name="srform[coupons][]" value="' . $get_coupon->id . '"/><a href="' . admin_url("admin.php?page=" . $page . "&action=edit&id=" . $get_coupon->id) . '" target="_blank">' . apply_filters( 'solidres_coupon_name', $get_coupon->coupon_name ) . '</a><br>';
				}
			}
			else
			{
				$coupon_groupbox .= __( 'No coupon available', 'solidres' );
			}
		}

		return $coupon_groupbox;
	}

	/**
	 * Get extra group with selected
	 *
	 * @param int $reservation_asset_id
	 * @param int $id
	 *
	 * @return string
	 */
	public static function render_extras_group($reservation_asset_id = 0, $id = 0)
	{
		global $wpdb;
		$get_page                      = $_GET['page'];
		$check_reservation_exist_extra = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}sr_extras WHERE reservation_asset_id = %d", $reservation_asset_id));
		$extra_groupbox                = '';
		if ($get_page == 'add_new_room_type')
		{
			$extra_groupbox .= __( 'Select a reservation asset to see available extra items.', 'solidres' );
		}
		else
		{
			if ($check_reservation_exist_extra > 0)
			{
				$get_extras         = $wpdb->get_results($wpdb->prepare("SELECT id, name FROM {$wpdb->prefix}sr_extras WHERE reservation_asset_id = %d", $reservation_asset_id));
				$get_checked_extras = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_room_type_extra_xref WHERE room_type_id = %d", $id));
				if (current_user_can('solidres_partner'))
				{
					$page = 'sr-hub-extras';
				}
				else
				{
					$page = 'sr-extras';
				}
				foreach ($get_extras as $get_extra)
				{
					$checked_extras = '';
					foreach ($get_checked_extras as $get_checked_extra)
					{
						if ($get_checked_extra->extra_id == $get_extra->id)
						{
							$checked_extras = 'checked';
						}
					}
					$extra_groupbox .= '<input type="checkbox" ' . $checked_extras . ' name="srform[extras][]" value="' . $get_extra->id . '"/><a href="' . admin_url("admin.php?page=" . $page . "&action=edit&id=" . $get_extra->id) . '" target="_blank">' . apply_filters( 'solidres_extra_name', $get_extra->name ) . '</a><br>';
				}
			}
			else
			{
				$extra_groupbox .= __( 'No extra available', 'solidres' );
			}
		}

		return $extra_groupbox;
	}

	/**
	 * Get images from Gallery with reservation asset_id
	 *
	 * @param $room_type_id
	 *
	 * @return string
	 */
	public static function get_images_gallery_roomtype($room_type_id)
	{
		global $wpdb;
		$images_gallery_view = '';
		$get_images_gallery  = $wpdb->get_results($wpdb->prepare("SELECT t1.*, t2.guid as img_url FROM {$wpdb->prefix}sr_media_roomtype_xref t1 LEFT JOIN $wpdb->posts t2 ON t1.media_id = t2.ID WHERE t1.room_type_id = %d ORDER BY t1.weight ASC", $room_type_id));
		foreach ($get_images_gallery as $image)
		{
			$images_gallery_view .= '<div class="gallery_img_wrap">';
			$images_gallery_view .= '<img src="' . $image->img_url . '" id="images_' . $image->media_id . '" alt="' . $image->media_id . '" />';
			$images_gallery_view .= '<input type="hidden" name="srform[mediaId][]" value="' . $image->media_id . '" >';
			$images_gallery_view .= '<div href="#" class="delete_img" title="Delete image" id="' . $image->media_id . '"></div>';
			$images_gallery_view .= '</div>';
		}

		return $images_gallery_view;
	}

	/**
	 * Get Assert Custom field with reservation asset_id
	 *
	 * @param int $room_type_id
	 *
	 * @return string
	 */
	public static function get_rooms_of_room_type($room_type_id = 0)
	{
		global $wpdb;
		$get_rooms = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}sr_rooms WHERE room_type_id = %d", $room_type_id));
		$room_view = '';
		foreach ($get_rooms as $room)
		{
			$room_view .= '<tr id="room_' . strtolower($room->label) . '" class="room_item">';
			$room_view .= '<td class="first"><input type="text" name="srform[rooms][' . $room->id . ']" size="20" value="' . $room->label . '" class="room_label" alt="' . $room->id . '" placeholder="Enter room label"></td>';
			$room_view .= '<td><a data-room_id="' . $room->id . '" href="" class="delete_room" title="Delete Room"><img src="' . solidres()->plugin_url() . '/assets/images/close_btn.png" alt="Delete Room"/></a></td>';
			$room_view .= '</tr>';
		}

		return $room_view;
	}

	/**
	 * Get generic list
	 *
	 * @param        $input_array
	 * @param array  $attributes
	 * @param string $selected
	 *
	 * @return string
	 */
	public static function get_generic_list($input_array, $attributes = array(), $selected = '')
	{
		$html = '';
		$attr = '';
		foreach ($attributes as $key => $value)
		{
			$attr .= $key . '="' . $value . '" ';
		}
		$html .= "<select $attr>";
		foreach ($input_array as $value => $text)
		{
			$selected_attr = '';
			if ($selected == $value)
			{
				$selected_attr = 'selected="selected"';
			}
			$html .= '<option ' . $selected_attr . ' value="' . $value . '">' . $text . '</option>';
		}
		$html .= '</select>';

		return $html;
	}

	public static function get_list_payment_plugins($asset_id)
	{
		$payment_offline      = array();
		$payment_paypal       = array();
		$payment_authorizenet = array();
		if (class_exists('SR_Payment_Offline'))
		{
			$payment_offline = SR_Payment_Offline::on_reservation_asset_prepare_data($asset_id);
		}
		if (class_exists('SR_Payment_Paypal'))
		{
			$payment_paypal = SR_Payment_Paypal::on_reservation_asset_prepare_data($asset_id);
		}
		if (class_exists('SR_Payment_Authorizenet'))
		{
			$payment_authorizenet = SR_Payment_Authorizenet::on_reservation_asset_prepare_data($asset_id);
		}
		$plugins        = array(
			'offline'      => array(
				'plugin'      => 'solidres-offline/solidres-offline.php',
				'name'        => __('Offline', 'solidres'),
				'description' => isset($payment_offline['offline_frontend_message']) ? $payment_offline['offline_frontend_message'] : '',
				'template'    => WP_PLUGIN_DIR . '/solidres-offline/includes/field.php',
				'form'        => '',
				'tab'         => WP_PLUGIN_DIR . '/solidres-offline/includes/tab.php',
			),
			'paypal'       => array(
				'plugin'      => 'solidres-paypal/solidres-paypal.php',
				'name'        => __('Paypal', 'solidres'),
				'description' => isset($payment_paypal['paypal_frontend_message']) ? $payment_paypal['paypal_frontend_message'] : '',
				'template'    => '',
				'form'        => WP_PLUGIN_DIR . '/solidres-paypal/includes/form.php',
				'tab'         => WP_PLUGIN_DIR . '/solidres-paypal/includes/tab.php',
			),
			'authorizenet' => array(
				'plugin'      => 'solidres-authorizenet/solidres-authorizenet.php',
				'name'        => __('Authorize.net', 'solidres'),
				'description' => isset($payment_authorizenet['authorizenet_frontend_message']) ? $payment_authorizenet['authorizenet_frontend_message'] : '',
				'template'    => WP_PLUGIN_DIR . '/solidres-authorizenet/includes/field.php',
				'form'        => '',
				'tab'         => WP_PLUGIN_DIR . '/solidres-authorizenet/includes/tab.php',
			)
		);
		$plugins_active = array();
		foreach ($plugins as $id => $value)
		{
			$file_path = WP_PLUGIN_DIR . '/' . $value['plugin'];
			if (file_exists($file_path))
			{
				if (is_plugin_active($value['plugin']))
				{
					$plugins_active[$id] = $value;
				}
			}
		}

		return $plugins_active;
	}

	public static function loadTabs($tabs)
	{
		$tab_title   = array();
		$tab_content = array();

		foreach ($tabs as $key => $tab)
		{
			$tab_title[]   = '<li role="presentation"' . ($key === 0 ? ' class="active"' : '') . '><a href="#sr-tab-' . ($key + 1) . '" aria-controls="home" role="tab" data-toggle="tab">' . $tab['title'] . '</a></li>';
			$tab_content[] = '<div role="tabpanel" class="tab-pane' . ($key === 0 ? ' active' : '') . '" id="sr-tab-' . ($key + 1) . '">' . $tab['content'] . '</div>';
		}
		?>
		<div class="main-tab">
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<?php echo join("\n", $tab_title); ?>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<?php echo join("\n", $tab_content); ?>
			</div>

		</div>
	<?php }

	public static function getStateValue($status)
	{
		switch (strtolower($status))
		{
			case 'restore':
			case 'untrash':
				return 1;
			case 'trash':
			case 'inactive':
				return 0;
			default:
				return -1;
		}
	}
}