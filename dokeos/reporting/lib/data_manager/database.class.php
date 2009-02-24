<?php
/**
 * @package tracking.lib.datamanager
 */
require_once dirname(__FILE__).'/../reporting_data_manager.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once dirname(__FILE__).'/database/databasereportingblockresultset.class.php';

class DatabaseReportingDataManager extends ReportingDataManager
{
	const ALIAS_REPORTINGBLOCK_TABLE = 'rpb';

	/**
	 * The database connection.
	 */
	private $connection;

	/** 
	 * Table prefix
	 */
	private $prefix;

	// Inherited.
	function initialize()
	{
		PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array (get_class(), 'handle_error'));
		
		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));
		
		if (PEAR::isError($this))
		{
			die($this->connection->getMessage());
		}
		$this->prefix = 'reporting_';
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
	 * Gets the table name from the dsn . prefix
	 * @param String $name the tablename
	 * @return String the prefixed tablename
	 */
	function get_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		return $dsn['database']. '.' . $this->prefix . $name;
	}
	
	/**
	 * Inherited
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
	 * Retrieves the tables of this database
	 */
	function get_tables()
	{
		$this->connection->loadModule('Manager');
		$manager = $this->connection->manager;
		return $manager->listTables();
	}
	
	
	/**
	 * Escapes a column name in accordance with the database type.
	 * @param string $name The column name.
	 * @param boolean $prefix_contentbox_properties Whether or not to
	 *                                                   prefix contentbox properties
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
			$prefix = self :: ALIAS_CONTENTBOX_TABLE.'.';
		}
		return $prefix.$this->connection->quoteIdentifier($name);
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
	 * Handles pear errors
	 */
	static function handle_error($error)
	{
		die(__FILE__.':'.__LINE__.': '.$error->getMessage()
		// For debugging only. May create a security hazard.
		.' ('.$error->getDebugInfo().')');
	}
	
	
	/**
	 * Checks whether the given name is a user column
	 * @param String $name The column name
	 */
	private static function is_user_column($name)
	{
		return false;
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
		$properties = array ();
		 
		$class = new $classname($properties);
		 
		foreach ($class->get_property_names() as $prop)
		{
		    $properties[$prop] = $record[$prop]; 
		}
		
		$class->set_properties($properties);
		 
		return $class;
	}
	
	/**
	 * Retrieves the next id from the given table
	 * @param string $tablename the tablename
	 */
	function get_next_id($tablename)
	{
		$id = $this->connection->nextID($this->get_table_name($tablename));
		return $id;
	}
	
	/**
	 * Creates a reporting block in the database
	 * @param ReportingBlock $reporting_block
	 */
	function create_block(&$reporting_block)
	{
		$props = array();
		foreach ($reporting_block->get_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('reporting_block'), $props, MDB2_AUTOQUERY_INSERT);
		
		return true;
	}
	
	/**
	 * Updates an reporting block (needed for change of activity)
	 * @param ReportingBlock $reporting_block
	 */
	function update_block(&$reporting_block)
	{
		$condition = new EqualityCondition('id', $reporting_block->get_id());
		
		$props = array();
		foreach ($reporting_block->get_properties() as $key => $value)
		{
			if($key == 'id') continue;
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('reporting_block'), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		return true;
	}
	
	/**
	 * Retrieves the reporting block with the given name
	 * @param String $name
	 * @return ReportingBlock $reporting_block
	 */
	function retrieve_block_by_name($blockname)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('reporting_block') . ' AS ' . 
				 self :: ALIAS_REPORTINGBLOCK_TABLE;
		
		$conditions = array();
		$conditions[] = new EqualityCondition('name', $blockname);
		
		$condition = new AndCondition($conditions);
		
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$statement = $this->connection->prepare($query);
		$result = $statement->execute($params);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
	
		$reporting_block = $this->record_to_classobject($record, 'ReportingBlock');
		
		return $reporting_block;
	}
	
	/**
	 * Retrieves all reporting blocks 
	 * @return array of reporting blocks
	 */
	function retrieve_reportingblocks($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('reporting_block') . ' AS ' . 
				 self :: ALIAS_REPORTINGBLOCK_TABLE;
		
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$orderBy[] = ReportingBlock :: PROPERTY_NAME;
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
		
		return new DatabaseReportingBlockResultSet($this, $res);
		
	}
	
	/**
	 * Count reporting blocks for a given condition
	 * @param Condition $condition
	 * @return Int reporting block count
	 */
	function count_reportingblocks($condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(ReportingBlock :: PROPERTY_ID).') FROM '.$this->escape_table_name('reporting_block');
		
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
	
	/**
	 * Retrieves a reporting block by given id
	 * @param int $reporting_block_id
	 * @return ReportingBlock $reporting_block
	 */
	function retrieve_reportingblock($reporting_block_id)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('reporting_block') . ' AS ' . 
				 self :: ALIAS_REPORTINGBLOCK_TABLE;
		
		$condition = new EqualityCondition('id', $reporting_block_id);
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$statement = $this->connection->prepare($query);
		$result = $statement->execute($params);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		$reporting_block = $this->record_to_classobject($record, 'ReportingBlock');
		
		return $reporting_block;
	}
	
	/**
	 * Creates a archive controller item in the database
	 * @param ArchiveControllerItem
	 * @return true if creation is valid
	 */
	function create_archive_controller_item($archive_controller_item)
	{
		$props = array();
		foreach ($archive_controller_item->get_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('archive_controller'), $props, MDB2_AUTOQUERY_INSERT);
		
		return true;
	}
	
	/**
	 * Convert unix time to DB date
	 * @param int $date unix time
	 */
	function to_db_date($date)
	{
		if (isset ($date))
		{
			return date('Y-m-d H:i:s', $date);
		}
		return null;
	}

}
?>