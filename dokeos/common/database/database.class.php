<?php
/**
 * @package common.database;
 */

require_once dirname(__FILE__) . '/object_result_set.class.php';
require_once dirname(__FILE__) . '/connection.class.php';

/**
 * This class provides basic functionality for database connections
 * Create Table, Get next id, Insert, Update, Delete, 
 * Select(with use of conditions), Count(with use of conditions)
 * @author Sven Vanpoucke
 */
class Database
{
	private $connection;
	private $prefix;
	private $aliases;
	
	/**
	 * Constructor
	 */
	function Database($aliases)
	{
		$this->aliases = $aliases;
		$this->initialize();
	}
	
	/**
	 * Initialiser, creates the connection and sets the database to UTF8
	 */
	function initialize()
	{
		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));
		$this->connection->query('SET NAMES utf8'); 
	}
	
	/**
	 * Returns the prefix
	 * @return String the prefix
	 */
	function get_prefix()
	{
		return $this->prefix;
	}
	
	/**
	 * Sets the prefix
	 * @param String $prefix
	 */
	function set_prefix($prefix)
	{
		$this->prefix = $prefix;		
	}
	
	/**
	 * Returns the connection
	 * @return Connection the connection
	 */
	function get_connection()
	{
		return $this->connection;
	}
	
	/**
	 * Sets the connection
	 * @param Connection $connection
	 */
	function set_connection($connection)
	{
		$this->connection = $connection;
	}
	
	/**
	 * Debug function
	 * Uncomment the lines if you want to debug
	 */
	function debug()
	{
		$args = func_get_args();
		// Do something with the arguments
		if($args[1] == 'query')
		{
//			echo '<pre>';
//		 	echo($args[2]);
//		 	echo '</pre>';
		}
	}
	
	/**
	 * Escapes a column name in accordance with the database type.
	 * @param string $name The column name.
	 * @param boolean $prefix_properties Whether or not to
	 *                                                   prefix properties
	 *                                                   to avoid collisions.
	 * @return string The escaped column name.
	 */
	function escape_column_name($name, $prefix_properties = null)
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
		elseif ($prefix_properties) 
		{
			$prefix = $prefix_properties . '.';
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
	
		
	/**
	 * Maps a record to a class object
	 * @param Record $record record from database
	 * @param String $classname Class name to create new object
	 * @return new object from classname
	 */
	function record_to_classobject($record, $classname)
	{
	    if (!is_array($record) || !count($record))
		{
		    throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		 
		$class = new $classname($defaultProp);
		 
		foreach ($class->get_default_property_names() as $prop)
		{
		    $defaultProp[$prop] = $record[$prop]; 
		}
		
		$class->set_default_properties($defaultProp);
		 
		return $class;
	}
	
	/**
	 * Creates a storage unit in the system
	 * @param String $name the table name
	 * @param Array $properties the table properties
	 * @param Array $indexes the table indexes
	 * @return true if the storage unit is succesfully created
	 */
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
	 * Retrieves the next id for a given table
	 * @param String $table_name
	 * @return Int the id
	 */
	function get_next_id($table_name)
	{
		$id = $this->connection->nextID($this->get_table_name($table_name));
		return $id;
	}
	
	/**
	 * 
	 */
	function create($object, $table_name)
	{
		$props = array();
		foreach ($object->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name($table_name), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Update functionality (can only be used when table has an ID)
	 * @param Object $object the object that has to be updated
	 * @param String $table_name the table name 
	 * @param Condition $condition The condition for the item that has to be updated
	 * @return True if update is successfull
	 */
	function update($object, $table_name, $condition)
	{
		$props = array();
		foreach ($object->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name($table_name), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		
		return true;
	}
	
	/**
	 * Deletes an object from a table with a given condition
	 * @param String $table_name
	 * @param Condition $condition
	 * @return true if deletion is successfull
	 */
	function delete($table_name, $condition)
	{
		$query = 'DELETE FROM '.$this->escape_table_name($table_name).' WHERE '.$condition;
		$sth = $this->connection->prepare($query);
		if($res = $sth->execute())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Counts the objects of a table with a given condition
	 * @param String $table_name
	 * @param Condition $condition
	 * return Int the number of objects
	 */
	function count_objects($table_name, $condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT(*) FROM '.$this->escape_table_name($table_name);
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, null);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}
	
	/**
	 * Retrieves the objects of a given table
	 * @param String $table_name
	 * @param String $classname The name of the class where the object has to be mapped to
	 * @param Condition $condition the condition
	 * @param Int $offset the starting offset
	 * @param Int $maxObjects the max amount of objects to be retrieved
	 * @param Array(String) $orderBy the list of column names that the objects have to be ordered by
	 * @param Array(String) $orderDir the list of order directions for the orderBy list
	 * @return ResultSet 
	 */
	function retrieve_objects($table_name, $classname, $condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name($table_name). ' AS '. $this->get_alias($table_name);

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$order = array ();
		
		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i], $this->get_alias($table_name)).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
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
		return new ObjectResultSet($this, $res, $classname);
	}
	
	function get_alias($table_name)
	{
		return $this->aliases[$table_name];
	}

	/**
	 * Function to check whether a column is a date column or not
	 * @param String $name the column name
	 * @return false (default value)
	 */
	static function is_date_column($name)
	{
		return false;
	}

}

?>