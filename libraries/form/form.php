<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Wordpress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2015 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/
namespace SR\Form;

use SR\Helper\SR_Loader;
use SR\Session\SR_Session;
use SR\Table\SR_Table;

if (!defined('ABSPATH'))
{
	exit;
}

class SR_Form
{

	protected $document;
	protected $form;
	protected $db_table;
	protected $db_table_alias = null;
	protected $id_field = 'id';
	protected $form_data = array();
	protected $data_edit = array();
	protected $title;
	protected $session;
	protected $db;
	protected $context;

	public function __construct($config = array())
	{
		global $wpdb;
		$this->db = $wpdb;
		if (!empty($config['form_data']))
		{
			$this->form_data = $config['form_data'];
		}
		if (!empty($config['data_edit']))
		{
			$this->data_edit = $config['data_edit'];
		}
		if (!empty($config['id_field']) && is_string($config['id_field']))
		{
			$this->id_field = trim($config['id_field']);
		}
		if (!empty($config['db_table']))
		{
			settype($config['db_table'], 'array');
			$this->db_table = $config['db_table'][0];
			if (!preg_match('/^(' . $this->db->prefix . ')/', $this->db_table))
			{
				$this->db_table = $this->db->prefix . $this->db_table;
			}
			if (isset($config['db_table'][1]))
			{
				$this->db_table_alias = $config['db_table'][1];
			}
		}
		if (isset($config['form_title']))
		{
			$this->title = $config['form_title'];
		}
		else
		{
			$this->title = __('Edit item');
		}
		$this->context  = isset($config['context'])
			? $config['context']
			: (isset($_REQUEST['page'])
				? $_REQUEST['page']
				: $this->db_table);
		$this->document = new \DOMDocument();
		$this->document->appendChild($this->document->createElement('form'));
		$this->form = $this->document->getElementsByTagName('form')[0];

		SR_Loader::import('table.table');
		SR_Loader::import('session.session');

		$table = new SR_Table(array('db_table' => $this->db_table, 'key' => $this->id_field, 'context' => $this->context));
		SR_Loader::addClass($table);
		$this->session = SR_Session::getInstance($this->context);
	}

	public function addField($name = 'input', $attributes = array())
	{
		settype($attributes, 'array');
		$type = isset($attributes['type']) ? strtolower($attributes['type']) : 'text';
		if ('display' == $name)
		{
			return $this;
		}

		$value  = isset($attributes['value']) ? strtolower($attributes['value']) : '';
		$f_name = isset($attributes['name']) ? strtolower($attributes['name']) : false;
		$field  = $this->document->createElement($name);
		if ('select' == $name && isset($attributes['options']) && is_array($attributes['options']))
		{
			$this->addOptions($field, $attributes);
		}
		$this->setAttributes($field, $attributes);
		if ($f_name && isset($this->form_data[$f_name]))
		{
			if ('select' == $name && $field->hasChildNodes())
			{
				$options = $field->getElementsByTagName('option');
				for ($i = 0, $n = $options->length; $i < $n; $i++)
				{
					if ($this->form_data[$f_name] == $options->item($i)->getAttribute('value'))
					{
						$options->item($i)->setAttribute('selected', 'selected');
						break;
					}
				}
			}
			elseif ('textarea' == $name)
			{
				$field->nodeValue = $this->form_data[$f_name];
			}
			elseif ('input' == $name && ('radio' == $type || 'checkbox' == $name) && $value == $this->form_data[$f_name])
			{
				$field->setAttribute('checked', 'checked');
			}
			else
			{
				$field->setAttribute('value', $this->form_data[$f_name]);
			}
		}

		$this->form->appendChild($field);

		return $this;
	}

	public function setFormData($data)
	{
		$this->form_data = $data;
	}

	protected function setAttributes(\DOMElement $field, $attributes = array())
	{
		foreach ($attributes as $name => $value)
		{
			if (is_string($value) || is_numeric($value))
			{
				$field->setAttribute($name, $value);
			}

		}

		return $this;
	}

