<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_role_result_set.class.php';
require_once dirname(__FILE__).'/database/database_right_result_set.class.php';
require_once dirname(__FILE__).'/database/database_location_result_set.class.php';
require_once dirname(__FILE__).'/../rights_data_manager.class.php';
require_once dirname(__FILE__).'/../role.class.php';
require_once dirname(__FILE__).'/../right.class.php';
require_once dirname(__FILE__).'/../location.class.php';
require_once dirname(__FILE__).'/../role_right_location.class.php';
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

class DatabaseRightsDataManager extends RightsDataManager
{
	const ALIAS_USER_TABLE = 'u';

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
		
		$this->prefix = 'rights_';
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
		elseif ($prefix_user_object_properties && self :: is_user_column($name))
		{
			$prefix = self :: ALIAS_USER_TABLE.'.';
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
	
	private static function is_user_column($name)
	{
		return User :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
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
	
	function update_rolerightlocation($rolerightlocation)
	{
		$where = $this->escape_column_name(RoleRightLocation :: PROPERTY_RIGHT_ID).'='.$rolerightlocation->get_right_id() . ' AND ' . $this->escape_column_name(RoleRightLocation :: PROPERTY_LOCATION_ID).'='.$rolerightlocation->get_location_id() . ' AND ' . $this->escape_column_name(RoleRightLocation :: PROPERTY_ROLE_ID).'='.$rolerightlocation->get_role_id();
		$props = array();
		$props[$this->escape_column_name(RoleRightLocation :: PROPERTY_VALUE)] = $rolerightlocation->get_value();
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('role_right_location'), $props, MDB2_AUTOQUERY_UPDATE, $where);

		return true;
	}
	
	function delete_rolerightlocation($rolerightlocation)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('role_right_location').' WHERE '.$this->escape_column_name(RoleRightLocation :: PROPERTY_RIGHT_ID).'=? AND '.$this->escape_column_name(RoleRightLocation :: PROPERTY_ROLE_ID).'=? AND '.$this->escape_column_name(RoleRightLocation :: PROPERTY_LOCATION_ID).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(array($rolerightlocation->get_right_id(), $rolerightlocation->get_role_id(), $rolerightlocation->get_location_id()));
		
		return true;
	}
	
	//Inherited.
	function create_location($location)
	{
		$props = array();
		foreach ($location->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(Location :: PROPERTY_ID)] = $location->get_id();
		
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('location'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_right($right)
	{
		$props = array();
		foreach ($right->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(Right :: PROPERTY_ID)] = $right->get_id();
		
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('right'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_role($role)
	{
		$props = array();
		foreach ($role->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(Role :: PROPERTY_ID)] = $role->get_id();
		
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('role'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_rolerightlocation($rolerightlocation)
	{
		$props = array();
		foreach ($rolerightlocation->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(RoleRightLocation :: PROPERTY_RIGHT_ID)] = $rolerightlocation->get_right_id();
		$props[$this->escape_column_name(RoleRightLocation :: PROPERTY_ROLE_ID)] = $rolerightlocation->get_role_id();
		$props[$this->escape_column_name(RoleRightLocation :: PROPERTY_LOCATION_ID)] = $rolerightlocation->get_location_id();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('role_right_location'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//Inherited.	
	function get_next_role_id()
	{
		return $this->connection->nextID($this->get_table_name('role'));
	}
	
	//Inherited.	
	function get_next_right_id()
	{
		return $this->connection->nextID($this->get_table_name('right'));
	}
	
	//Inherited.	
	function get_next_location_id()
	{
		return $this->connection->nextID($this->get_table_name('location'));
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
	 * Parses a database record fetched as an associative array into a role.
	 * @param array $record The associative array.
	 * @return Role The role.
	 */
	function record_to_role($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Role :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new Role($record[Role :: PROPERTY_ID], $defaultProp);
	}

	function record_to_role_right_location($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (RoleRightLocation :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new RoleRightLocation($record[RoleRightLocation :: PROPERTY_RIGHT_ID], $record[RoleRightLocation :: PROPERTY_ROLE_ID], $record[RoleRightLocation :: PROPERTY_LOCATION_ID], $defaultProp);
	}
	
	/**
	 * Parses a database record fetched as an associative array into a right.
	 * @param array $record The associative array.
	 * @return Right The right.
	 */
	function record_to_right($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Right :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new Right($record[Right :: PROPERTY_ID], $defaultProp);
	}
	
	function record_to_location($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Location :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new Location($defaultProp);
	}
	
	function retrieve_location_id_from_location_string($location)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('location');
		$condition = new PatternMatchCondition(Location :: PROPERTY_NAME, $location);

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		
		if ($res->numRows() >= 1)
		{
			$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$res->free();
			return self :: record_to_location($record);
		}
		else
		{
			throw new Exception(Translation :: get('NoSuchLocation'));
		}
	}
	
	function retrieve_role_right_location($right_id, $role_id, $location_id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('role_right_location');
		$conditions = array();
		
		$conditions[] = new EqualityCondition('right_id', $right_id);
		$conditions[] = new EqualityCondition('role_id', $role_id);
		$conditions[] = new EqualityCondition('location_id', $location_id);
		
		$condition = new AndCondition($conditions);

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		
		if ($res->numRows() >= 1)
		{
			$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$res->free();
			return self :: record_to_role_right_location($record);
		}
		else
		{
			$rolerightlocation = new RoleRightLocation($right_id, $role_id, $location_id);
			$rolerightlocation->set_value('0');
			$rolerightlocation->create();
			return $rolerightlocation;
		}
	}
	
	function retrieve_roles($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('role');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
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
		return new DatabaseRoleResultSet($this, $res);
	}
	
	function retrieve_location($id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('location') . ' WHERE '.$this->escape_column_name(Location :: PROPERTY_ID).'=?';;
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_location($record);
	}
	
	function retrieve_right($id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('right') . ' WHERE '.$this->escape_column_name(Right :: PROPERTY_ID).'=?';;
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_right($record);
	}
	
	function retrieve_role($id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('role') . ' WHERE '.$this->escape_column_name(Role :: PROPERTY_ID).'=?';;
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_role($record);
	}
	
	function retrieve_rights($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('right');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
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
		return new DatabaseRightResultSet($this, $res);
	}
	
	function retrieve_locations($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('location');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$orderBy[] = Location :: PROPERTY_LOCATION;
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
		return new DatabaseLocationResultSet($this, $res);
	}
}
?>
