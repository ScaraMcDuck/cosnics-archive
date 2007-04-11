<?php
require_once dirname(__FILE__).'/../personalcalendardatamanager.class.php';
require_once 'MDB2.php';
class DatabasePersonalCalendarDatamanager extends PersonalCalendarDatamanager
{
	private $prefix;
	private $repoDM;
	function initialize()
	{
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string_personal_calendar'),array('debug'=>3,'debug_handler'=>array('PersonalCalendarDatamanager','debug')));
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
		$this->connection->query('SET NAMES utf8');
	}
	function get_next_personal_calendar_event_id()
	{
		return $this->connection->nextID($this->get_table_name('personal_calendar_event'));
	}
	function create_personal_calendar_event($personal_event)
	{
		$props = array ();
		$props[$this->escape_column_name('id')] = $personal_event->get_id();
		$props[$this->escape_column_name('learning_object')] = $personal_event->get_event()->get_id();
		$props[$this->escape_column_name('publisher')] = $personal_event->get_user_id();
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('personal_calendar'), $props, MDB2_AUTOQUERY_INSERT);
		return true;
	}
	function retrieve_personal_calendar_events($user_id)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('publisher').'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($user_id);
		$events = array();
		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$event = $this->repoDM->retrieve_learning_object($record['learning_object'],'calendar_event');
			$events[] = new PersonalCalendarEvent($record['id'],$record['publisher'],$event);
		}
		return $events;
	}
	function load_personal_calendar_event($id)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('id').'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$event = $this->repoDM->retrieve_learning_object($record['learning_object'],'calendar_event');
		return new PersonalCalendarEvent($record['id'],$record['publisher'],$event);
	}
	private function get_table_name($name)
	{
		global $personal_calendar_database;
		return $personal_calendar_database.'.'.$this->prefix.$name;
	}
	private function escape_column_name($name, $prefix_learning_object_properties = false)
	{
		list($table, $column) = explode('.', $name, 2);
		$prefix = '';

		if (isset($column))
		{
			$prefix = $table.'.';
			$name = $column;
		}
		return $prefix.$this->connection->quoteIdentifier($name);
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
		$manager->createTable($name,$properties,$options);
		foreach($indexes as $index_name => $index_info)
		{
			if($index_info['type'] == 'primary')
			{
				$index_info['primary'] = 1;
				$manager->createConstraint($name,$index_name,$index_info);
			}
			else if($index_info['type'] == 'unique')
			{
				$index_info['unique'] = 1;
				$manager->createConstraint($name,$index_name,$index_info);
			}
			else
			{
				$manager->createIndex($name,$index_name,$index_info);
			}
		}
	}
}
?>