<?php
/**
 * @package tracking.lib.datamanager
 */
require_once dirname(__FILE__).'/../tracking_data_manager.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once dirname(__FILE__).'/database/databaseeventresultset.class.php';

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
	const ALIAS_EVENTS_TABLE = 'ev';
	const ALIAS_TRACKER_REGISTRATION_TABLE = 'tr';
	const ALIAS_TRACKER_TABLE = 'trk';
	const ALIAS_TRACKER_SETTINGS_TABLE = 'trs';
	const ALIAS_TRACKER_EVENT_TABLE = 'tre';

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
	 * Retrieves the next id from the given table
	 * @param string $tablename the tablename
	 */
	function get_next_id($tablename)
	{
		$id = $this->connection->nextID($this->get_table_name($tablename));
		return $id;
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
	 * Creates a tracker registration in the database
	 * @param TrackerRegistration $trackerregistration
	 */
	function create_tracker_registration($trackerregistration)
	{
		$props = array();
		foreach ($trackerregistration->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('registration'), $props, MDB2_AUTOQUERY_INSERT);
		
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
	 * Creates a Tracker Setting in the database
	 * @param TrackerSetting $trackersetting
	 */
	function create_tracker_setting($trackersetting)
	{
		$props = array();
		foreach ($trackersetting->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('setting'), $props, MDB2_AUTOQUERY_INSERT);
		
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
		$conditions[] = new EqualityCondition(EventRelTracker :: PROPERTY_EVENT_ID, $eventtrackerrelation->get_event_id());
		$conditions[] = new EqualityCondition(EventRelTracker :: PROPERTY_TRACKER_ID, $eventtrackerrelation->get_tracker_id());
		$condition = new AndCondition($conditions);
		
		$props = array();
		foreach ($eventtrackerrelation->get_default_properties() as $key => $value)
		{
			if($key == EventRelTracker :: PROPERTY_EVENT_ID || $key == EventRelTracker :: PROPERTY_TRACKER_ID) continue;
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('event_rel_tracker'), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		
		return true;
	}
	
	/**
	 * Updates an tracker setting
	 * @param TrackerSetting $trackersetting
	 */
	function update_tracker_setting($trackersetting)
	{
		$condition = new EqualityCondition('id', $trackersetting->get_id());
		
		$props = array();
		foreach ($trackersetting->get_default_properties() as $key => $value)
		{
			if($key == 'id') continue;
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('tracker_setting'), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		
		return true;
	}
	
	/**
	 * Retrieves the event with the given name
	 * @param String $name
	 * @return Event event
	 */
	function retrieve_event_by_name($eventname, $block = null)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('event') . ' AS ' . 
				 self :: ALIAS_EVENTS_TABLE;
		
		$conditions = array();
		$conditions[] = new EqualityCondition('name', $eventname);
		
		if($block)
		{
			$conditions[] = new EqualityCondition('block', $block);
		}
		
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
	
		$event = $this->record_to_classobject($record, 'Event');
		
		return $event;
	}
	
	/**
	 * Retrieve all trackers from an event
	 * @param int $event_id
	 * @param Bool $active true if only the active ones should be shown (default true)
	 * @return array of Tracker Registrations
	 */
	function retrieve_trackers_from_event($event_id, $active = true)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('event_rel_tracker') . ' AS ' . 
				 self :: ALIAS_TRACKER_EVENT_TABLE;
		
		$conditions = array();
		$conditions[] = new EqualityCondition('event_id', $event_id);
		if($active)
			$conditions[] = new EqualityCondition('active', 1);
		
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
		
		$relations = array();
		
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$relations[] = $this->record_to_classobject($record, 'EventRelTracker');
		}
		
		$trackers = array();
		
		foreach($relations as $relation)
		{
			$trackers[] = $this->retrieve_tracker_registration($relation->get_tracker_id(), $relation->get_active());
		}
		
		return $trackers;
	}
	
	/**
	 * Retrieves an event tracker relation by given id's
	 * @param int $event_id the event id
	 * @param int $tracker_id the tracker id
	 * @return EventTrackerRelation that belongs to the given id's
	 */
	function retrieve_event_tracker_relation($event_id, $tracker_id)
	{ 
		$query = 'SELECT * FROM ' . $this->escape_table_name('event_rel_tracker') . ' AS ' . 
				 self :: ALIAS_TRACKER_REGISTRATION_TABLE;
		
		$conditions = array();
		$conditions[] = new EqualityCondition('tracker_id', $tracker_id);
		$conditions[] = new EqualityCondition('event_id', $event_id);
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
		
		$eventreltracker = $this->record_to_classobject($record, 'EventRelTracker');
		
		return $eventreltracker;
	}
	
	/**
	 * Retrieves a tracker registration by the given id
	 * @param int $tracker_id the tracker id
	 * @return Tracker Registration
	 */
	function retrieve_tracker_registration($tracker_id, $active)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('registration') . ' AS ' . 
				 self :: ALIAS_TRACKER_REGISTRATION_TABLE;
		
		$condition = new EqualityCondition('id', $tracker_id);
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
		
		$tracker = $this->record_to_classobject($record, 'TrackerRegistration');
		$tracker->set_active($active);
		
		return $tracker;
	}
	
	/**
	 * Retrieves all events 
	 * @return array of events
	 */
	function retrieve_events($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('event') . ' AS ' . 
				 self :: ALIAS_EVENTS_TABLE;
		
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$orderBy[] = Event :: PROPERTY_BLOCK;
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
		
		return new DatabaseEventResultSet($this, $res);
		
	}
	
	/**
	 * Count events for a given condition
	 * @param Condition $condition
	 * @return Int event count
	 */
	function count_events($condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(Event :: PROPERTY_ID).') FROM '.$this->escape_table_name('event');
		
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
	 * Retrieves an event by given id
	 * @param int $event_id
	 * @return Event $event
	 */
	function retrieve_event($event_id)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('event') . ' AS ' . 
				 self :: ALIAS_EVENTS_TABLE;
		
		$condition = new EqualityCondition('id', $event_id);
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
		
		$event = $this->record_to_classobject($record, 'Event');
		
		return $event;
	}
	
	/** Creates a tracker item in the database
	 * @param string $tablename the table name where the database has to be written to
	 * @param MainTracker $tracker_item a subclass of MainTracker
	 * @return true if creation is valid
	 */
	function create_tracker_item($tablename, $tracker_item)
	{
		$props = array();
		foreach ($tracker_item->get_default_properties() as $key => $value)
		{ 
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name($tablename), $props, MDB2_AUTOQUERY_INSERT);
		
		return true;
	}
	
	/**
	 * Retrieves all tracker items from the database
	 * @param string $tablename the table name where the database has to be written to
	 * @param string $classname the tracker's class name (needed to create the class when data is retrieved)
	 * @param array $conditons a list of conditions
	 * @return MainTracker $tracker a subclass of MainTracker
	 */
	function retrieve_tracker_items($tablename, $classname, $condition)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name($tablename) . ' AS ' . 
				 self :: ALIAS_TRACKER_TABLE;

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
		
		$trackeritems = array();
		
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$trackeritems[] = $this->record_to_classobject($record, $classname);
		}
		
		return $trackeritems;
	}
	
	/**
	 * Retrieves a tracker item from the database
	 * @param string $tablename the table name where the database has to be written to
	 * @param int $id the id of the tracker item
	 * @param string $classname the tracker's class name (needed to create the class when data is retrieved)
	 * @return MainTracker $tracker a subclass of MainTracker
	 */
	function retrieve_tracker_item($tablename, $classname, $id)
	{
		$condition = new EqualityCondition('id', $id);
		return $this->retrieve_tracker_items($tablename, $classname, $condition);
	}
	
	/**
	 * Updates a tracker item in the database
	 * @param string $tablename the table name where the database has to be written to
	 * @param MainTracker $tracker_item a subclass of MainTracker
	 * @return true if update is valid
	 */
	function update_tracker_item($tablename, $tracker_item)
	{
		$condition = new EqualityCondition('id', $tracker_item->get_id());
		
		$props = array();
		foreach ($tracker_item->get_default_properties() as $key => $value)
		{
			if($key == 'id') continue;
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name($tablename), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		
		return true;
	}
	
	/**
	 * Deletes tracker items in the database
	 * @param Condition conditon which items should be removed
	 * @return true if tracker items are removed
	 */
	function remove_tracker_items($tablename, $condition)
	{
		$query = 'DELETE FROM '.$this->escape_table_name($tablename);
		
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

		return true;
	}
	
	/**
	 * Creates a archive controller item in the database
	 * @param ArchiveControllerItem
	 * @return true if creation is valid
	 */
	function create_archive_controller_item($archive_controller_item)
	{
		$props = array();
		foreach ($archive_controller_item->get_default_properties() as $key => $value)
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