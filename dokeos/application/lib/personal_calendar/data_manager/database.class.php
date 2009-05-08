<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personal_calendar_data_manager.class.php';
require_once dirname(__FILE__).'/../calendar_event_publication.class.php';
require_once dirname(__FILE__).'/database/database_calendar_event_publication_result_set.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';
/**
 * This is an implementation of a personal calendar datamanager using the PEAR::
 * MDB2 package as a database abstraction layer.
 */
class DatabasePersonalCalendarDatamanager extends PersonalCalendarDatamanager
{
	private $db;
	
	function initialize()
	{
		$this->db = new Database(array());
		$this->db->set_prefix('personal_calendar_');
	}
	
	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->db->create_storage_unit($name,$properties,$indexes);
	}
	
	public function learning_object_is_published($object_id)
	{
		$condition = new EqualityCondition('id',$object_id);
		return $this->db->count_objects('publication',$condition) == 1;
	}
	
	public function any_learning_object_is_published($object_ids)
	{
		$condition = new InCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT,$object_ids);
		return $this->db->count_objects('publication',$condition)>=1;
	}
	
	public function is_date_column($var)
	{
		return $this->db->is_date_column($var);
	}
	public function escape_column_name($name)
	{
		return $this->db->escape_column_name($name);
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
				$query = 'SELECT * FROM '.$this->db->get_table_name('publication').' WHERE '.$this->db->escape_column_name('publisher').'=?';

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

				$statement = $this->db->get_connection()->prepare($query);
				$res = $statement->execute(Session :: get_user_id());
			}
		}
		else
		{
			$query = 'SELECT * FROM '.$this->db->get_table_name('publication').' WHERE '.$this->db->escape_column_name('calendar_event').'=?';
			$statement = $this->db->get_connection()->prepare($query);
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
		$condition = new EqualityCondition('id',$publication_id);
		$record = $this->db->next_result();

		$info = new LearningObjectPublicationAttributes();
		$info->set_id($record->get_id());
		$info->set_publisher_user_id($record->get_publisher());
		$info->set_publication_date($record->get_publication_date());
		$info->set_application('personal_calendar');
		//TODO: i8n location string
		$info->set_location('');
		//TODO: set correct URL
		$info->set_url('index_personal_calendar.php?pid='. $record->get_id());
		$info->set_publication_object_id($record->get_learning_object());
		return $info;
	}
	/**
	 * @see Application::count_publication_attributes()
	 */
	public function count_publication_attributes($type = null, $condition = null)
	{
		$condition = new EqualityCondition('publisher', Session :: get_user_id());
		return $this->db->count_objects('publication', $condition);
	}
	/**
	 * @see Application::delete_learning_object_publications()
	 */
	public function delete_learning_object_publications($object_id)
	{
		$condition = new EqualityCondition('calendar_event',$object_id);
		$this->db->delete('publication',$condition);
	}
	/**
	 * @see Application::update_learning_object_publication_id()
	 */
	function update_learning_object_publication_id($publication_attr)
	{
		//$condition = new EqualityCondition('id',$publiction->get_id());
		
		$where = $this->db->escape_column_name('id').'='.$publication_attr->get_id();
		$props = array();
		$props[$this->db->escape_column_name('learning_object')] = $publication_attr->get_publication_object_id();
		$this->db->get_connection()->loadModule('Extended');
		return $this->db->get_connection()->extended->autoExecute($this->db->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
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
		$this->db->get_connection()->setLimit($limit,$offset);
		$statement = $this->db->get_connection()->prepare($query,null,($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		return $res;
	}
	
	function get_next_calendar_event_publication_id()
	{
		return $this->db->get_next_id('publication');
	}
	
    //Inherited
    function retrieve_calendar_event_publication($id)
	{

		$query = 'SELECT * FROM '.$this->db->escape_table_name('publication');
		$query .= ' WHERE '.$this->db->escape_column_name(CalendarEventPublication :: PROPERTY_ID).'=?';

		$this->db->get_connection()->setLimit(1);
		$statement = $this->db->get_connection()->prepare($query);
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

		$query = 'SELECT * FROM '.$this->db->escape_table_name('publication');

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
			$order[] = $this->db->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
		}
		if (count($order))
		{
			$query .= ' ORDER BY '.implode(', ', $order);
		}
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}

		$this->db->get_connection()->setLimit(intval($maxObjects),intval($offset));
		$statement = $this->db->get_connection()->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseCalendarEventPublicationResultSet($this, $res);
	}
	
	//Inherited.
	function update_calendar_event_publication($calendar_event_publication)
	{
		$where = $this->db->escape_column_name(CalendarEventPublication :: PROPERTY_ID).'='.$calendar_event_publication->get_id();
		$props = array();
		foreach ($calendar_event_publication->get_default_properties() as $key => $value)
		{
			$props[$this->db->escape_column_name($key)] = $value;
		}
		$this->db->get_connection()->loadModule('Extended');
		$this->db->get_connection()->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	//Inherited
	function delete_calendar_event_publication($calendar_event_publication)
	{
		$condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_ID,$calendar_event_publication->get_id());
		return $this->db->delete('publication',$condition);
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
		$where = $this->db->escape_column_name(CalendarEventPublication :: PROPERTY_ID).'='.$publication_attr->get_id();
		$props = array();
		$props[$this->db->escape_column_name(CalendarEventPublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
		$this->db->get_connection()->loadModule('Extended');
		if ($this->db->get_connection()->extended->autoExecute($this->db->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
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
			$props[$this->db->escape_column_name($key)] = $value;
		}
		$props[$this->db->escape_column_name(CalendarEventPublication :: PROPERTY_ID)] = $publication->get_id();

		$this->db->get_connection()->loadModule('Extended');
		if ($this->db->get_connection()->extended->autoExecute($this->db->get_table_name('publication'), $props, MDB2_AUTOQUERY_INSERT))
		{
			$users = $publication->get_target_users();
			foreach($users as $index => $user_id)
			{
				$props = array();
				$props[$this->escape_column_name('publication')] = $publication->get_id();
				$props[$this->escape_column_name('user')] = $user_id;
				$this->db->get_connection()->extended->autoExecute($this->db->get_table_name('publication_user'), $props, MDB2_AUTOQUERY_INSERT);
			}
			$groups = $publication->get_target_groups();
			foreach($groups as $index => $group_id)
			{
				$props = array();
				$props[$this->escape_column_name('publication')] = $publication->get_id();
				$props[$this->escape_column_name('group_id')] = $group_id;
				$this->db->get_connection()->extended->autoExecute($this->db->get_table_name('publication_group'), $props, MDB2_AUTOQUERY_INSERT);
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function retrieve_calendar_event_publication_target_groups($calendar_event_publication)
	{
		return array();
	}
	
	function retrieve_calendar_event_publication_target_users($calendar_event_publication)
	{
		return array();
	}
}
?>