<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Wordpress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2015 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/
namespace SR\Query;
if (!defined('ABSPATH'))
{
	exit;
}

class SR_Query
{
	private $select = null;
	private $from = null;
	private $where = null;
	private $join = null;
	private $order = null;
	private $limit = null;
	private $group = null;
	private $query = null;
	private $db;

	public function __construct($config = array())
	{
		global $wpdb;
		$this->db = $wpdb;
	}

	public function getDbo()
	{
		return $this->db;
	}

	public function qn($text)
	{
		return '`' . (string) $text . '`';
	}

	public function q($text)
	{
		return '\'' . esc_sql((string) $text) . '\'';
	}

	public function select($string)
	{
		if (empty($this->select))
		{
			$this->select = '';
		}
		else
		{
			$this->select .= ',';
		}
		$string = trim((string) $string);
		$this->select .= $string;

		return $this;
	}

	public function from($table, $as_name = null)
	{
		if (!empty($table) && is_string($table))
		{
			$this->from = $table;
			if (!empty($as_name))
			{
				$this->from .= ' AS ' . (string) $as_name;
			}
		}

		return $this;
	}

	public function join($type = 'join', $table, $condition)
	{

		switch (strtoupper($type))
		{
			case 'JOIN':
				$join_type = 'JOIN';
				break;
			case 'LEFT':
				$join_type = 'LEFT JOIN';
				break;
			case 'RIGHT':
				$join_type = 'RIGHT JOIN';
				break;
			case 'INNER':
				$join_type = 'INNER JOIN';
				break;
		}
		if (empty($this->join))
		{
			$this->join = '';
		}
		else
		{
			$this->join .= ' ';
		}
		$this->join .= $join_type . ' ' . $table . ' ON ' . $condition;

		return $this;
	}

	public function leftJoin($table, $condition)
	{
		return $this->join('LEFT', $table, $condition);
	}

	public function innerJoin($table, $condition)
	{
		return $this->join('INNER', $table, $condition);
	}

	public function rightJoin($table, $condition)
	{
		return $this->join('RIGHT', $table, $condition);
	}

	public function where($condition, $concat = 'AND')
	{
		if (empty($this->where))
		{
			$this->where = '(';
		}
		else
		{
			$this->where .= ' ' . strtoupper($concat) . ' (';
		}
		if (is_array($condition))
		{
			$n = count($condition);
			$i = 0;
			foreach ($condition as $field => $value)
			{
				if (preg_match('#[=><]|(<=)|(>=)#', $field))
				{
					$this->where .= $field . $this->q($value);
				}
				if (preg_match('#(LIKE)#', $field))
				{
					$this->where .= $field . $this->q('%' . $value . '%');
				}
				else
				{
					$this->where .= $field . ' = ' . $this->q($value);
					if (++$i < $n)
					{
						$this->where .= ' AND ';
					}
				}
			}
		}
		elseif (is_string($condition))
		{
			$this->where .= $condition;
		}
		$this->where .= ')';

		return $this;

	}

	public function orWhere($condition)
	{
		return $this->where($condition, 'OR');
	}

	public function andWhere($condition)
	{
		return $this->where($condition, 'AND');
	}

	public function like($field, $text)
	{
		return $this->where(array($field . ' LIKE ' => $text));
	}

	public function order($ordering, $direction = null)
	{
		if ($ordering == 'RAND()')
		{
			$this->order = 'ORDER BY ' . $ordering;
		}
		else
		{
			$this->order = 'ORDER BY ' . $ordering . ' ' . $direction;
		}

		return $this;
	}

	public function limit($start = 0, $limit = 0)
	{
		$this->limit = 'LIMIT ' . (int) $start . ', ' . (int) $limit;

		return $this;
	}

	public function group($group)
	{
		if (!empty($group) && is_string($group))
		{
			$this->group = 'GROUP BY ' . (string) $group;
		}

		return $this;
	}

	public function toString()
	{
		$string = array();
		if (!empty($this->select))
		{
			$string[] = 'SELECT ' . $this->select;
		}
		if (!empty($this->from))
		{
			$string[] = 'FROM ' . trim($this->from);
		}
		if (!empty($this->join))
		{
			$string[] = $this->join;
		}
		if (!empty($this->where))
		{
			$string[] = 'WHERE ' . $this->where;
		}
		if (!empty($this->group))
		{
			$string[] = $this->group;
		}
		if (!empty($this->order))
		{
			$string[] = $this->order;
		}
		if (!empty($this->limit))
		{
			$string[] = $this->limit;
		}

		return trim(join(' ', $string));

	}

	public function clear($property = null)
	{
		if (is_null($property))
		{
			$this->select = null;
			$this->from   = null;
			$this->where  = null;
			$this->join   = null;
			$this->order  = null;
			$this->limit  = null;
			$this->query  = null;
			$this->group  = null;
		}
		elseif (property_exists($this, $property))
		{
			$this->{$property} = null;
		}

		return $this;
	}

	public function getItems()
	{
		try
		{
			$items = $this->db->get_results($this->getQuery());
			$this->clear('query');
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
			exit;
		}

		return (array) $items;
	}

	public function getQuery()
	{
		if (empty($this->query))
		{
			$this->query = $this->toString();
		}

		return $this->query;
	}

	public function setQuery($query)
	{
		$this->query = $query;

		return $this;
	}

	public function execute()
	{
		return $this->db->query($this->getQuery());
	}

	public function get($table, $fields = array(), $where = array())
	{
		$query = clone $this;
		$query->from($query->qn($table));
		if (!empty($where))
		{
			$query->where($where);
		}
		if (is_array($fields) && count($fields) > 1)
		{
			return $query->select(join(', ', $fields))
				->getItems();
		}
		$query->select($fields);

		return $this->db->get_var($query->toString());

	}

	public function insert($table, $data = array(), $format = null)
	{
		return $this->db->insert($table, $data, $format);
	}

	public function update($table, $data = array(), $where = array(), $format = null, $where_format = null)
	{
		return $this->db->update($table, $data, $where, $format, $where_format);
	}

	public function delete($table, $where, $where_format = null)
	{
		return $this->db->delete($table, $where, $where_format);
	}

	public function getTable($table)
	{
		if (!preg_match('#^(' . $this->db->prefix . ')#', $table))
		{
			$table = $this->db->prefix . $table;
		}

		return $table;
	}

	public function getProperty($property, $default = null)
	{
		return property_exists($this, $property) ? $this->{$property} : $default;
	}

}