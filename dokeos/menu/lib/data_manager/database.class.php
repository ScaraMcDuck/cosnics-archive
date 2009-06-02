<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_menu_item_result_set.class.php';
require_once dirname(__FILE__).'/../menu_data_manager.class.php';
require_once dirname(__FILE__).'/../menu_item.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
==============================================================================
 */

class DatabaseMenuDataManager extends MenuDataManager
{
	const ALIAS_CATEGORY_TABLE = 'c';
	const ALIAS_ITEM_TABLE = 'i';
	const ALIAS_MAX_SORT = 'max_sort';

	/**
	 * The database connection.
	 */
	private $connection;

	/**
	 * The table name prefix, if any.
	 */
	private $prefix;

	function initialize()
	{
		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));
		
		$this->prefix = 'menu_';
		$this->connection->query('SET NAMES utf8');
	}
	
	function debug()
	{
		$args = func_get_args();
		// Do something with the arguments
		if($args[1] == 'query')
		{
			//echo '<pre>';
		 	//echo($args[2]);
		 	//echo '</pre>';
		}
	}
	
	/**
	 * Escapes a column name in accordance with the database type.
	 * @param string $name The column name.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The escaped column name.
	 */
	function escape_column_name($name, $prefix_user_object_properties = false)
	{
		// Check whether the name contains a seperator, avoids notices.
		$contains_table_name = strpos($name, '.');
		if ($contains_table_name === false)
		{
			$table = $name;
			$column = null;
		}
		else
		{
			list($table, $column) = explode('.', $name, 2);
		}
		
		$prefix = '';
		if (isset($column))
		{
			$prefix = $table.'.';
			$name = $column;
		}
//		elseif ($prefix_user_object_properties && self :: is_menu_item_column($name))
//		{
//			$prefix = self :: ALIAS_ITEM_TABLE.'.';
//		}
		return $prefix.$this->connection->quoteIdentifier($name);
	}
	
	/**
	 * Expands a table identifier to the real table name. Currently, this
	 * method prefixes the given table name with the user-defined prefix, if
	 * any.
	 * @param string $name The table identifier.
	 * @return string The actual table name.
	 */
	function get_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		return $dsn['database'].'.'.$this->prefix.$name;
	}

	/**
	 * Escapes a table name in accordance with the database type.
	 * @param string $name The table identifier.
	 * @return string The escaped table name.
	 */
	function escape_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		$database_name = $this->connection->quoteIdentifier($dsn['database']);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
	}
		
	private static function is_menu_item_column($name)
	{
		return MenuItem :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
	}
	
	function get_next_menu_item_id()
	{
		$id = $this->connection->nextID($this->get_table_name('item'));
		return $id;
	}
	
	function create_storage_unit($name,$properties,$indexes)
	{
		$name = $this->get_table_name($name);
		$this->connection->loadModule('Manager');
		$manager = $this->connection->manager;
		// If table allready exists -> drop it
		// @todo This should change: no automatic table drop but warning to user
		$tables = $manager->listTables();
		if( in_array($name,$tables))
		{
			$manager->dropTable($name);
		}
		$options['charset'] = 'utf8';
		$options['collate'] = 'utf8_unicode_ci';
		if (!MDB2 :: isError($manager->createTable($name,$properties,$options)))
		{
			foreach($indexes as $index_name => $index_info)
			{
				if($index_info['type'] == 'primary')
				{
					$index_info['primary'] = 1;
					if (MDB2 :: isError($manager->createConstraint($name,$index_name,$index_info)))
					{
						return false;
					}
				}
				else
				{
					if (MDB2 :: isError($manager->createIndex($name,$index_name,$index_info)))
					{
						return false;
					}
				}
			}
			return true;
		}
		else
		{
			return false;
		}

	}
	
	function count_menu_items($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(MenuItem :: PROPERTY_ID).') FROM '.$this->escape_table_name('item').' AS '. self :: ALIAS_ITEM_TABLE;

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}
	
	function retrieve_menu_items($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('item'). ' AS '. self :: ALIAS_CATEGORY_TABLE;

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		/*
		 * Always respect display order as a last resort.
		 */
		$orderBy[] = MenuItem :: PROPERTY_SORT;
		$orderDir[] = SORT_ASC;
		$order = array ();
		
		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
		}
		if (count($order))
		{
			$query .= ' ORDER BY '.implode(', ', $order);
		}
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}
		$this->connection->setLimit(intval($maxObjects),intval($offset));
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseMenuItemResultSet($this, $res);
	}
	
    function retrieve_menu_item($id)
	{
		
		$query = 'SELECT * FROM '.$this->escape_table_name('item');
		$query .= ' WHERE '.$this->escape_column_name(MenuItem :: PROPERTY_ID).'=?';
		
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_menu_item($record);
	}
	
	function retrieve_menu_item_at_sort($parent, $sort, $direction)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('item') .' WHERE '.$this->escape_column_name(MenuItem :: PROPERTY_CATEGORY).'=?';
		if ($direction == 'up')
		{
			$query .= ' AND '.$this->escape_column_name(MenuItem :: PROPERTY_SORT).'<? ORDER BY '.$this->escape_column_name(MenuItem :: PROPERTY_SORT) . 'DESC';
		}
		elseif ($direction == 'down')
		{
			$query .= ' AND '.$this->escape_column_name(MenuItem :: PROPERTY_SORT).'>? ORDER BY '.$this->escape_column_name(MenuItem :: PROPERTY_SORT) . 'ASC';
		}
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute(array ($parent, $sort));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_menu_item($record);
	}
	
	function record_to_menu_item($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (MenuItem :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new MenuItem($record[MenuItem :: PROPERTY_ID], $defaultProp);
	}
	
	function update_menu_item($menu_item)
	{
		$where = $this->escape_column_name(MenuItem :: PROPERTY_ID).'='.$menu_item->get_id();
		$props = array();
		foreach ($menu_item->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		
		$old_menu_item = $this->retrieve_menu_item($menu_item->get_id());
		
		if ($old_menu_item->get_category() !== $menu_item->get_category())
		{
			$condition = new EqualityCondition(MenuItem :: PROPERTY_CATEGORY, $menu_item->get_category());
			$sort = $this->retrieve_max_sort_value('item', MenuItem :: PROPERTY_SORT, $condition);

			$props[$this->escape_column_name(MenuItem :: PROPERTY_SORT)] = $sort+1;
		}
		
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('item'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		
		
		if($old_menu_item->get_category() != $menu_item->get_category())
		{
			$query = 'UPDATE ' . $this->escape_table_name('item') . ' SET sort = sort - 1 WHERE ' . 
								 $this->escape_column_name(MenuItem :: PROPERTY_SORT) . ' > ? AND ' .
								 $this->escape_column_name(MenuItem :: PROPERTY_CATEGORY) . ' = ?;';
			
			$statement = $this->connection->prepare($query);
			$statement->execute(array($old_menu_item->get_sort(), $old_menu_item->get_category()));					
		}
		
		return true;
	}
	
	function delete_menu_item($menu_item)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('item').' WHERE '.$this->escape_column_name(MenuItem :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($menu_item->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function retrieve_max_sort_value($table, $column, $condition = null)
	{
		$query .= 'SELECT MAX('. $this->escape_column_name($column) .') as '. self :: ALIAS_MAX_SORT .' FROM'. $this->escape_table_name($table);

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		if ($res->numRows() >= 1)
		{
			$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
			$max = $record[0];
		}
		else
		{
			$max = 0;
		}
		
		return $max;
	}
	
	function create_menu_item($menu_item)
	{
		$props = array();
		foreach ($menu_item->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(MenuItem :: PROPERTY_ID)] = $menu_item->get_id();
		
		$condition = new EqualityCondition(MenuItem :: PROPERTY_CATEGORY, $menu_item->get_category());
		$sort = $this->retrieve_max_sort_value('item', MenuItem :: PROPERTY_SORT, $condition);
		
		$props[$this->escape_column_name(MenuItem :: PROPERTY_SORT)] = $sort+1;
		
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('item'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Checks whether the given column name is the name of a column that
	 * contains a date value, and hence should be formatted as such.
	 * @param string $name The column name.
	 * @return boolean True if the column is a date column, false otherwise.
	 */
	static function is_date_column($name)
	{
		return false;
	}
}
?>