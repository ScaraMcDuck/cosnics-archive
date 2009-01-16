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
require_once Path :: get_user_path().'lib/user_role.class.php';
require_once Path :: get_group_path().'lib/group_role.class.php';
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
	
	function delete_role_right_locations($condition)
	{
		$query = 'DELETE FROM '. $this->escape_table_name('role_right_location');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		
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
		return new RoleRightLocation($defaultProp);
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
			$defaultProperties = array();
			
			$defaultProperties[RoleRightLocation :: PROPERTY_ROLE_ID] = $role_id;
			$defaultProperties[RoleRightLocation :: PROPERTY_RIGHT_ID] = $right_id;
			$defaultProperties[RoleRightLocation :: PROPERTY_LOCATION_ID] = $location_id;
			$defaultProperties[RoleRightLocation :: PROPERTY_VALUE] = 0;
			
			$rolerightlocation = new RoleRightLocation($defaultProperties);
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
			$translator = new ConditionTranslator($this, $params, true);
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
	
	function count_locations($condition = null)
	{
		$query = 'SELECT COUNT(*) FROM '.$this->escape_table_name('location');
		
		$params = array();
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}
	
	function count_roles($condition = null)
	{
		$query = 'SELECT COUNT(*) FROM '.$this->escape_table_name('role');
		
		$params = array();
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}
	
	function update_location($location)
	{
		$where = $this->escape_column_name(Location :: PROPERTY_ID).'='.$location->get_id();
		$props = array();
		foreach ($location->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('location'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}
	
	function add_nested_values($location, $previous_visited, $number_of_elements = 1)
	{
		// Update all necessary left-values
		$conditions = array();
		$conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
		$conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
		$condition = new AndCondition($conditions);
		
		$query = 'UPDATE '. $this->escape_table_name('location') .' SET '. $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . ' + ?';

		$params = array ();
		$params[] = $number_of_elements * 2;
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$statement = $this->connection->prepare($query);
		// TODO: Some error-handling please !
		$res = $statement->execute($params);
		
		// Update all necessary right-values
		$conditions = array();
		$conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
		$conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $previous_visited);
		$condition = new AndCondition($conditions);
		
		$query = 'UPDATE '. $this->escape_table_name('location') .' SET '. $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . ' + ?';

		$params = array ();
		$params[] = $number_of_elements * 2;
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$statement = $this->connection->prepare($query);
		// TODO: Some error-handling please !
		$res = $statement->execute($params);
		
		// TODO: For now we just return true ...
        return true;
	}
	
	function delete_location_nodes($location)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
		$conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $location->get_left_value());
		$conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $location->get_right_value());
		$condition = new AndCondition($conditions);
		
		$query = 'DELETE FROM '. $this->escape_table_name('location');

		$params = array ();
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$statement = $this->connection->prepare($query);
		// TODO: Some error-handling please !
		$statement->execute($params);
		
		// TODO: For now we just return true ...
        return true;
	}
	
	function delete_nested_values($location)
	{
        $delta = $location->get_right_value() - $location->get_left_value() + 1;
        
		// Update all necessary nested-values
		$conditions = array();
		$conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
		$conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $location->get_left_value());
		$condition = new AndCondition($conditions);
		
		$query  = 'UPDATE '. $this->escape_table_name('location');
		$query .= ' SET '. $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . ' - ?,';
		$query .= $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . ' - ?';

		$params = array ();
		$params[] = $delta;
		$params[] = $delta;
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$statement = $this->connection->prepare($query);
		// TODO: Some error-handling please !
		$statement->execute($params);
		
		// Update some more nested-values
		$conditions = array();
		$conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
		$conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $location->get_left_value());
		$conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $location->get_right_value());
		$condition = new AndCondition($conditions);
		
		$query  = 'UPDATE '. $this->escape_table_name('location');
		$query .= ' SET '. $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . ' - ?';

		$params = array ();
		$params[] = $delta;
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$statement = $this->connection->prepare($query);
		// TODO: Some error-handling please !
		$statement->execute($params);
		
        return true;
	}
	
	function move_location($location, $new_parent_id, $new_previous_id = 0)
	{
        // Check some things first to avoid trouble
        if ($new_previous_id)
        {
            // Don't let people move an element behind itself
            // No need to spawn an error, since we're just not doing anything
            if ($new_previous_id == $location->get_id())
            {
                return true;
            }
            
            $new_previous = $this->retrieve_location($new_previous_id);
            // TODO: What if location $new_previous_id doesn't exist ? Return error.
            $new_parent_id = $new_previous->get_parent();
        }
        else
        {
        	// No parent ID was set ... problem !
            if ($new_parent_id == 0)
            {
                return false;
            }
            // Move the location underneath one of it's children ?
            // I think not ... Return error
            if ($location->is_child_of($new_parent_id))
            {
            	return false;
            }
            // Move an element underneath itself ?
            // No can do ... just ignore and return true
            if ($new_parent_id == $location->get_id())
            {
                return true;
            }
            // Try to retrieve the data of the parent element
            $new_parent = $this->retrieve_location($new_parent_id);
            // TODO: What if this is an invalid location ? Return error. 
        }

        $number_of_elements = ($location->get_right_value() - $location->get_left_value() + 1) / 2;
        $previous_visited = $new_previous_id ? $new_previous->get_right_value() : $new_parent->get_left_value();
        
        // Update the nested values so we can actually add the element
        // Return false if this failed
        if (!$this->add_nested_values($location, $previous_visited, $number_of_elements))
        {
        	return false;
        }
        
        // Now we can update the actual parent_id
        // Return false if this failed
        $location->set_parent($new_parent_id);
        if (!$location->update())
        {
        	return false;
        }

        // Update the left/right values of those elements that are being moved

        // First get the offset we need to add to the left/right values
        // if $newPrevId is given we need to get the right value,
        // otherwise the left since the left/right has changed
        // because we already updated it up there. We need to get them again.
        // We have to do that anyway, to have the proper new left/right values
        if ($new_previous_id)
        {
        	$temp = $this->retrieve_location($new_previous_id);
        	// TODO: What if $temp doesn't exist ? Return error.
        	$calculate_width = $temp->get_right_value();
        }
        else
        {
        	$temp = $this->retrieve_location($new_parent_id);
        	// TODO: What if $temp doesn't exist ? Return error.
        	$calculate_width = $temp->get_left_value();
        }

        // Get the element that is being moved again, since the left and
        // right might have changed by the add-call
        
        $location = $this->retrieve_location($location->get_id());
        // TODO: What if $location doesn't exist ? Return error.
        
        // Calculate the offset of the element to to the spot where it should go
        // correct the offset by one, since it needs to go inbetween!
        $offset = $calculate_width - $location->get_left_value() + 1;
        
        // Do the actual update
		$conditions = array();
		$conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $location->get_application());
		$conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, ($location->get_left_value() - 1));
		$conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, ($location->get_right_value() + 1));
		$condition = new AndCondition($conditions);
		
		$query  = 'UPDATE '. $this->escape_table_name('location');
		$query .= ' SET '. $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_LEFT_VALUE) . ' + ?,';
		$query .= $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . '=' . $this->escape_column_name(Location :: PROPERTY_RIGHT_VALUE) . ' + ?';

		$params = array ();
		$params[] = $offset;
		$params[] = $offset;
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$statement = $this->connection->prepare($query);
		// TODO: Some error-handling please !
		$statement->execute($params);
		
		// Remove the subtree where the location was before
		if (!$this->delete_nested_values($location))
		{
			return false;
		}

        return true;
	}
	
	function update_role($role)
	{
		$where = $this->escape_column_name(Role :: PROPERTY_ID).'='.$role->get_id();
		$props = array();
		foreach ($role->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('role'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}
	
	function delete_role($role)
	{		
		// Delete all role_right_locations for that specific role
		$condition = new EqualityCondition(RoleRightLocation :: PROPERTY_ROLE_ID, $role->get_id());
		$this->delete_role_right_locations($condition);
		
		// Delete all links between this role and users
		// Code comes here ...
		$udm = UserDataManager :: get_instance();
		
		$condition = new EqualityCondition(UserRole :: PROPERTY_ROLE_ID, $role->get_id());
		$udm->delete_user_roles($condition);
				
		// Delete all links between this role and groups
		// Code comes here ...
		$gdm = GroupDataManager :: get_instance();
		
		$condition = new EqualityCondition(GroupRole :: PROPERTY_ROLE_ID, $role->get_id());
		$gdm->delete_group_roles($condition);
		
		// Delete the actual role
		$query = 'DELETE FROM '.$this->escape_table_name('role').' WHERE '.$this->escape_column_name(Role :: PROPERTY_ID).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(array($role->get_id()));
		
		return true;
	}
}
?>
