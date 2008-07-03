<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_home_row_result_set.class.php';
require_once dirname(__FILE__).'/database/database_home_column_result_set.class.php';
require_once dirname(__FILE__).'/database/database_home_block_result_set.class.php';
require_once dirname(__FILE__).'/database/database_home_block_config_result_set.class.php';
require_once dirname(__FILE__).'/../home_data_manager.class.php';
require_once dirname(__FILE__).'/../home_row.class.php';
require_once dirname(__FILE__).'/../home_column.class.php';
require_once dirname(__FILE__).'/../home_block.class.php';
require_once dirname(__FILE__).'/../home_block_config.class.php';
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

class DatabaseHomeDataManager extends HomeDataManager
{
	const ALIAS_ROW_TABLE = 'r';
	const ALIAS_COLUMN_TABLE = 'c';
	const ALIAS_BLOCK_TABLE = 'b';
	const ALIAS_BLOCK_CONFIG_TABLE = 'bc';
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
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string'),array('debug'=>3,'debug_handler'=>array('DatabaseHomeDatamanager','debug')));
		$this->prefix = 'home_';
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
//		elseif ($prefix_user_object_properties && self :: is_home_column_column($name))
//		{
//			$prefix = self :: ALIAS_COLUMN_TABLE.'.';
//		}
//		elseif ($prefix_user_object_properties && self :: is_menu_item_column($name))
//		{
//			$prefix = self :: ALIAS_ITEM_TABLE.'.';
//		}
		return $prefix.$this->connection->quoteIdentifier($name);
	}
	
	private static function is_home_row_column($name)
	{
		return HomeRow :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
	}
	
	private static function is_home_column_column($name)
	{
		return HomeColumn :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
	}
	
	private static function is_home_block_column($name)
	{
		return HomeBlock :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
	}
	
	function get_next_home_row_id()
	{
		$id = $this->connection->nextID($this->get_table_name('row'));
		return $id;
	}
	
	function get_next_home_column_id()
	{
		$id = $this->connection->nextID($this->get_table_name('column'));
		return $id;
	}
	
	function get_next_home_block_id()
	{
		$id = $this->connection->nextID($this->get_table_name('block'));
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
	
	function count_home_rows($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(HomeRow :: PROPERTY_ID).') FROM '.$this->escape_table_name('row').' AS '. self :: ALIAS_ROW_TABLE;

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
	
	function count_home_columns($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(HomeColumn :: PROPERTY_ID).') FROM '.$this->escape_table_name('column').' AS '. self :: ALIAS_COLUMN_TABLE;

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
	
	function count_home_blocks($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(HomeBlock :: PROPERTY_ID).') FROM '.$this->escape_table_name('block').' AS '. self :: ALIAS_BLOCK_TABLE;

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
	
	function retrieve_home_rows($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('row'). ' AS '. self :: ALIAS_ROW_TABLE;

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
		$orderBy[] = HomeRow :: PROPERTY_SORT;
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
		return new DatabaseHomeRowResultSet($this, $res);
	}
	
    function retrieve_home_row($id)
	{
		
		$query = 'SELECT * FROM '.$this->escape_table_name('row');
		$query .= ' WHERE '.$this->escape_column_name(HomeRow :: PROPERTY_ID).'=?';
		
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_home_row($record);
	}
	
	function retrieve_home_columns($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('column'). ' AS '. self :: ALIAS_COLUMN_TABLE;

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
		$orderBy[] = HomeColumn :: PROPERTY_SORT;
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
		return new DatabaseHomeColumnResultSet($this, $res);
	}
	
    function retrieve_home_column($id)
	{
		
		$query = 'SELECT * FROM '.$this->escape_table_name('column');
		$query .= ' WHERE '.$this->escape_column_name(HomeColumn :: PROPERTY_ID).'=?';
		
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_home_column($record);
	}
	
	function retrieve_home_blocks($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('block'). ' AS '. self :: ALIAS_BLOCK_TABLE;

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
		$orderBy[] = HomeBlock :: PROPERTY_SORT;
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
		return new DatabaseHomeBlockResultSet($this, $res);
	}
	
    function retrieve_home_block($id)
	{
		
		$query = 'SELECT * FROM '.$this->escape_table_name('block');
		$query .= ' WHERE '.$this->escape_column_name(HomeBlock :: PROPERTY_ID).'=?';
		
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_home_block($record);
	}
	
	function record_to_home_row($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (HomeRow :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new HomeRow($record[HomeRow :: PROPERTY_ID], $defaultProp);
	}
	
	function record_to_home_column($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (HomeColumn :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new HomeColumn($record[HomeColumn :: PROPERTY_ID], $defaultProp);
	}
	
	function record_to_home_block($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (HomeBlock :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new HomeBlock($record[HomeBlock :: PROPERTY_ID], $defaultProp);
	}
	
	function record_to_home_block_config($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (HomeBlockConfig :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new HomeBlockConfig($record[HomeBlockConfig :: PROPERTY_BLOCK_ID], $defaultProp);
	}
	
	function create_home_block($home_block)
	{
		$props = array();
		foreach ($home_block->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(HomeBlock :: PROPERTY_ID)] = $home_block->get_id();
		
		$condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $home_block->get_column());
		$sort = $this->retrieve_max_sort_value('block', HomeBlock :: PROPERTY_SORT, $condition);
		
		$props[$this->escape_column_name(HomeBlock :: PROPERTY_SORT)] = $sort+1;
		
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('block'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_home_block_config($home_block_config)
	{
		$props = array();
		foreach ($home_block_config->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(HomeBlockConfig :: PROPERTY_BLOCK_ID)] = $home_block_config->get_block_id();
		
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('block_config'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_home_column($home_column)
	{
		$props = array();
		foreach ($home_column->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(HomeColumn :: PROPERTY_ID)] = $home_column->get_id();
		
		$condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $home_column->get_row());
		$sort = $this->retrieve_max_sort_value('column', HomeColumn :: PROPERTY_SORT, $condition);
		
		$props[$this->escape_column_name(HomeColumn :: PROPERTY_SORT)] = $sort+1;
		
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('column'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_home_row($home_row)
	{
		$props = array();
		foreach ($home_row->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(HomeRow :: PROPERTY_ID)] = $home_row->get_id();
		
		$sort = $this->retrieve_max_sort_value('row', HomeRow :: PROPERTY_SORT, null);
		
		$props[$this->escape_column_name(HomeRow :: PROPERTY_SORT)] = $sort+1;
		
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('row'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function truncate_home($user_id)
	{
		$failures = 0;
		
		$query = 'DELETE FROM '.$this->escape_table_name('block');
		$query .= ' WHERE '.$this->escape_column_name(HomeBlock :: PROPERTY_USER).'=?';
		$sth = $this->connection->prepare($query);
		if (!$sth->execute($user_id))
		{
			$failures++;
		}
		
		$query = 'DELETE FROM '.$this->escape_table_name('column');
		$query .= ' WHERE '.$this->escape_column_name(HomeColumn :: PROPERTY_USER).'=?';
		$sth = $this->connection->prepare($query);
		if (!$sth->execute($user_id))
		{
			$failures++;
		}
		
		$query = 'DELETE FROM '.$this->escape_table_name('row');
		$query .= ' WHERE '.$this->escape_column_name(HomeRow :: PROPERTY_USER).'=?';
		$sth = $this->connection->prepare($query);
		if (!$sth->execute($user_id))
		{
			$failures++;
		}
		
		if ($failures == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function update_home_block($home_block)
	{
		$where = $this->escape_column_name(HomeBlock :: PROPERTY_ID).'='.$home_block->get_id();
		$props = array();
		foreach ($home_block->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		
		$old_column = $this->retrieve_home_block($home_block->get_id());
		
		if ($old_column->get_column() !== $home_block->get_column())
		{
			$condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $home_block->get_column());
			$sort = $this->retrieve_max_sort_value('block', HomeBlock :: PROPERTY_SORT, $condition);
			
			$props[$this->escape_column_name(HomeBlock :: PROPERTY_SORT)] = $sort+1;
		}
		
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('block'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}
	
	function update_home_block_config($home_block_config)
	{
		$where = $this->escape_column_name(HomeBlockConfig :: PROPERTY_BLOCK_ID).'='.$home_block_config->get_block_id() . ' AND ' . $this->escape_column_name(HomeBlockConfig :: PROPERTY_VARIABLE).'= "'.$home_block_config->get_variable() . '"';
		$props = array();
		$props[HomeBlockConfig :: PROPERTY_VALUE] = $home_block_config->get_value();
		
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('block_config'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}
	
	function update_home_row($home_row)
	{
		$where = $this->escape_column_name(HomeRow :: PROPERTY_ID).'='.$home_row->get_id();
		$props = array();
		foreach ($home_row->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		
//		$old_category = $this->retrieve_encyclopedia_category($encyclopedia_category->get_id());
//		
//		if ($old_category->get_parent() !== $encyclopedia_category->get_parent())
//		{
//			$condition = new EqualityCondition(EncyclopediaCategory :: PROPERTY_PARENT, $encyclopedia_category->get_parent());
//			$sort = $this->retrieve_max_sort_value('encyclopedia_category', EncyclopediaCategory :: PROPERTY_SORT, $condition);
//			
//			$props[$this->escape_column_name(EncyclopediaCategory :: PROPERTY_SORT)] = $sort+1;
//		}
		
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('row'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}
	
	function update_home_column($home_column)
	{
		$where = $this->escape_column_name(HomeColumn :: PROPERTY_ID).'='.$home_column->get_id();
		$props = array();
		foreach ($home_column->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		
		$old_row = $this->retrieve_home_column($home_column->get_id());
		
		if ($old_row->get_row() !== $home_column->get_row())
		{
			$condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $home_column->get_row());
			$sort = $this->retrieve_max_sort_value('column', HomeColumn :: PROPERTY_SORT, $condition);
			
			$props[$this->escape_column_name(HomeColumn :: PROPERTY_SORT)] = $sort+1;
		}
		
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('column'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}
	
	function retrieve_home_block_at_sort($parent, $sort, $direction)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('block') .' WHERE '.$this->escape_column_name(HomeBlock :: PROPERTY_COLUMN).'=?';
		if ($direction == 'up')
		{
			$query .= ' AND '.$this->escape_column_name(HomeBlock :: PROPERTY_SORT).'<? ORDER BY '.$this->escape_column_name(HomeBlock :: PROPERTY_SORT) . 'DESC';
		}
		elseif ($direction == 'down')
		{
			$query .= ' AND '.$this->escape_column_name(HomeBlock :: PROPERTY_SORT).'>? ORDER BY '.$this->escape_column_name(HomeBlock :: PROPERTY_SORT) . 'ASC';
		}
		$res = $this->limitQuery($query, 1, null, array ($parent, $sort));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_home_block($record);
	}
	
	function retrieve_home_column_at_sort($parent, $sort, $direction)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('column') .' WHERE '.$this->escape_column_name(HomeColumn :: PROPERTY_ROW).'=?';
		if ($direction == 'up')
		{
			$query .= ' AND '.$this->escape_column_name(HomeColumn :: PROPERTY_SORT).'<? ORDER BY '.$this->escape_column_name(HomeColumn :: PROPERTY_SORT) . 'DESC';
		}
		elseif ($direction == 'down')
		{
			$query .= ' AND '.$this->escape_column_name(HomeColumn :: PROPERTY_SORT).'>? ORDER BY '.$this->escape_column_name(HomeColumn :: PROPERTY_SORT) . 'ASC';
		}
		$res = $this->limitQuery($query, 1, null, array ($parent, $sort));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_home_column($record);
	}
	
	function retrieve_home_row_at_sort($sort, $direction)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('row') .' WHERE ';
		if ($direction == 'up')
		{
			$query .= $this->escape_column_name(HomeRow :: PROPERTY_SORT).'<? ORDER BY '.$this->escape_column_name(HomeRow :: PROPERTY_SORT) . 'DESC';
		}
		elseif ($direction == 'down')
		{
			$query .= $this->escape_column_name(HomeRow :: PROPERTY_SORT).'>? ORDER BY '.$this->escape_column_name(HomeRow :: PROPERTY_SORT) . 'ASC';
		}
		$res = $this->limitQuery($query, 1, null, array ($sort));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_home_row($record);
	}
	
	private function limitQuery($query,$limit,$offset,$params,$is_manip = false)
	{
		$this->connection->setLimit($limit,$offset);
		$statement = $this->connection->prepare($query,null,($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		return $res;
	}
	
	function delete_home_row($home_row)
	{
		$condition = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $home_row->get_id());
		$columns = $this->retrieve_home_columns($condition);
		
		while($column = $columns->next_result())
		{
			$this->delete_home_column($column);
		}
		
		$query = 'DELETE FROM '.$this->escape_table_name('row').' WHERE '.$this->escape_column_name(HomeRow :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($home_row->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function delete_home_column($home_column)
	{
		$condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $home_column->get_id());
		$blocks = $this->retrieve_home_blocks($condition);
		
		while($block = $blocks->next_result())
		{
			$this->delete_home_block($block);
		}
		
		$query = 'DELETE FROM '.$this->escape_table_name('column').' WHERE '.$this->escape_column_name(HomeColumn :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($home_column->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function delete_home_block($home_block)
	{
		if (!$this->delete_home_block_configs($home_block))
		{
			return false;
		}
		
		$query = 'DELETE FROM '.$this->escape_table_name('block').' WHERE '.$this->escape_column_name(HomeBlock :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($home_block->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function delete_home_block_config($home_block_config)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('block_config').' WHERE '.$this->escape_column_name(HomeBlockConfig :: PROPERTY_BLOCK_ID).'=? AND '.$this->escape_column_name(HomeBlockConfig :: PROPERTY_VARIABLE).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute(array($home_block_config->get_block_id(), $home_block_config->get_variable())))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function delete_home_block_configs($home_block)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('block_config').' WHERE '.$this->escape_column_name(HomeBlockConfig :: PROPERTY_BLOCK_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($home_block->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function retrieve_home_block_config($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('block_config'). ' AS '. self :: ALIAS_BLOCK_CONFIG_TABLE;

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
		$orderBy[] = HomeBlockConfig :: PROPERTY_VARIABLE;
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
		return new DatabaseHomeBlockConfigResultSet($this, $res);
	}
	
	function count_home_block_config($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(HomeBlockConfig :: PROPERTY_BLOCK_ID).') FROM '.$this->escape_table_name('block_config').' AS '. self :: ALIAS_BLOCK_CONFIG_TABLE;

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