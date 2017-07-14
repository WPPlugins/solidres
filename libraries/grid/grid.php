<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Wordpress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2015 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/
namespace SR\Grid;

use SR\Helper\SR_Loader;
use SR\Session\SR_Session;
use SR\Query\SR_Query;
use SR\Table\SR_Table;

if (!defined('ABSPATH'))
{
	exit;
}
if (!class_exists('\\WP_List_Table'))
{
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SR_Grid extends \WP_List_Table
{
	protected $db;
	protected $data_grid;
	protected $column_headers;
	protected $db_table;
	protected $db_table_alias = null;
	protected $id_field = 'id';
	protected $state_field = null;
	protected $title;
	protected $context;
	protected $session;

	public function __construct($config)
	{
		global $wpdb, $_wp_column_headers;
		$this->db             = $wpdb;
		$this->column_headers = &$_wp_column_headers;

		if (isset($config['data_grid']))
		{
			$this->setDataGrid($config['data_grid']);
			unset($config['data_grid']);
		}
		if (isset($config['db_table']))
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
		else
		{
			die('Table not exist!');
		}
		if (isset($config['list_title']))
		{
			$this->title = $config['list_title'];
		}
		else
		{
			$this->title = __('List items');
		}
		if (isset($config['id_field']))
		{
			$this->id_field = $config['id_field'];
		}
		if (isset($config['state_field']))
		{
			$this->state_field = $config['state_field'];
		}
		$this->context = isset($config['context'])
			? $config['context']
			: (isset($_REQUEST['page'])
				? $_REQUEST['page']
				: $this->db_table);

		SR_Loader::import('table.table');
		SR_Loader::import('session.session');

		$table = new SR_Table(array('db_table' => $this->db_table, 'key' => $this->id_field, 'context' => $this->context));
		SR_Loader::addClass($table);
		$this->session = SR_Session::getInstance($this->context);

		if (!class_exists('\\SR_Helper'))
		{
			SR_Loader::import('includes.class-sr-helper', ABSPATH . '/plugins/solidres');
		}
		parent::__construct($config);
	}

	public function prepare_items()
	{
		if (isset($_REQUEST['filters']) && is_array($_REQUEST['filters']))
		{
			$this->session->set('filters', (array) $_REQUEST['filters']);
		}
		static $_pagination_args = array();
		if (empty($_pagination_args))
		{
			$data_grid = $this->data_grid;
			$options   = @get_option('solidres_plugin');
			$limit     = @$options['list_limit'];
			settype($limit, 'int');
			if ($limit < 1)
			{
				$limit = 20;
			}
			$current_page     = (int) $this->get_pagenum();
			$start            = ($current_page * $limit) - $limit;
			$total            = $this->db->get_var('SELECT COUNT(' . $this->id_field . ') FROM ' . $this->db_table);
			$_pagination_args = array(
				'total_items' => $total,
				'total_pages' => ceil($total / $limit),
				'per_page'    => $limit,
			);
			$this->set_pagination_args($_pagination_args);
		}
		$screen                            = get_current_screen();
		$this->column_headers[$screen->id] = $this->get_columns();
		$this->_column_headers             = array($this->get_columns(), get_hidden_columns($screen), $this->get_sortable_columns());

		SR_Loader::import('query.query');
		$query  = new SR_Query;
		$select = array_keys($data_grid['list']);

		$alias = !is_null($this->db_table_alias) ? $this->db_table_alias . '.' : null;

		foreach ($select as $column)
		{
			if (array_key_exists('display', $data_grid['list'][$column]))
			{
				unset($column);
				continue;
			}
			$query->select($alias . $column);
		}
		$query->from($this->db_table, $this->db_table_alias);

		if (is_admin())
		{
			$filters = $this->session->get('filters', array());
			$fields  = SR_Loader::getClass('SR_Table')->getProperties();
			foreach ($filters as $name => $value)
			{
				if (!in_array($name, $fields))
				{
					continue;
				}
				$value = trim($value);
				if (is_numeric($value))
				{
					$query->where($alias . $name . ' = ' . (int) $value);
				}
				elseif (is_string($value) && !empty($value))
				{
					$query->where($alias . $name . ' LIKE \'%' . $this->db->esc_like($value) . '%\'');
				}
			}
		}
		elseif (isset($this->state_field))
		{
			$query->where($alias . $this->state_field . ' = 1');
		}
		if (isset($_GET['orderby']))
		{
			$order_by = preg_replace('/[^a-z0-9\-\_\.]/i', '', $_GET['orderby']);
			$order    = 'ASC';
			if (isset($_GET['order']) && in_array(strtoupper($_GET['order']), array('DESC', 'ASC')))
			{
				$order = strtoupper($_GET['order']);
			}
			$query->order($order_by, $order);
		}
		$query->limit($start, $limit);

		$query = apply_filters('sr_grid_query', $query, $this->context);

		$this->items = $query->getItems();

		return $this;
	}

	public function get_columns()
	{
		$list_fields = $this->data_grid['list'];
		$columns     = array('cb' => '<input type="checkbox" />');
		foreach ($list_fields as $name => $field)
		{
			$columns[$name] = $field['title'];
		}

		return $columns;
	}

	public function get_sortable_columns()
	{
		$list_fields  = $this->data_grid['list'];
		$sort_columns = array();
		foreach ($list_fields as $name => $field)
		{
			if (isset($field['sort']) && is_array($field['sort']) && count($field['sort']) == 2)
			{
				$sort_columns[$name] = array($field['sort'][0], strtolower($field['sort'][1]) == 'DESC' ? true : false);
			}
		}

		return $sort_columns;
	}

	protected function column_default($item, $column_name)
	{
		$data_grid = $this->getDataGrid();
		$item_xref = (array) $data_grid['list'][$column_name];
		if (!empty($item_xref['display']))
		{
			$output = !empty($item->{$column_name})
				? $item->{$column_name}
				: $item_xref['display'];
		}
		else
		{
			$output = $item->{$column_name};
			if (isset($item_xref['bulk']) && (int) $item_xref['bulk'])
			{
				$field_id     = $this->id_field;
				$bulk_actions = $this->get_bulk_actions();
				$actions      = array();
				foreach ($bulk_actions as $action => $title)
				{
					$actions[$action] = '<a href="?page=' . $_REQUEST['page'] . '&action=' . $action . '&' . $this->id_field . '=' . (int) $item->{$field_id} . '">' . $title . '</a>';
				}
				$output .= $this->row_actions($actions);
			}
		}
		$output = apply_filters('sr_grid_column_default', $output, $item, $column_name, $this->context);

		return $output;
	}

	protected function column_state($item)
	{
		$status = isset($this->state_field) ? (int) $item->{$this->state_field} : false;
		if (false !== $status)
		{
			$action = $status ? 'trash' : 'untrash';
			$link   = admin_url('admin.php?page=' . $_REQUEST['page'] . '&action=' . $action . '&' . $this->id_field . '=' . $item->{$this->id_field});
			$icon   = '<span class="dashicons dashicons-' . ($status ? 'yes' : 'no') . '"></span>';
			$output = '<a href="' . $link . '">' . $icon . '</a>';;
			$output = apply_filters('sr_grid_column_state', $output, $this->context);

			return $output;
		}
	}

	public function setDataGrid($data = array())
	{
		$previous        = $this->data_grid;
		$this->data_grid = $data;

		return $previous;
	}

	public function getDataGrid()
	{
		if (empty($this->data_grid))
		{
			die(__('Data not found!'));
		}

		return $this->data_grid;
	}

	protected function column_cb($item)
	{
		$output = '<input type="checkbox" name="' . $this->id_field . '[]" value="' . (int) $item->id . '" />';
		$output = apply_filters('sr_grid_column_cb', $output, $this->context);

		return $output;
	}

	protected function get_bulk_actions()
	{
		$filters = $this->session->get('filters', array());
		$status  = isset($filters[$this->state_field]) ? $filters[$this->state_field] : '';
		switch ($status)
		{
			case '0':
				$actions = array(
					'restore' => __('Restore', 'Solidres'),
					'delete'  => __('Delete', 'Solidres'),
				);
				break;
			case '1':
				$actions = array(
					'edit'  => __('Edit', 'Solidres'),
					'trash' => __('Trash', 'Solidres')
				);
				break;
			default:
				if (empty($this->state_field))
				{
					$actions = array(
						'edit'   => __('Edit', 'Solidres'),
						'delete' => __('Delete', 'Solidres')
					);
				}
				else
				{
					$actions = array(
						'edit'  => __('Edit', 'Solidres'),
						'trash' => __('Trash', 'Solidres')
					);
				}
				break;
		}


		return $actions;
	}

	protected function bulk_actions($which = '')
	{
		parent::bulk_actions($which);
		echo '<script>
			var $j = jQuery.noConflict();
			$j("form [name=\'action\'], form [name=\'action2\']").on("change", function(){
				var name = $j(this).attr("name") == "action" ? "action2" : "action";
				$j("form [name="+name+"]>option:eq(0)").prop("selected", true);
			});

		</script>';
	}

	public function progressAction()
	{
		$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '-1';
		if ($action == '-1')
		{
			$action = isset($_REQUEST['action2']) ? trim($_REQUEST['action2']) : '-1';
		}
		$pks = isset($_REQUEST[$this->id_field]) ? $_REQUEST[$this->id_field] : array();
		settype($pks, 'array');
		if ($action != '-1' && count($pks))
		{
			$tmp = array();
			foreach ($pks as $id)
			{
				if (is_numeric($id))
				{
					$tmp[] = (int) $id;
				}
			}
			$pks = $tmp;

			if (count($pks))
			{
				if ($action == 'delete')
				{
					$table  = SR_Loader::getClass('SR_Table');
					$result = $table->delete($pks);
					if ($result)
					{
						$this->session->set('message', array(
							'icon'    => '<span class="dashicons dashicons-yes"></span>',
							'message' => count($pks) . __('items deleted')
						));
					}
					else
					{
						$this->session->set('message', array(
							'icon'    => '<span class="dashicons dashicons-no"></span>',
							'message' => $this->db->last_error
						));
					}
					wp_redirect(admin_url('admin.php?page=' . $_GET['page']));
					exit;
				}
				elseif ($action == 'edit')
				{
					$id = $pks[0]; //Just edit for 1 record
					wp_redirect(admin_url('admin.php?page=' . $_GET['page'] . '&action=edit&' . $this->id_field . '=' . $id));
					exit;
				}
				elseif (in_array($action, array('trash', 'restore', 'inactive', 'active', 'untrash')) && !empty($this->state_field))
				{
					$status = \SR_Helper::getStateValue($action);
					$result = $this->db->query('UPDATE ' . $this->db_table . ' SET ' . $this->state_field . ' = ' . $status . ' WHERE ' . $this->id_field . ' IN(' . join(',', $pks) . ')');
					if (false !== $result)
					{
						$this->session->set('message', array(
							'icon'    => '<span class="dashicons dashicons-yes"></span>',
							'message' => count($pks) . __('items ' . $action . 'ed!')
						));
					}
					else
					{
						$this->session->set('message', array(
							'icon'    => '<span class="dashicons dashicons-no"></span>',
							'message' => $this->db->last_error
						));
					}
					wp_redirect(admin_url('admin.php?page=' . $_GET['page']));
					exit;
				}
			}
		}
	}

	public function display()
	{
		$this->progressAction();
		$this->prepare_items();
		ob_start();
		parent::display();
		$buffer       = ob_get_clean();
		$main_content = '<form method="post" name="sr_main_form" action="?page=' . $_REQUEST['page'] . '">' . $buffer . '</form>';
		$add_new_url  = '<a href="' . admin_url('admin.php?page=' . $_REQUEST['page'] . '&action=edit&id=0') . '" class="add-new-h2">' . __('Add new') . '</a>';
		$message      = $this->session->getClear('message');

		$dom                     = new \DOMDocument;
		$dom->preserveWhiteSpace = false;
		$dom->loadHTML(str_replace('&', '&amp;', $main_content));
		$xpath   = new \DOMXpath($dom);
		$filters = $this->session->get('filters', array());
		foreach ($filters as $name => $value)
		{
			$value = trim($value);
			$nodes = $xpath->query('//*[@name="filters[' . $name . ']"]');
			foreach ($nodes as $node)
			{
				switch (strtoupper($node->nodeName))
				{
					case 'SELECT':
						$options = $node->getElementsByTagName('option');
						foreach ($options as $option)
						{
							if ($option->getAttribute('value') == $value)
							{
								$option->setAttribute('selected', 'selected');
							}
						}
						break;

					case 'INPUT':
						$node->setAttribute('value', $value);
						break;
					case 'TEXTAREA':
						$node->nodeValue = $value;
						break;

				}
			}

		}
		$form         = $dom->getElementsByTagName('form');
		$main_content = str_replace('&amp;', '&', $dom->saveHTML($form->item(0)));

		if (null !== $message && is_array($message))
		{
			$main_content = '<div class="updated">' . @$message['icon'] . ' ' . @$message['message'] . '</div>' . $main_content;
		}
		$html = array(
			'<div class="wrap ' . $_REQUEST['page'] . '">',
			'   <div id="icon-users" class="icon32"><br/></div>',
			'   <h2>' . esc_html($this->title) . $add_new_url . '</h2>',
			'   ' . $main_content,
			'</div>'
		);
		$html = join("\n", $html);


		echo $html;

	}

	public function extra_tablenav($which)
	{
		if ('top' == $which && !empty($this->data_grid['extra_tablenav']))
		{
			settype($this->data_grid['extra_tablenav'], 'array');
			echo '<div class="alignleft actions bulkactions">' . join("\n", $this->data_grid['extra_tablenav']) . '</div>';
		}

	}

}