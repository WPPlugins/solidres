<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Wordpress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2015 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/
namespace SR\Layout;
if (!defined('ABSPATH'))
{
	exit;
}

class SR_Layout
{
	protected $config = array();

	public function __construct($config = array())
	{
		$this->config = $config;
		if (isset($config['auto_load']) && $config['auto_load'] && !empty($config['path']))
		{
			$admin = is_admin();
			$this->loadLayout($admin, $config['path']);
		}
	}

	public function loadLayout($admin = true, $path)
	{
		$page         = @strtolower($_REQUEST['page']);
		$accept_pages = isset($this->config['accept_pages']) ? (array) $this->config['accept_pages'] : array();
		if (!in_array($page, $accept_pages))
		{
			return false;
		}
		$function_file = $path . '/' . ($admin ? 'admin' : 'site') . '/' . str_replace('sr-', '', $page) . '.php';
		if (!file_exists($function_file))
		{
			return false;
		}
		include_once $function_file;

		return true;
	}

	public static function loadPostBox($data = array())
	{
		$output = '<div' . (!empty($data['id']) ? ' id="' . $data['id'] . '"' : '') . ' class="postbox">'
			. '         <button type="button" class="handlediv button-link" aria-expanded="true">'
			. '             <span class="toggle-indicator" aria-hidden="true"></span>'
			. '         </button>'
			. '         <h3 class="hndle handle ui-sortable-handle"><span>' . @$data['title'] . '</span></h3>'
			. '         <div class="inside">' . @$data['content'] . '</div>'
			. '     </div>';

		return $output;
	}


}