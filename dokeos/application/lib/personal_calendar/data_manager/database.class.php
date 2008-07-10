<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personal_calendar_data_manager.class.php';
require_once dirname(__FILE__).'/../calendar_event_publication.class.php';
require_once dirname(__FILE__).'/database/database_calendar_event_publication_result_set.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
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
	 * Initializes this datamanager
	 */
	function initialize()
	{
		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));
		
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
	function escape_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		$database_name = $this->connection->quoteIdentifier($dsn['database']);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
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
	function escape_column_name($name)
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
		$query = 'SELECT * FROM '.$this->get_table_name('publication').' WHERE '.$this->escape_column_name('id').'=?';
		$res = $this->limitQuery($query, 1,null, array ($object_id));
		return $res->numRows() == 1;
	}
	/**
	 * @see Application::any_learning_object_is_published()
	 */
	public function any_learning_object_is_published($object_ids)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('publication').' WHERE '.$this->escape_column_name(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT).' IN (?'.str_repeat(',?', count($object_ids) - 1).')';
		$res = $this->limitQuery($query, 1, null,$object_ids);
		return $res->numRows() == 1;
	}
	
	//Inherited.
	static function is_date_column($name)
	{
		return ($name == CalendarEventPublication :: PROPERTY_PUBLISHED);
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
				$query = 'SELECT * FROM '.$this->get_table_name('publication').' WHERE '.$this->escape_column_name('publisher').'=?';

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
				$res = $statement->execute(Session :: get_user_id());
			}
		}
		else
		{
			$query = 'SELECT * FROM '.$this->get_table_name('publication').' WHERE '.$this->escape_column_name('calendar_event').'=?';
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
			$info->set_publication_object_id($record['calendar_event']);
			$publication_attr[] = $info;
		}
		return $publication_attr;
	}
	/**
	 * @see Application::get_learning_object_publication_attribute()
	 */
	public function get_learning_object_publication_attribute($publication_id)
	{
		$query = 'SELECT * FROM '.$this->get_table_name('publication').' WHERE '.$this->escape_column_name('id').'=?';
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
		$query = 'SELECT COUNT('.$this->escape_column_name('id').') FROM '.$this->get_table_name('publication').' WHERE '.$this->escape_column_name('publisher').'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(Session :: get_user_id());
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}
	/**
	 * @see Application::delete_learning_object_publications()
	 */
	public function delete_learning_object_publications($object_id)
	{
		$query = 'DELETE FROM '.$this->get_table_name('publication').' WHERE calendar_event = ?';
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
		if ($this->connection->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
	
	function get_next_calendar_event_publication_id()
	{
		return $this->connection->nextID($this->get_table_name('publication'));
	}
	
    //Inherited
    function retrieve_calendar_event_publication($id)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('publication');
		$query .= ' WHERE '.$this->escape_column_name(CalendarEventPublication :: PROPERTY_ID).'=?';

		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_calendar_event_publication($record);
	}
	
	//Inherited.
	function record_to_calendar_event_publication($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (CalendarEventPublication :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new CalendarEventPublication($record[CalendarEventPublication :: PROPERTY_ID], $defaultProp);
	}

    //Inherited.
    function retrieve_calendar_event_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('publication');

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
		return new DatabaseCalendarEventPublicationResultSet($this, $res);
	}
	
	//Inherited.
	function update_calendar_event_publication($calendar_event_publication)
	{
		$where = $this->escape_column_name(CalendarEventPublication :: PROPERTY_ID).'='.$calendar_event_publication->get_id();
		$props = array();
		foreach ($calendar_event_publication->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	//Inherited
	function delete_calendar_event_publication($calendar_event_publication)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(CalendarEventPublication :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($calendar_event_publication->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//Inherited.
	function delete_calendar_event_publications($object_id)
	{
		$condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_PROFILE, $object_id);
		$publications = $this->retrieve_calendar_event_publications($condition, null, null, null, null, true, array (), array (), 0, -1, $object_id);
		while ($publication = $publications->next_result())
		{
//			$subject = '['.PlatformSetting :: get('site_name').'] '.$publication->get_learning_object()->get_title();
//			// TODO: SCARA - Add meaningfull publication removal message
//			$body = 'message';
//			$user = $this->userDM->retrieve_user($publication->get_publisher_id());
//			$mail = Mail :: factory($subject, $body, $user->get_email());
//			$mail->send();
			$this->delete_calendar_event_publication($publication);
		}
		return true;
	}


	//Inherited.
	function update_calendar_event_publication_id($publication_attr)
	{
		$where = $this->escape_column_name(CalendarEventPublication :: PROPERTY_ID).'='.$publication_attr->get_id();
		$props = array();
		$props[$this->escape_column_name(CalendarEventPublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_calendar_event_publication($publication)
	{
		$props = array();
		foreach ($publication->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(CalendarEventPublication :: PROPERTY_ID)] = $publication->get_id();

		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>