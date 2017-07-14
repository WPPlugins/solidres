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

add_action('admin_init', 'solidres_options_init');
function solidres_options_init()
{
	register_setting('solidres_plugin', 'solidres_plugin');
	register_setting('solidres_tariff', 'solidres_tariff');
	register_setting('solidres_currency', 'solidres_currency');
	register_setting('solidres_invoice', 'solidres_invoice');
	register_setting('solidres_pages', 'solidres_pages');
	register_setting('solidres_tools', 'solidres_tools');
	register_setting('solidres_feedback', 'solidres_feedback');

	do_action('sr_option_init');
}

function sr_options()
{
	global $wpdb;
	if (!isset($_REQUEST['settings-updated']))
	{
		$_REQUEST['settings-updated'] = false;
	}

	flush_rewrite_rules();

	$currencies = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sr_currencies");
	foreach ($currencies as $currency)
	{
		$currencies_options_array[$currency->id] = array(
			'value' => $currency->id,
			'label' => $currency->currency_name
		);
	}
	$default_gallery_options        = array(
		'simple_gallery' => array(
			'value' => 'simple_gallery',
			'label' => __('simple_gallery', 'solidres')
		),
	);
	$yes_no_options                 = array(
		'1' => array(
			'value' => '1',
			'label' => __('Yes', 'solidres')
		),
		'0' => array(
			'value' => '0',
			'label' => __('No', 'solidres')
		),
	);
	$reservation_state              = array(
		'0' => array(
			'value' => '0',
			'label' => __('Pending arrival', 'solidres')
		),
		'1' => array(
			'value' => '1',
			'label' => __('Checked-in', 'solidres')
		),
		'2' => array(
			'value' => '2',
			'label' => __('Checked-out', 'solidres')
		),
		'3' => array(
			'value' => '3',
			'label' => __('Closed', 'solidres')
		),
		'4' => array(
			'value' => '4',
			'label' => __('Canceled', 'solidres')
		),
		'5' => array(
			'value' => '5',
			'label' => __('Confirmed', 'solidres')
		),
	);
	$curency_format_pattern_options = array(
		'1'  => array(
			'value' => '1',
			'label' => __('X0,000.00', 'solidres')
		),
		'2'  => array(
			'value' => '2',
			'label' => __('0 000,00X', 'solidres')
		),
		'3'  => array(
			'value' => '3',
			'label' => __('X0.000,00', 'solidres')
		),
		'4'  => array(
			'value' => '4',
			'label' => __('0,000.00X', 'solidres')
		),
		'5'  => array(
			'value' => '5',
			'label' => __('0 000.00X', 'solidres')
		),
		'6'  => array(
			'value' => '6',
			'label' => __('X 0,000.00', 'solidres')
		),
		'7'  => array(
			'value' => '7',
			'label' => __('0 000,00 X', 'solidres')
		),
		'8'  => array(
			'value' => '8',
			'label' => __('X 0.000,00', 'solidres')
		),
		'9'  => array(
			'value' => '9',
			'label' => __('0,000.00 X', 'solidres')
		),
		'10' => array(
			'value' => '10',
			'label' => __('0 000.00 X', 'solidres')
		),
	);
	$code_or_symbol_options         = array(
		'code' => array(
			'value' => 'code',
			'label' => __('Code', 'solidres')
		),
		'sign' => array(
			'value' => 'sign',
			'label' => __('Symbol', 'solidres')
		),
	);
	$auto_update_exchange_rate      = array(
		'0' => array(
			'value' => 'hourly',
			'label' => __('hourly', 'solidres')
		),
		'1' => array(
			'value' => 'twicedaily',
			'label' => __('twicedaily', 'solidres')
		),
		'2' => array(
			'value' => 'daily',
			'label' => __('daily', 'solidres')
		),
		'3' => array(
			'value' => '0',
			'label' => __('Not set', 'solidres')
		)
	);
	?>

    <div class="wrap">
		<?php echo "<h2>" . __(' Solidres Options', 'solidres') . "</h2>"; ?>
		<?php if (false !== $_REQUEST['settings-updated']) : ?>
            <div class="updated fade"><p><strong><?php _e('Options saved', 'solidres'); ?></strong></p></div>
		<?php endif; ?>
		<?php
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'plugins';
		if (isset($_GET['tab']))
		{
			$active_tab = $_GET['tab'];
		}
		?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=sr-options&amp;tab=plugins"
               class="nav-tab <?php echo $active_tab == 'plugins' ? 'nav-tab-active' : ''; ?>"><?php _e('Plugins', 'solidres'); ?></a>
            <a href="?page=sr-options&amp;tab=tariff"
               class="nav-tab <?php echo $active_tab == 'tariff' ? 'nav-tab-active' : ''; ?>"><?php _e('Tariff', 'solidres'); ?></a>
            <a href="?page=sr-options&amp;tab=currency"
               class="nav-tab <?php echo $active_tab == 'currency' ? 'nav-tab-active' : ''; ?>"><?php _e('Currency', 'solidres'); ?></a>
            <a href="?page=sr-options&amp;tab=invoice"
               class="nav-tab <?php echo $active_tab == 'invoice' ? 'nav-tab-active' : ''; ?>"><?php _e('Invoice', 'solidres'); ?></a>
            <a href="?page=sr-options&amp;tab=pages"
               class="nav-tab <?php echo $active_tab == 'pages' ? 'nav-tab-active' : ''; ?>"><?php _e('Pages', 'solidres'); ?></a>
            <a href="?page=sr-options&amp;tab=tools"
               class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>"><?php _e('Tools', 'solidres'); ?></a>
			<?php do_action('sr_option_tab_title', $active_tab); ?>
        </h2>
		<?php if ($active_tab == 'plugins')
		{ ?>
            <form name="srform_edit_asset" action="options.php" method="post" id="srform">
				<?php
				settings_fields('solidres_plugin');
				$options = get_option('solidres_plugin');
				?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="srform_default_gallery"
                                               title="<?php _e('Select the default gallery to be used in Solidres, this list represents all Solidres Gallery plugins installed.', 'solidres'); ?>"><?php _e('Default gallery', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_plugin[default_gallery]" id="srform_default_gallery">
								<?php
								$selected = $options['default_gallery'];
								$p        = '';
								$r        = '';
								foreach ($default_gallery_options as $option)
								{
									$label = $option['label'];
									if ($selected == $option['value']) // Make default first in list
									{
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
									}
									else
									{
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
									}
								}
								echo $p . $r;
								?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_availability_calendar_enable"
                                               title="<?php _e('Enable the availability calendar in the front end to quickly show the availability of a room type', 'solidres'); ?>"><?php _e('Enable availability calendar', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_plugin[availability_calendar_enable]"
                                    id="srform_availability_calendar_enable">
								<?php
								$selected = $options['availability_calendar_enable'];
								$p        = '';
								$r        = '';
								foreach ($yes_no_options as $option)
								{
									$label = $option['label'];
									if ($selected == $option['value']) // Make default first in list
									{
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
									}
									else
									{
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
									}
								}
								echo $p . $r;
								?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_availability_calendar_month_number"
                                               title="<?php _e('The number of months to show in availability calendar. Default is showing 12 months starting from the current month', 'solidres'); ?>"><?php _e('Months in availability calendar', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_availability_calendar_month_number" class="regular-text"
                                   type="text" name="solidres_plugin[availability_calendar_month_number]"
                                   value="<?php esc_attr_e(!empty ($options['availability_calendar_month_number']) ? $options['availability_calendar_month_number'] : 6); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_datepicker_month_number"
                                               title="<?php _e('Enter the number of months to be shown in front end date picker', 'solidres'); ?>"><?php _e('Months in date picker', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_datepicker_month_number" class="regular-text" type="text"
                                   name="solidres_plugin[datepicker_month_number]"
                                   value="<?php esc_attr_e(isset($options['datepicker_month_number']) ? $options['datepicker_month_number'] : 1); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_min_days_book_in_advance"
                                               title="<?php _e('Specify the minimum number of day a booking must be made in advance of arrival. For example if you need your customer to book at least 7 days before their arrival date, enter 7 into this field. Default value is 0', 'solidres'); ?>"><?php _e('Min days book in advance', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_min_days_book_in_advance" class="regular-text" type="text"
                                   name="solidres_plugin[min_days_book_in_advance]"
                                   value="<?php esc_attr_e(!empty($options['min_days_book_in_advance']) ? $options['min_days_book_in_advance'] : 0); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_max_days_book_in_advance"
                                               title="<?php _e('Specify the maximum number of day a booking can be made in advance of arrival. For example if you do not allow your customers to book more than 90 days in advance, enter 90 into this field. Default value is 0 which means no limitation.', 'solidres'); ?>"><?php _e('Max days book in advance', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_max_days_book_in_advance" class="regular-text" type="text"
                                   name="solidres_plugin[max_days_book_in_advance]"
                                   value="<?php esc_attr_e(!empty($options['max_days_book_in_advance']) ? $options['max_days_book_in_advance'] : 0); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_min_length_of_stay"
                                               title="<?php _e('Specify the minimum number of night a customer can book. Default value is 1 night', 'solidres'); ?>"><?php _e('Minimum length of stay', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_min_length_of_stay" class="regular-text" type="text"
                                   name="solidres_plugin[min_length_of_stay]"
                                   value="<?php esc_attr_e(isset($options['min_length_of_stay']) ? $options['min_length_of_stay'] : 1); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_child_max_age_limit"
                                               title="<?php _e('The maximum age for a guest to be considered as child. Default is 17', 'solidres'); ?>"><?php _e('Child max age', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_child_max_age_limit" class="regular-text" type="text"
                                   name="solidres_plugin[child_max_age_limit]"
                                   value="<?php esc_attr_e(!empty($options['child_max_age_limit']) ? $options['child_max_age_limit'] : 17); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_show_price_with_tax"
                                               title="<?php _e('If you want to show room price with tax when your guests check for room availability, select Yes for this option.', 'solidres'); ?>"><?php _e('Show price with tax', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_plugin[show_price_with_tax]" id="srform_show_price_with_tax">
								<?php
								$selected = $options['show_price_with_tax'];
								$p        = '';
								$r        = '';
								foreach ($yes_no_options as $option)
								{
									$label = $option['label'];
									if ($selected == $option['value']) // Make default first in list
									{
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
									}
									else
									{
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
									}
								}
								echo $p . $r;
								?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="srform_default_reservation_state"
                                   title="<?php _e('Specify the default reservation status.', 'solidres'); ?>"><?php _e('Default reservation status', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_plugin[default_reservation_state]"
                                    id="srform_default_reservation_state">
								<?php
								$selected = $options['default_reservation_state'];
								$p        = '';
								$r        = '';
								foreach ($reservation_state as $option)
								{
									$label = $option['label'];
									if ($selected == $option['value']) // Make default first in list
									{
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
									}
									else
									{
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
									}
								}
								echo $p . $r;
								?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_discount_pre_tax"
                                               title="<?php _e('Specify whether to apply discount pre tax or after tax.', 'solidres'); ?>"><?php _e('Discount pre tax', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['discount_pre_tax'])) :
								$options['discount_pre_tax'] = 0;
							endif;
							?>
                            <select name="solidres_plugin[discount_pre_tax]" id="srform_discount_pre_tax">
                                <option <?php echo $options['discount_pre_tax'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No', 'solidres') ?></option>
                                <option <?php echo $options['discount_pre_tax'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Yes', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="srform_enable_multilingual_mode"
                                               title="<?php _e('If you want to support multilingual in Solidres, turn on this option. Otherwise turn it off for performance purpose.', 'solidres'); ?>"><?php _e('Enable multilingual mode', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['enable_multilingual_mode'])) :
								$options['enable_multilingual_mode'] = 0;
							endif;
							?>
                            <select name="solidres_plugin[enable_multilingual_mode]" id="srform_enable_multilingual_mode">
                                <option <?php echo $options['enable_multilingual_mode'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No', 'solidres') ?></option>
                                <option <?php echo $options['enable_multilingual_mode'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Yes', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="srform_list_limit"
                                               title="<?php _e('Number rows per pages listview', 'solidres'); ?>"><?php _e('Number rows per pages', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_list_limit" class="regular-text" type="number"
                                   name="solidres_plugin[list_limit]"
                                   value="<?php esc_attr_e(!empty($options['list_limit']) ? $options['list_limit'] : 5); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label
                                    title="<?php _e('Solidres small thumbnail size', 'solidres'); ?>"><?php _e('Small thumbnail size', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_plugin[sr_small_thumbnail_width]" class="regular-text" type="number"
                                   name="solidres_plugin[sr_small_thumbnail_width]"
                                   value="<?php esc_attr_e(!empty($options['sr_small_thumbnail_width']) ? $options['sr_small_thumbnail_width'] : 75); ?>"/>
                            px<br/>
                            x</br>
                            <input id="solidres_plugin[sr_small_thumbnail_height]" class="regular-text" type="number"
                                   name="solidres_plugin[sr_small_thumbnail_height]"
                                   value="<?php esc_attr_e(!empty($options['sr_small_thumbnail_height']) ? $options['sr_small_thumbnail_height'] : 75); ?>"/>
                            px
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label
                                    title="<?php _e('Solidres medium thumbnail size', 'solidres'); ?>"><?php _e('Medium thumbnail size', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_plugin[sr_medium_thumbnail_width]" class="regular-text" type="number"
                                   name="solidres_plugin[sr_medium_thumbnail_width]"
                                   value="<?php esc_attr_e(!empty($options['sr_medium_thumbnail_width']) ? $options['sr_medium_thumbnail_width'] : 300); ?>"/>
                            px<br/>
                            x</br>
                            <input id="solidres_plugin[sr_medium_thumbnail_height]" class="regular-text" type="number"
                                   name="solidres_plugin[sr_medium_thumbnail_height]"
                                   value="<?php esc_attr_e(!empty($options['sr_medium_thumbnail_height']) ? $options['sr_medium_thumbnail_height'] : 250); ?>"/>
                            px
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_enable_reservation_live_refresh"
                                               title="<?php _e('With this option enabled, Solidres will automatically count and show you the number of new reservation in the side navigation bar.', 'solidres'); ?>"><?php _e('Reservation live refresh', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['enable_reservation_live_refresh'])) :
								$options['enable_reservation_live_refresh'] = 0;
							endif;
							?>
                            <select name="solidres_plugin[enable_reservation_live_refresh]" id="srform_enable_reservation_live_refresh">
                                <option <?php echo $options['enable_reservation_live_refresh'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No', 'solidres') ?></option>
                                <option <?php echo $options['enable_reservation_live_refresh'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Yes', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_reservation_live_refresh_interval"
                                               title="<?php _e('Enter the interval for reservation live refresh feature in seconds, the default value is 15 seconds', 'solidres'); ?>"><?php _e('Live refresh interval', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_reservation_live_refresh_interval" class="regular-text" type="text"
                                   name="solidres_plugin[reservation_live_refresh_interval]"
                                   value="<?php esc_attr_e(!empty($options['reservation_live_refresh_interval']) ? $options['reservation_live_refresh_interval'] : 15); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_show_login_box"
                                               title="<?php _e('Specify whether to show login box in asset page to allow guest log in, this feature requires User plugin.', 'solidres'); ?>"><?php _e('Show login box', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['show_login_box'])) :
								$options['show_login_box'] = 0;
							endif;
							?>
                            <select name="solidres_plugin[show_login_box]" id="srform_show_login_box">
                                <option <?php echo $options['show_login_box'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No', 'solidres') ?></option>
                                <option <?php echo $options['show_login_box'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Yes', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_google_map_api_key"
                                               title="<?php _e('Google Map API Key', 'solidres'); ?>"><?php _e('Google Map API Key', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_google_map_api_key" class="regular-text" type="text"
                                   name="solidres_plugin[google_map_api_key]"
                                   value="<?php esc_attr_e(!empty($options['google_map_api_key']) ? $options['google_map_api_key'] : ''); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_ui_framework"
                                               title="<?php _e('Choose an UI Framework that Solidres use, the default option is Bootstrap 2.3.2. If you are using templates made by Solidres, you need to choose Bootstrap 2.3.2', 'solidres'); ?>"><?php _e('UI Framework', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['ui_framework'])) :
								$options['ui_framework'] = 'bs2';
							endif;
							?>
                            <select name="solidres_plugin[ui_framework]" id="srform_ui_framework">
                                <option <?php echo $options['ui_framework'] == 'bs2' ? 'selected' : '' ?>
                                        value="bs2"><?php _e('Bootstrap 2', 'solidres') ?></option>
                                <option <?php echo $options['ui_framework'] == 'bs3' ? 'selected' : '' ?>
                                        value="bs3"><?php _e('Bootstrap 3', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_enable_auto_scroll"
                                               title="<?php _e('Enable the auto scroll in front end, when guest check for availability, Solidres will automatically scroll to booking form/room type/tariff. The default value is Yes, you may want to turn off this feature if your template has sticky menu or header.', 'solidres'); ?>"><?php _e('Enable auto scroll', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['enable_auto_scroll'])) :
								$options['enable_auto_scroll'] = 1;
							endif;
							?>
                            <select name="solidres_plugin[enable_auto_scroll]" id="srform_enable_auto_scroll">
                                <option <?php echo $options['enable_auto_scroll'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No', 'solidres') ?></option>
                                <option <?php echo $options['enable_auto_scroll'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Yes', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
				<?php submit_button(); ?>
            </form>
		<?php }
		if ($active_tab == 'tariff')
		{ ?>
            <form name="srform_edit_asset" action="options.php" method="post" id="srform">
				<?php settings_fields('solidres_tariff'); ?>
				<?php $options = get_option('solidres_tariff'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="srform_show_frontend_tariffs"
                                               title="<?php _e('Show front end tariff', 'solidres'); ?>"><?php _e('Show front end tariff', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['show_frontend_tariffs'])) :
								$options['show_frontend_tariffs'] = 1;
							endif;
							?>
                            <select name="solidres_tariff[show_frontend_tariffs]" id="srform_show_frontend_tariffs">
                                <option <?php echo $options['show_frontend_tariffs'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No', 'solidres') ?></option>
                                <option <?php echo $options['show_frontend_tariffs'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Yes', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_enable_adjoining_tariffs"
                                               title="<?php _e('Enable tariff joining', 'solidres'); ?>"><?php _e('Enable tariff joining', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['enable_adjoining_tariffs'])) :
								$options['enable_adjoining_tariffs'] = 1;
							endif;
							?>
                            <select name="solidres_tariff[enable_adjoining_tariffs]"
                                    id="srform_enable_adjoining_tariffs">
								<?php
								?>
                                <option <?php echo $options['enable_adjoining_tariffs'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No', 'solidres') ?></option>
                                <option <?php echo $options['enable_adjoining_tariffs'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Yes', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_adjoining_tariffs_show_desc"
                                               title="<?php _e('Show description', 'solidres'); ?>"><?php _e('Show description', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['adjoining_tariffs_show_desc'])) :
								$options['adjoining_tariffs_show_desc'] = 1;
							endif;
							?>
                            <select name="solidres_tariff[adjoining_tariffs_show_desc]"
                                    id="srform_adjoining_tariffs_show_desc">
								<?php
								?>
                                <option <?php echo $options['adjoining_tariffs_show_desc'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No', 'solidres') ?></option>
                                <option <?php echo $options['adjoining_tariffs_show_desc'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Yes', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_adjoining_tariffs_mode"
                                               title="<?php _e('Adjoining tariffs mode', 'solidres'); ?>"><?php _e('Adjoining tariffs mode', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['adjoining_tariffs_mode'])) :
								$options['adjoining_tariffs_mode'] = 0;
							endif;
							?>
                            <select name="solidres_tariff[adjoining_tariffs_mode]" id="srform_adjoining_tariffs_mode">
								<?php
								?>
                                <option <?php echo $options['adjoining_tariffs_mode'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('No limit - Ignore stay length limit of any adjoining tariffs', 'solidres') ?></option>
                                <option <?php echo $options['adjoining_tariffs_mode'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Lowest - Use lowest min stay length of any adjoining tariffs', 'solidres') ?></option>
                                <option <?php echo $options['adjoining_tariffs_mode'] == 2 ? 'selected' : '' ?>
                                        value="2"><?php _e('Highest - Use highest min stay length of any adjoining tariffs', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_overlapping_tariffs_mode"
                                               title="<?php _e('Overlapping tariffs mode', 'solidres'); ?>"><?php _e('Overlapping tariffs mode', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['overlapping_tariffs_mode'])) :
								$options['overlapping_tariffs_mode'] = 0;
							endif;
							?>
                            <select name="solidres_tariff[overlapping_tariffs_mode]"
                                    id="srform_overlapping_tariffs_mode">
								<?php
								?>
                                <option <?php echo $options['overlapping_tariffs_mode'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('Show all tariffs options', 'solidres') ?></option>
                                <option <?php echo $options['overlapping_tariffs_mode'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Show the lowest tariff', 'solidres') ?></option>
                                <option <?php echo $options['overlapping_tariffs_mode'] == 2 ? 'selected' : '' ?>
                                        value="2"><?php _e('Show the highest tariff', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_child_room_cost_calc"
                                               title="<?php _e('Child room cost', 'solidres'); ?>"><?php _e('Child room cost', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							if (!isset($options['child_room_cost_calc'])) :
								$options['child_room_cost_calc'] = 1;
							endif;
							?>
                            <select name="solidres_tariff[child_room_cost_calc]" id="srform_child_room_cost_calc">
								<?php
								?>
                                <option <?php echo $options['child_room_cost_calc'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('Per age range', 'solidres') ?></option>
                                <option <?php echo $options['child_room_cost_calc'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Per child quantity', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
				<?php submit_button(); ?>
            </form>
		<?php }
		if ($active_tab == 'currency')
		{ ?>
            <form name="srform_edit_asset" action="options.php" method="post" id="srform">
				<?php settings_fields('solidres_currency'); ?>
				<?php $options = get_option('solidres_currency'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="srform_default_currency"
                                               title="<?php _e('Currency', 'solidres'); ?>"><?php _e('Currency', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_currency[default_currency_id]" id="srform_default_currency">
								<?php
								$selected = $options['default_currency_id'];
								$p        = '';
								$r        = '';
								if (!empty ($currencies_options_array))
								{
									foreach ($currencies_options_array as $option)
									{
										$label = apply_filters( 'solidres_currency_name', $option['label'] );
										if ($selected == $option['value']) // Make default first in list
										{
											$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
										}
										else
										{
											$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
										}
									}
									echo $p . $r;
								} ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_currency_format_pattern"
                                               title="<?php _e('Select the default currency format pattern', 'solidres'); ?>"><?php _e('Currency format pattern', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_currency[currency_format_pattern]"
                                    id="srform_currency_format_pattern">
								<?php
								$selected = $options['currency_format_pattern'];
								$p        = '';
								$r        = '';
								foreach ($curency_format_pattern_options as $option)
								{
									$label = $option['label'];
									if ($selected == $option['value']) // Make default first in list
									{
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
									}
									else
									{
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
									}
								}
								echo $p . $r;
								?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_number_decimal_points"
                                               title="<?php _e('Specify the number of decimal points', 'solidres'); ?>"><?php _e('Number of decimal points', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="srform_number_decimal_points" class="regular-text" type="text"
                                   name="solidres_currency[number_decimal_points]"
                                   value="<?php esc_attr_e(isset($options['number_decimal_points']) ? $options['number_decimal_points'] : 2); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="srform_currency_code_symbol"
                                               title="<?php _e('Specify whether to show code or symbol of currency in front end.', 'solidres'); ?>"><?php _e('Show code or symbol', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_currency[currency_code_symbol]" id="srform_currency_code_symbol">
								<?php
								$selected = $options['currency_code_symbol'];
								$p        = '';
								$r        = '';
								foreach ($code_or_symbol_options as $option)
								{
									$label = $option['label'];
									if ($selected == $option['value']) // Make default first in list
									{
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
									}
									else
									{
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
									}
								}
								echo $p . $r;
								?>
                            </select>
                        </td>
                    </tr>
					<?php
					$sr_currency = 'solidres-currency/solidres-currency.php';
					if (is_plugin_active($sr_currency)) : ?>
                        <tr valign="top">
                            <th scope="row"><label for="srform_currency_auto_update_exchange_rate"
                                                   title="<?php _e('Specify the interval time Solidres currency update exchange rate.', 'solidres'); ?>"><?php _e('Interval time update exchange rate', 'solidres'); ?></label>
                            </th>
                            <td>
                                <select name="solidres_currency[interval_time]" id="srform_currency_interval_time">
									<?php
									$selected = isset($options['auto_update_exchange_rate']) ? $options['auto_update_exchange_rate'] : '';
									$p        = '';
									$r        = '';
									foreach ($auto_update_exchange_rate as $option)
									{
										$label = $option['label'];
										if ($selected == $option['value']) // Make default first in list
										{
											$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
										}
										else
										{
											$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
										}
									}
									echo $p . $r;
									?>
                                </select>
                            </td>
                        </tr>
					<?php endif; ?>
                </table>
				<?php submit_button();
				do_action('solidres_currency_cron'); ?>
            </form>
		<?php }
		if ($active_tab == 'invoice')
		{ ?>
            <form name="srform_edit_asset" action="options.php" method="post" id="srform">
				<?php settings_fields('solidres_invoice'); ?>
				<?php $options = get_option('solidres_invoice'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="srform_enable_pdf_attachment"
                                               title="<?php _e('Turn on/off PDF attachment. Default is on.', 'solidres'); ?>"><?php _e('PDF attachment', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_invoice[enable_pdf_attachment]" id="srform_enable_pdf_attachment">
								<?php
								$selected = $options['enable_pdf_attachment'];
								$p        = '';
								$r        = '';
								foreach ($yes_no_options as $option)
								{
									$label = $option['label'];
									if ($selected == $option['value']) // Make default first in list
									{
										$p = "\n\t<option style=\"padding-right: 10px;\" selected='selected' value='" . esc_attr($option['value']) . "'>$label</option>";
									}
									else
									{
										$r .= "\n\t<option style=\"padding-right: 10px;\" value='" . esc_attr($option['value']) . "'>$label</option>";
									}
								}
								echo $p . $r;
								?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_number_prefix"
                                               title="<?php _e('Custom Invoice number prefix with tags : [dd] show day of month, [mm] show month of year [yy] show year. You can also use ' - ' symbol or specify your own string prefix. Default is re ', 'solidres'); ?>"><?php _e('Invoice number prefix format', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_number_prefix" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_number_prefix]"
                                   value="<?php esc_attr_e(!empty($options['solidres_invoice_number_prefix']) ? $options['solidres_invoice_number_prefix'] : 'INV'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_number_prefix_override"
                                               title="<?php _e('Set your number prefix override', 'solidres'); ?>"><?php _e('Invoice number prefix override', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_number_prefix_override" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_number_prefix_override]"
                                   value="<?php esc_attr_e($options['solidres_invoice_number_prefix_override']); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_number_digit"
                                               title="<?php _e('Enter number of digit of number part in your invoice number.Default is 00.', 'solidres'); ?>"><?php _e('Invoice number digit.', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_number_digit" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_number_digit]"
                                   value="<?php esc_attr_e(!empty($options['solidres_invoice_number_digit']) ? $options['solidres_invoice_number_digit'] : 00); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_number_digit"
                                               title="<?php _e('Define the invoice number starting point. Default is 1', 'solidres'); ?>"><?php _e('Invoice number start', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_number_start" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_number_start]"
                                   value="<?php esc_attr_e(!empty($options['solidres_invoice_number_start']) ? $options['solidres_invoice_number_start'] : 1); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_number_override"
                                               title="<?php _e('Set your override number', 'solidres'); ?>"><?php _e('Invoice number override', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_number_override" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_number_override]"
                                   value="<?php esc_attr_e($options['solidres_invoice_number_override']); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_logo_image_path"
                                               title="<?php _e('Invoice logo image path', 'solidres'); ?>"><?php _e('Logo image', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_logo_image_path" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_logo_image_path]"
                                   value="<?php esc_attr_e($options['solidres_invoice_logo_image_path']); ?>"/>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd; ">
                        <td colspan="2"><?php _e('PDF', 'solidres'); ?></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_title"
                                               title="<?php _e('Set your invoice pdf title. Default is invoice', 'solidres'); ?>"><?php _e('PDF title', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_title" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_title]"
                                   value="<?php esc_attr_e(!empty($options['solidres_invoice_pdf_title']) ? $options['solidres_invoice_pdf_title'] : 'invoice'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_subject"
                                               title="<?php _e('Set your invoice pdf subject. Default is invoice.', 'solidres'); ?>"><?php _e('PDF subject', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_subject" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_subject]"
                                   value="<?php esc_attr_e(!empty($options['solidres_invoice_pdf_subject']) ? $options['solidres_invoice_pdf_subject'] : 'invoice'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_page_format"
                                               title="<?php _e('Set your invoice pdf page format. Default is A4', 'solidres'); ?>"><?php _e('Page format', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_page_format" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_page_format]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_page_format']) ? $options['solidres_invoice_page_format'] : 'A4'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_page_orientation"
                                               title="<?php _e('Set your invoice pdf page orientation. Default is P', 'solidres'); ?>"><?php _e('Page orientation', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_page_orientation" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_page_orientation]"
                                   value="<?php esc_attr_e(!empty($options['solidres_invoice_pdf_page_orientation']) ? $options['solidres_invoice_pdf_page_orientation'] : 'P'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_creator"
                                               title="<?php _e('Set your PDF creator. Default is hotel.', 'solidres'); ?>"><?php _e('PDF creator', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_creator" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_creator]"
                                   value="<?php esc_attr_e(!empty($options['solidres_invoice_pdf_creator']) ? $options['solidres_invoice_pdf_creator'] : 'hotel'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_author"
                                               title="<?php _e('Set your PDF author. Default is hotel', 'solidres'); ?>"><?php _e('PDF author', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_author" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_author]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_author']) ? $options['solidres_invoice_pdf_author'] : 'hotel'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_unit"
                                               title="<?php _e('Set your PDF unit. Default is mm', 'solidres'); ?>"><?php _e('PDF unit', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_unit" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_unit]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_unit']) ? $options['solidres_invoice_pdf_unit'] : 'mm'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_margin_header"
                                               title="<?php _e('Set your margin header. Default is 5.', 'solidres'); ?>"><?php _e('Margin header', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_margin_header" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_margin_header]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_margin_header']) ? $options['solidres_invoice_pdf_margin_header'] : 5); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_margin_footer"
                                               title="<?php _e('Set your margin footer. Default is 10.'); ?>"><?php _e('Margin footer', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_margin_footer" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_margin_footer]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_margin_footer']) ? $options['solidres_invoice_pdf_margin_footer'] : '10'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_margin_top"
                                               title="<?php _e('Set your margin top. Default is 27.', 'solidres'); ?>"><?php _e('Margin top', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_margin_top" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_margin_top]"
                                   value="<?php esc_attr_e(!empty($options['solidres_invoice_pdf_margin_top']) ? $options['solidres_invoice_pdf_margin_top'] : 27); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_margin_bottom"
                                               title="<?php _e('Set your margin bottom. Default is 25.', 'solidres'); ?>"><?php _e('Margin bottom', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_margin_bottom" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_margin_bottom]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_margin_bottom']) ? $options['solidres_invoice_pdf_margin_bottom'] : 25); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_margin_left"
                                               title="<?php _e('Set your margin left. Default is 15.', 'solidres'); ?>"><?php _e('Margin left', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_margin_left" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_margin_left]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_margin_left']) ? $options['solidres_invoice_pdf_margin_left'] : 15); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_margin_right"
                                               title="<?php _e('Set your margin right. Default is 15.', 'solidres'); ?>"><?php _e('Margin right', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_margin_right" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_margin_right]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_margin_right']) ? $options['solidres_invoice_pdf_margin_right'] : 15); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_font_name_main"
                                               title="<?php _e('Set your font name main. Default is dejavusans', 'solidres'); ?>"><?php _e('Font name main', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							$invoice_font_name_main = !empty ($options['solidres_invoice_pdf_font_name_main']) ? $options['solidres_invoice_pdf_font_name_main'] : 'dejavusans';
							$font_list = array(
								'courier',
								'dejavusans',
								'freeserif',
								'helvetica',
								'pdfacourier',
								'pdfahelvetica',
								'pdfasymbol',
								'pdfatimes',
								'symbol',
								'times',
							);
							?>
                            <select id="solidres_invoice_pdf_font_name_main"
                                    name="solidres_invoice[solidres_invoice_pdf_font_name_main]">
								<?php foreach ($font_list as $font) : ?>
                                    <option <?php echo $invoice_font_name_main == $font ? 'selected' : $font ?> value="<?php echo $font ?>"><?php echo $font ?></option>
								<?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_font_size_main"
                                               title="<?php _e('Set your font size main. Default is 8.', 'solidres'); ?>"><?php _e('Font size main', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_font_size_main" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_font_size_main]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_font_size_main']) ? $options['solidres_invoice_pdf_font_size_main'] : 8); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_font_name_footer"
                                               title="<?php _e('Set your font name footer. Default is false', 'solidres'); ?>"><?php _e('Font name footer', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_font_name_footer" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_font_name_footer]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_font_name_footer']) ? $options['solidres_invoice_pdf_font_name_footer'] : 'false'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_font_size_footer"
                                               title="<?php _e('Set your font size footer. Default is false', 'solidres'); ?>"><?php _e('Font size footer', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_font_size_footer" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_font_size_footer]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_font_size_footer']) ? $options['solidres_invoice_pdf_font_size_footer'] : 'false'); ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_font_monospaced"
                                               title="<?php _e('Set your font monospaced. Default is courier', 'solidres'); ?>"><?php _e('Font monospaced', 'solidres'); ?></label>
                        </th>
                        <td>
							<?php
							$invoice_pdf_font_monospaced = !empty ($options['solidres_invoice_pdf_font_monospaced']) ? $options['solidres_invoice_pdf_font_monospaced'] : 'dejavusans';
							?>
                            <select id="solidres_invoice_pdf_font_monospaced"
                                    name="solidres_invoice[solidres_invoice_pdf_font_monospaced]">
								<?php foreach ($font_list as $font) : ?>
                                    <option <?php echo $invoice_pdf_font_monospaced == $font ? 'selected' : '' ?> value="<?php echo $font ?>"><?php echo $font ?></option>
								<?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="solidres_invoice_pdf_image_scale_ratio"
                                               title="<?php _e('Set your image scale ratio. Default is 1.25', 'solidres'); ?>"><?php _e('Image scale ratio', 'solidres'); ?></label>
                        </th>
                        <td>
                            <input id="solidres_invoice_pdf_image_scale_ratio" class="regular-text" type="text"
                                   name="solidres_invoice[solidres_invoice_pdf_image_scale_ratio]"
                                   value="<?php esc_attr_e(!empty ($options['solidres_invoice_pdf_image_scale_ratio']) ? $options['solidres_invoice_pdf_image_scale_ratio'] : 1.25); ?>"/>
                        </td>
                    </tr>
                </table>
				<?php submit_button(); ?>
            </form>
		<?php }
		if ($active_tab == 'pages')
		{ ?>
            <form name="srform_edit_asset" action="options.php" method="post" id="srform">
				<?php settings_fields('solidres_pages'); ?>
				<?php $options = get_option('solidres_pages'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Reservation completed', 'solidres'); ?></th>
                        <td>
							<?php echo wp_dropdown_pages(array(
								'name'             => 'solidres_pages[reservationcompleted]',
								'echo'             => false,
								'show_option_none' => __('- None -', 'solidres'),
								'selected'         => !empty($options['reservationcompleted']) ? $options['reservationcompleted'] : false
							)) ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Customer Dashboard', 'solidres'); ?></th>
                        <td>
							<?php echo wp_dropdown_pages(array(
								'name'             => 'solidres_pages[customerdashboard]',
								'echo'             => false,
								'show_option_none' => __('- None -', 'solidres'),
								'selected'         => !empty($options['customerdashboard']) ? $options['customerdashboard'] : false
							)) ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('View reservation endpoint', 'solidres'); ?></th>
                        <td>
                            <input type="text" name="solidres_pages[customerdashboard_viewreservation_endpoint]"
                                   id="solidres_pages[customerdashboard_viewreservation_endpoint]"
                                   placeholder="<?php _e('view-reservation', 'solidres') ?>"
                                   value="<?php echo !empty($options['customerdashboard_viewreservation_endpoint']) ? $options['customerdashboard_viewreservation_endpoint'] : 'view-reservation' ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Cancel reservation endpoint', 'solidres'); ?></th>
                        <td>
                            <input type="text" name="solidres_pages[customerdashboard_cancelreservation_endpoint]"
                                   id="solidres_pages[customerdashboard_cancelreservation_endpoint]"
                                   placeholder="<?php _e('cancel-reservation', 'solidres') ?>"
                                   value="<?php echo !empty($options['customerdashboard_cancelreservation_endpoint']) ? $options['customerdashboard_cancelreservation_endpoint'] : 'cancel-reservation' ?>"/>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Edit account endpoint', 'solidres'); ?></th>
                        <td>
                            <input type="text" name="solidres_pages[customerdashboard_editaccount_endpoint]"
                                   id="solidres_pages[customerdashboard_editaccount_endpoint]"
                                   placeholder="<?php _e('edit-account', 'solidres') ?>"
                                   value="<?php echo !empty($options['customerdashboard_editaccount_endpoint']) ? $options['customerdashboard_editaccount_endpoint'] : 'edit-account' ?>"/>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Logout endpoint', 'solidres'); ?></th>
                        <td>
                            <input type="text" name="solidres_pages[customerdashboard_logout_endpoint]"
                                   id="solidres_pages[customerdashboard_logout_endpoint]"
                                   placeholder="<?php _e('customer-logout', 'solidres') ?>"
                                   value="<?php echo !empty($options['customerdashboard_logout_endpoint']) ? $options['customerdashboard_logout_endpoint'] : 'customer-logout' ?>"/>
                        </td>
                    </tr>
                </table>
				<?php submit_button(); ?>
            </form>
		<?php }
		if ($active_tab == 'tools')
		{ ?>
            <form name="srform_edit_tools" action="options.php" method="post" id="srform">
				<?php settings_fields('solidres_tools'); ?>
				<?php $options = get_option('solidres_tools'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="solidres_single_asset_page"
                                               title="<?php _e('It will disable template overrides for logged-in administrators for debugging purpose', 'solidres'); ?>"><?php _e('Enable template debug', 'solidres'); ?></label>
                        </th>
                        <td>
                            <select name="solidres_tools[enable_template_debug]">
                                <option <?php echo $options['enable_template_debug'] == 0 ? 'selected' : '' ?>
                                        value="0"><?php _e('Disable', 'solidres') ?></option>
                                <option <?php echo $options['enable_template_debug'] == 1 ? 'selected' : '' ?>
                                        value="1"><?php _e('Enable', 'solidres') ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
				<?php submit_button(); ?>
            </form>
		<?php } ?>
		<?php do_action('sr_option_tab_content', $active_tab); ?>
    </div>
<?php }