	protected function addOptions(&$field, $attributes)
	{
		$options = @$attributes['options'];
		settype($options, 'array');
		foreach ($options as $attr)
		{
			settype($attr, 'array');
			$option = $this->document->createElement('option', @$attr['text']);
			$option->setAttribute('value', @$attr['value']);
			$field->appendChild($option);
		}
	}

	public function getFieldById($id, $render = true)
	{
		$field = $this->form->getElementById($id);
		if ($render)
		{
			$field = $field->ownerDocument->saveHTML($field);
		}

		return $field;
	}

	public function getFields()
	{
		$fields = array();
		foreach ($this->form->childNodes as $field)
		{
			$name          = $field->getAttribute('name');
			$fields[$name] = $field->ownerDocument->saveHTML($field);
		}

		return $fields;
	}

	public function getFieldByName($name)
	{
		static $fields = array();
		if (!isset($fields[$name]))
		{
			$fields = $this->getFields();
		}

		return isset($fields[$name]) ? $fields[$name] : false;
	}

	protected function loadItemData()
	{
		$id = (int) $_REQUEST[$this->id_field];
		if ($id)
		{
			$item_table = SR_Loader::getClass('SR_Table');
			if ($item_table->load($id))
			{
				$form_data = array();
				foreach ($item_table->getData() as $name => $value)
				{
					$form_data[$name] = $value;
				}
				$this->form_data = apply_filters('sr_form_bind_data', $form_data);

			}
		}
	}

	public function progressForm($redirect = null)
	{
		$data = isset($_POST['srform']) && is_array($_POST['srform']) ? $_POST['srform'] : array();
		if (count($data))
		{
			$id         = (int) $data[$this->id_field];
			$item_table = SR_Loader::getClass('SR_Table');
			if ($item_table->save($data))
			{
				$this->session->set('message', array(
					'icon'    => '<span class="dashicons dashicons-yes"></span>',
					'message' => __('Item saved!')
				));
			}
			elseif (!empty($item_table->getDbo()->last_error))
			{
				$this->session->set('message', array(
					'icon'    => '<span class="dashicons dashicons-no"></span>',
					'message' => $item_table->getDbo()->last_error
				));
			}

			if (!$id)
			{
				$id = (int) $item_table->getInsertId();
			}

			if (null == $redirect)
			{
				$redirect = admin_url('admin.php?page=' . $_GET['page'] . '&action=edit&' . $this->id_field . '=' . $id);
			}
			wp_redirect($redirect);
			exit;
		}
	}

	public function renderForm($return = false)
	{
		if (empty($this->data_edit))
		{
			die('Data not found!');
		}
		$this->loadItemData();
		$items = array();
		foreach ($this->data_edit as $name => $attributes)
		{
			$display = isset($attributes['display']) ? $attributes['display'] : null;

			if (null !== $display)
			{
				$field_name = 'display';
			}
			else
			{
				$field_name         = strtolower($attributes['field']);
				$attributes['name'] = $name;
				unset($attributes['field']);
			}

			$this->addField($field_name, $attributes);
			$required = (isset($attributes['required']) && (bool) $attributes['required']);
			$title    = isset($attributes['title']) ? trim($attributes['title']) : __('NO TITLE');
			$id       = isset($attributes['id']) ? trim($attributes['id']) : '';
			$input    = null !== $display ? $display : preg_replace('/name\s*=\s*["\']([^"\']+)["\']/i', 'name="srform[${1}]"', $this->getFieldByName($name));
			ob_start();
			include WP_PLUGIN_DIR . '/solidres/libraries/form/layouts/field.php';
			$items[] = ob_get_clean();
		}
		ob_start();
		include WP_PLUGIN_DIR . '/solidres/libraries/form/layouts/wrap.php';
		$output = ob_get_clean();

		$this->progressForm();
		$message = $this->session->getClear('message');
		if (null !== $message && is_array($message))
		{
			$output = '<div class="updated">' . @$message['icon'] . ' ' . @$message['message'] . '</div>' . $output;
		}
		if ($return)
		{
			return $output;
		}
		echo $output;
	}

}