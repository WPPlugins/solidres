<?php
/*------------------------------------------------------------------------
Solidres - Hotel booking extension for Wordpress
------------------------------------------------------------------------
@Author    Solidres Team
@Website   http://www.solidres.com
@Copyright Copyright (C) 2013 - 2014 Solidres. All Rights Reserved.
@License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/
namespace SR\Helper;
if (!defined('ABSPATH'))
{
	exit;
}

class SR_Loader
{
	protected static $instance;
	protected static $classes = array();
	public static $prefix = 'SR_';

	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new static;
		}

		return self::$instance;
	}

	public static function import($file_name, $path = '')
	{
		static $files = array();

		if (empty($path))
		{
			$path = WP_PLUGIN_DIR . '/solidres/libraries';
		}

		$file_name = str_replace('.', '/', preg_replace('/(\.php)$/i', '', $file_name)) . '.php';
		if (!isset($files[$file_name]))
		{
			$files[$file_name] = $path . '/' . $file_name;
			include $files[$file_name];
		}

		return $files[$file_name];
	}

	public static function addClass($objClass)
	{
		$class_name = preg_replace('#^(.*)[\\\\]#', '', get_class($objClass));

		if (!isset(static::$classes[$class_name]))
		{
			static::$classes[$class_name] = $objClass;
		}

		return static::$classes[$class_name];
	}

	public static function getClass($class_name)
	{
		return isset(static::$classes[$class_name]) ? static::$classes[$class_name] : null;
	}

}