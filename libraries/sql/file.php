<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Wordpress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2015 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/
namespace SR\Sql;
if (!defined('ABSPATH'))
{
	exit;
}
require_once ABSPATH . '/wp-admin/includes/upgrade.php';

class SR_File
{
	public static function createTable($file_name, $path)
	{
		global $wpdb;
		$file_name = preg_replace('/(\.sql)$/i', '', strtolower($file_name), 1);
		$sql       = file_get_contents($path . '/' . $file_name . '.sql', FILE_USE_INCLUDE_PATH);
		preg_match_all('/CREATE\s+TABLE[^;]+;/i', str_ireplace('wp_prefix_', $wpdb->prefix, $sql), $matchs);
		if (!empty($matchs) && $matchs[0])
		{
			foreach ($matchs[0] as $query)
			{
				dbDelta($query);
			}
		}
	}

	public static function execute($file_name, $path)
	{
		global $wpdb;
		$file_name = preg_replace('/(\.sql)$/i', '', strtolower($file_name), 1);
		$sql       = file_get_contents($path . '/' . $file_name . '.sql', FILE_USE_INCLUDE_PATH);
		$wpdb->query('SET FOREIGN_KEY_CHECKS=0');
		$wpdb->query(str_ireplace('wp_prefix_', $wpdb->prefix, $sql));
	}


}