<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_class_group_result_set.class.php';
require_once dirname(__FILE__).'/database/database_class_group_rel_user_result_set.class.php';
require_once dirname(__FILE__).'/../class_group_data_manager.class.php';
require_once dirname(__FILE__).'/../class_group.class.php';
require_once dirname(__FILE__).'/../class_group_rel_user.class.php';
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

class DatabaseClassGroupDataManager extends ClassGroupDataManager
{
	const ALIAS_CLASSGROUP_TABLE = 'g';

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
		
		$this->prefix = 'class_group_';
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
		elseif ($prefix_user_object_properties && self :: is_classgroup_column($name))
		{
			$prefix = self :: ALIAS_CLASSGROUP_TABLE.'.';
		}
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
	
	private static function is_classgroup_column($name)
	{
		return ClassGroup :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_GROUP_ID;
	}
	
	/**
	 * Checks whether the given column name is the name of a column that
	 * contains a date value, and hence should be formatted as such.
	 * @param string $name The column name.
	 * @return boolean True if the column is a date column, false otherwise.
	 */
	static function is_date_column($name)
	{
		return ($name == LearningObject :: PROPERTY_CREATION_DATE || $name == LearningObject :: PROPERTY_MODIFICATION_DATE);
	}
	
	function update_classgroup($classgroup)
	{
		$where = $this->escape_column_name(ClassGroup :: PROPERTY_ID).'='.$classgroup->get_id();
		$props = array();
		foreach ($classgroup->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('class_group'), $props, MDB2_AUTOQUERY_UPDATE, $where);

		return true;
	}
	
	function get_next_classgroup_id()
	{
		$id = $this->connection->nextID($this->get_table_name('class_group'));
		return $id;
	}
	
	function delete_classgroup($classgroup)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('class_group').' WHERE '.$this->escape_column_name(ClassGroup :: PROPERTY_ID).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(array($classgroup->get_id()));
		
		return true;
	}
	
	function truncate_classgroup($classgroup)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('class_group_rel_user').' WHERE '.$this->escape_column_name(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID).'=?';
		$sth = $this->connection->prepare($query);
		if($sth->execute(array($classgroup->get_id())))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function delete_classgroup_rel_user($classgroupreluser)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('class_group_rel_user').' WHERE '.$this->escape_column_name(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID).'=? AND '.$this->escape_column_name(ClassGroupRelUser :: PROPERTY_USER_ID).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(array($classgroupreluser->get_classgroup_id(), $classgroupreluser->get_user_id()));
		
		return true;
	}
	
	function create_classgroup($classgroup)
	{
		$props = array();
		foreach ($classgroup->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(ClassGroup :: PROPERTY_ID)] = $classgroup->get_id();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('class_group'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_classgroup_rel_user($classgroupreluser)
	{
		$props = array();
		$props[$this->escape_column_name(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID)] = $classgroupreluser->get_classgroup_id();
		$props[$this->escape_column_name(ClassGroupRelUser :: PROPERTY_USER_ID)] = $classgroupreluser->get_user_id();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('class_group_rel_user'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
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
	
	function retrieve_classgroup($id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('class_group').' WHERE '.$this->escape_column_name(ClassGroup :: PROPERTY_ID).'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_classgroup($record);
	}
	
	function record_to_classgroup($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (ClassGroup :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new ClassGroup($record[ClassGroup :: PROPERTY_ID], $defaultProp);
	}
	
	function record_to_classgroup_rel_user($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		return new ClassGroupRelUser($record[ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID], $record[ClassGroupRelUser :: PROPERTY_USER_ID]);
	}
	
	function count_classgroups($condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(ClassGroup :: PROPERTY_ID).') FROM '.$this->escape_table_name('class_group');
		
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
	
	function count_classgroup_rel_users($condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT(*) FROM '.$this->escape_table_name('class_group_rel_user');
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
	
	function retrieve_classgroups($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('class_group'). ' AS '. self :: ALIAS_CLASSGROUP_TABLE;

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
		$orderBy[] = ClassGroup :: PROPERTY_NAME;
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
		return new DatabaseClassGroupResultSet($this, $res);
	}
	
	function retrieve_classgroup_rel_users($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('class_group_rel_user');

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

		$order = array ();
		
//		for ($i = 0; $i < count($orderBy); $i ++)
//		{
//			$order[] = $this->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
//		}
//		if (count($order))
//		{
//			$query .= ' ORDER BY '.implode(', ', $order);
//		}
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}
		$this->connection->setLimit(intval($maxObjects),intval($offset));
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseClassGroupRelUserResultSet($this, $res);
	}
	
	function retrieve_classgroup_rel_user($user_id, $group_id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('class_group_rel_user');
		
		$params = array ();
		$conditions = array();		
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_USER_ID, $user_id);
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $group_id);
		$condition = new AndCondition($conditions);
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
//		echo($query);
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		
		if ($res->numRows() >= 1)
		{
			$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$res->free();
			return self :: record_to_classgroup_rel_user($record);
		}
		else
		{
			return null;
		}
	}
}
?>