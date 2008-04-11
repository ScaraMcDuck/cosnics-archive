<?php
/**
 * @package tracking.lib.datamanager
 */
require_once dirname(__FILE__).'/../trackingdatamanager.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_library_path().'condition/conditiontranslator.class.php';

require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *  @author Sven Vanpoucke
==============================================================================
 */

class DatabaseTrackingDataManager extends TrackingDataManager
{
	const ALIAS_CONTENTBOX_TABLE = 'tracking';

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
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string'),array('debug'=>3,'debug_handler'=>array('DatabaseTrackingDataManager','debug')));
		if (PEAR::isError($this)) {
   		 die($this->connection->getMessage());
		}
		$this->prefix = 'tracker_';
		$this->connection->query('SET NAMES utf8');
	}

	/**
	 * This function can be used to handle some debug info from MDB2
	 */
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
	 * Escapes a column name in accordance with the database type.
	 * @param string $name The column name.
	 * @param boolean $prefix_contentbox_properties Whether or not to
	 *                                                   prefix contentbox properties
	 *                                                   to avoid collisions.
	 * @return string The escaped column name.
	 */
	function escape_column_name($name, $prefix_user_object_properties = false)
	{
		list($table, $column) = explode('.', $name, 2);
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
		    throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
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
	 * Creates an event in the database
	 * @param Event $event
	 */
	function create_event($event)
	{
		$props = array();
		foreach ($event->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('event'), $props, MDB2_AUTOQUERY_INSERT);
		
		return true;
	}
	
	/**
	 * Registers a tracker in the database
	 * @param Tracker $tracker
	 */
	function register_tracker($tracker)
	{
		$props = array();
		foreach ($tracker->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('tracker'), $props, MDB2_AUTOQUERY_INSERT);
		
		return true;
	}
	
	/**
	 * Registers a tracker to an event
	 * @param EventTrackerRelation $eventtrackerrelation
	 */
	function create_event_tracker_relation($eventtrackerrelation)
	{
		$props = array();
		foreach ($eventtrackerrelation->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('event_rel_tracker'), $props, MDB2_AUTOQUERY_INSERT);
		
		return true;
	}
	
	/**
	 * Updates an event (needed for change of activity)
	 * @param Event $event
	 */
	function update_event($event)
	{
		$condition = new EqualityCondition('id', $event->get_id());
		
		$props = array();
		foreach ($event->get_default_properties() as $key => $value)
		{
			if($key == 'id') continue;
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('event'), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		
		return true;
	}
	
	/**
	 * Updates an event tracker relation (needed for change of activity)
	 * @param EventTrackerRelation $eventtrackerrelation
	 */
	function update_event_tracker_relation($eventtrackerrelation)
	{
		$conditions[] = new EqualityCondition('eventid', $eventtrackerrelation->get_event_id());
		$conditions[] = new EqualityCondition('trackerid', $eventtrackerrelation->get_tracker_id());
		$condition = new AndCondition($conditions);
		
		$props = array();
		foreach ($event->get_default_properties() as $key => $value)
		{
			if($key == 'eventid' || $key == 'trackerid') continue;
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('event_rel_tracker'), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		
		return true;
	}
	
	/**
	 * Retrieves the event with the given name
	 * @param String $name
	 */
	function retrieve_event_by_name($eventname)
	{
		
	}
	
	/**
	 * Retrieve all trackers from an event
	 * @param Event $event
	 * @param Bool $active true if only the active ones should be shown (default true)
	 */
	function retrieve_trackers_from_event($event, $active = true)
	{
		
	}
	
	/**
	 * Retrieves all events 
	 */
	function retrieve_events()
	{
		
	}

}
?>