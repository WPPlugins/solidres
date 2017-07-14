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

use SR\Helper\SR_Loader;
use SR\Grid\SR_Grid;
use SR\Form\SR_Form;

if (!defined('ABSPATH'))
{
	exit;
}

class SR_Layout_Admin
{
	public function __construct($config = array())
	{
		$is_form = (isset($_GET['action']) && trim($_GET['action']) == 'edit');
		if ($is_form)
		{
			SR_Loader::import('form.form');
			$form = new SR_Form($config);

			$form->renderForm();
		}
		else
		{
			SR_Loader::import('grid.grid');
			$grid = new SR_Grid($config);

			$grid->display();
		}
	}
}