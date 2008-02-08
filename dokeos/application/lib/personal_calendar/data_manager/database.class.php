<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personalcalendardatamanager.class.php';
require_once 'MDB2.php';
/**
 * This is an implementation of a personal calendar datamanager using the PEAR::
 * MDB2 package as a database abstraction layer.
 */
class DatabasePersonalCalendarDatamanager extends PersonalCalendarDatamanager
{
	/**
	 * A prefix
	 */
	private $prefix;
	/**
	 * An instance of a RepositoryDatamanager
	 */
	private $repoDM;
	/**
	 * Initializes this datamanager
	 */
	function initialize()
	{
		$this->repoDM = RepositoryDataManager :: get_instance();
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string'),array('debug'=>3,'debug_handler'=>array('DatabasePersonalCalendarDatamanager','debug')));
		$this->prefix = 'personal_calendar_';
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
	 * @see PersonalCalendarDatamanager
	 */
	function get_next_personal_calendar_event_id()
	{
		return $this->connection->nextID($this->get_table_name('personal_calendar_event'));
	}
	/**
	 * @see PersonalCalendarDatamanager
	 */
	function create_personal_calendar_event($personal_event)
	{
		$props = array ();
		$props[$this->escape_column_name('id')] = $personal_event->get_id();
		$props[$this->escape_column_name('learning_object')] = $personal_event->get_event()->get_id();
		$props[$this->escape_column_name('publisher')] = $personal_event->get_user_id();
		$props[$this->escape_column_name('publication_date')] = $personal_event->get_publication_date();
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('personal_calendar'), $props, MDB2_AUTOQUERY_INSERT);
		return true;
	}
	/**
	 * @see PersonalCalendarDatamanager
	 */
	function delete_personal_calendar_event($personal_event)
	{
		$query = 'DELETE FROM '.$this->get_table_name('personal_calendar').' WHERE id = ?';
		$statement = $this->connection->prepare($query);
		$statement->execute($personal_event->get_id());
	}
	/**
	 * @see PersonalCalendarDatamanager
	 */
	function retrieve_personal_calendar_events($user_id)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('publisher').'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($user_id);
		$events = array();
		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$event = $this->repoDM->retrieve_learning_object($record['learning_object'],'calendar_event');
			$events[] = new PersonalCalendarEvent($record['id'],$record['publisher'],$event,$record['publication_date']);
		}
		return $events;
	}
	/**
	 * @see PersonalCalendarDatamanager
	 */
	function load_personal_calendar_event($id)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('id').'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$event = $this->repoDM->retrieve_learning_object($record['learning_object'],'calendar_event');
		return new PersonalCalendarEvent($record['id'],$record['publisher'],$event,$record['publication_date']);
	}
	/**
	 * Gets the full name of a given table (by adding the database name and a
	 * prefix if required)
	 * @param string $name
	 */
	private function get_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		return $dsn['database'].'.'.$this->prefix.$name;
	}
	/**
	 * Escapes a column name
	 * @param string $name
	 */
	private function escape_column_name($name)
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
	/**
	 * @see PersonalCalendarDatamanager
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
	 * @see Application::learning_object_is_published()
	 */
	public function learning_object_is_published($object_id)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('id').'=?';
		$res = $this->limitQuery($query, 1,null, array ($object_id));
		return $res->numRows() == 1;
	}
	/**
	 * @see Application::any_learning_object_is_published()
	 */
	public function any_learning_object_is_published($object_ids)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('learning_object').' IN (?'.str_repeat(',?', count($object_ids) - 1).')';
		$res = $this->limitQuery($query, 1, null,$object_ids);
		return $res->numRows() == 1;
	}
	/**
	 * @see Application::get_learning_object_publication_attributes()
	 */
	public function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('publisher').'=?';

				$order = array ();
				for ($i = 0; $i < count($order_property); $i ++)
				{
					if ($order_property[$i] == 'application')
					{
					}
					elseif($order_property[$i] == 'location')
					{
					}
					elseif($order_property[$i] == 'title')
					{
					}
					else
					{
					}
				}
				if (count($order))
				{
					$query .= ' ORDER BY '.implode(', ', $order);
				}

				$statement = $this->connection->prepare($query);
				$res = $statement->execute(api_get_user_id());
			}
		}
		else
		{
			$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('learning_object').'=?';
			$statement = $this->connection->prepare($query);
			$res = $statement->execute($object_id);
		}
		$publication_attr = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$info = new LearningObjectPublicationAttributes();
			$info->set_id($record['id']);
			$info->set_publisher_user_id($record['publisher']);
			$info->set_publication_date($record['publication_date']);
			$info->set_application('personal_calendar');
			//TODO: i8n location string
			$info->set_location('');
			//TODO: set correct URL
			$info->set_url('index_personal_calendar.php?pid='. $record['id']);
			$info->set_publication_object_id($record['learning_object']);
			$publication_attr[] = $info;
		}
		return $publication_attr;
	}
	/**
	 * @see Application::get_learning_object_publication_attribute()
	 */
	public function get_learning_object_publication_attribute($publication_id)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('id').'=?';
		$statement = $this->connection->prepare($query);
		$this->connection->setLimit(0,1);
		$res = $statement->execute($publication_id);

		$publication_attr = array();
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		$info = new LearningObjectPublicationAttributes();
		$info->set_id($record['id']);
		$info->set_publisher_user_id($record['publisher']);
		$info->set_publication_date($record['publication_date']);
		$info->set_application('personal_calendar');
		//TODO: i8n location string
		$info->set_location('');
		//TODO: set correct URL
		$info->set_url('index_personal_calendar.php?pid='. $record['id']);
		$info->set_publication_object_id($record['learning_object']);
		return $info;
	}
	/**
	 * @see Application::count_publication_attributes()
	 */
	public function count_publication_attributes($type = null, $condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name('id').') FROM '.$this->get_table_name('personal_calendar').' WHERE '.$this->escape_column_name('publisher').'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(api_get_user_id());
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}
	/**
	 * @see Application::delete_learning_object_publications()
	 */
	public function delete_learning_object_publications($object_id)
	{
		$query = 'DELETE FROM '.$this->get_table_name('personal_calendar').' WHERE learning_object = ?';
		$statement = $this->connection->prepare($query);
		$statement->execute($object_id);
	}
	/**
	 * @see Application::update_learning_object_publication_id()
	 */
	function update_learning_object_publication_id($publication_attr)
	{
		$where = $this->escape_column_name('id').'='.$publication_attr->get_id();
		$props = array();
		$props[$this->escape_column_name('learning_object')] = $publication_attr->get_publication_object_id();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('personal_calendar'), $props, MDB2_AUTOQUERY_UPDATE, $where))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	/**
	 * Executes a query
	 * @param string $query The query (which will be used in a prepare-
	 * statement)
	 * @param int $limit The number of rows
	 * @param int $offset The offset
	 * @param array $params The parameters to replace the placeholders in the
	 * query
	 * @param boolean $is_manip Is the query a manipulation query
	 */
	private function limitQuery($query,$limit,$offset,$params,$is_manip = false)
	{
		$this->connection->setLimit($limit,$offset);
		$statement = $this->connection->prepare($query,null,($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		return $res;
	}
}
?>