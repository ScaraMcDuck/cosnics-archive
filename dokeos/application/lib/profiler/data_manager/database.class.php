<?php
/**
 * @package application.lib.profiler.data_manager.database
 */
require_once dirname(__FILE__).'/../profiler_data_manager.class.php';
require_once dirname(__FILE__).'/../profile_publication.class.php';
require_once dirname(__FILE__).'/database/database_profile_publication_result_set.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/data_manager/database.class.php';
require_once 'MDB2.php';

class DatabaseProfilerDataManager extends ProfilerDataManager {

	private $prefix;
	private $userDM;
	private $db;

	const ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE = 'pmb';
	const ALIAS_LEARNING_OBJECT_TABLE = 'lo';

	function initialize()
	{
		PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array (get_class(), 'handle_error'));
		$this->userDM = UserDataManager :: get_instance();
		
		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));
		
		if (PEAR::isError($this)) {
   		 die($this->connection->getMessage());
		}
		$this->prefix = 'profiler_';
		$this->connection->query('SET NAMES utf8');
		
		$this->db = new Database(array('profiler_category' => 'cat'));
		$this->db->set_prefix('profiler_');
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
	 * Escapes a column name
	 * @param string $name
	 */
	public function escape_column_name($name)
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

	static function handle_error($error)
	{
		die(__FILE__.':'.__LINE__.': '.$error->getMessage()
		// For debugging only. May create a security hazard.
		.' ('.$error->getDebugInfo().')');
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

	//Inherited.
	function get_next_profile_publication_id()
	{
		return $this->connection->nextID($this->get_table_name('publication'));
	}

	//Inherited.
    function count_profile_publications($condition = null)
    {
		$query = 'SELECT COUNT('.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('publication');
		$query .= 'JOIN '.$this->userDM->get_database()->escape_table_name('user') . 'ON' . $this->escape_table_name('publication'). '.' . $this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->get_database()->escape_table_name('user') .'.'. $this->userDM->get_database()->escape_column_name('user_id');

		$params = array ();
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
		$res->free();
		return $record[0];
    }

    //Inherited
    function retrieve_profile_publication($id)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('publication');
		$query .= ' JOIN '.$this->userDM->get_database()->escape_table_name('user') . 'ON' . $this->escape_table_name('publication'). '.' . $this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->get_database()->escape_table_name('user') .'.'. $this->userDM->get_database()->escape_column_name('user_id');
		$query .= ' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).'=?';

		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_profile_publication($record);
	}

    //Inherited.
    function retrieve_profile_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('publication');
		$query .= 'JOIN '.$this->userDM->get_database()->escape_table_name('user') . 'ON' . $this->escape_table_name('publication'). '.' . $this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->get_database()->escape_table_name('user') .'.'. $this->userDM->get_database()->escape_column_name('user_id');

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
		return new DatabaseProfilePublicationResultSet($this, $res);
	}

	//Inherited.
	function record_to_profile_publication($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (ProfilePublication :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new ProfilePublication($record[ProfilePublication :: PROPERTY_ID], $defaultProp);
	}

	//Inherited.
	function update_profile_publication($profile_publication)
	{
		$where = $this->escape_column_name(ProfilePublication :: PROPERTY_ID).'='.$profile_publication->get_id();
		$props = array();
		foreach ($profile_publication->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	//Inherited
	function delete_profile_publication($profile_publication)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($profile_publication->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//Inherited.
	function delete_profile_publications($object_id)
	{
		$condition = new EqualityCondition(ProfilePublication :: PROPERTY_PROFILE, $object_id);
		$publications = $this->retrieve_profile_publications($condition, null, null, null, null, true, array (), array (), 0, -1, $object_id);
		while ($publication = $publications->next_result())
		{
//			$subject = '['.PlatformSetting :: get('site_name').'] '.$publication->get_learning_object()->get_title();
//			// TODO: SCARA - Add meaningfull publication removal message
//			$body = 'message';
//			$user = $this->userDM->retrieve_user($publication->get_publisher_id());
//			$mail = Mail :: factory($subject, $body, $user->get_email());
//			$mail->send();
			$this->delete_profile_publication($publication);
		}
		return true;
	}


	//Inherited.
	function update_profile_publication_id($publication_attr)
	{
		$where = $this->escape_column_name(ProfilePublication :: PROPERTY_ID).'='.$publication_attr->get_id();
		$props = array();
		$props[$this->escape_column_name(ProfilePublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
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

	//Inherited.
	static function is_date_column($name)
	{
		return ($name == ProfilePublication :: PROPERTY_PUBLISHED);
	}

	//Inherited.
	function any_learning_object_is_published($object_ids)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_PROFILE).' IN (?'.str_repeat(',?', count($object_ids) - 1).')';
		$res = $this->limitQuery($query, 1, null,$object_ids);
		return $res->numRows() == 1;
	}

	//Inherited.
	function learning_object_is_published($object_id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_PROFILE).'=?';
		$res = $this->limitQuery($query, 1,null, array ($object_id));
		return $res->numRows() == 1;
	}

	//Inherited
	private function limitQuery($query,$limit,$offset,$params,$is_manip = false)
	{
		$this->connection->setLimit($limit,$offset);
		$statement = $this->connection->prepare($query,null,($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		return $res;
	}

	//Inherited
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

	//Inherited
	function get_learning_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$query  = 'SELECT '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE.'.*, '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.'. $this->escape_column_name('title') .' FROM '.$this->escape_table_name('publication').' AS '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .' JOIN '.RepositoryDataManager :: get_instance()->escape_table_name('learning_object').' AS '. self :: ALIAS_LEARNING_OBJECT_TABLE .' ON '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .'.`profile` = '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.`id`';
				$query .= ' WHERE '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.'.$this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER).'=?';

				$order = array ();
				for ($i = 0; $i < count($order_property); $i ++)
				{
					if ($order_property[$i] == 'application' || $order_property[$i] == 'location')
					{
					}
					elseif($order_property[$i] == 'title')
					{
						$order[] = self :: ALIAS_LEARNING_OBJECT_TABLE. '.' .$this->escape_column_name('title').' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
					}
					else
					{
						$order[] = self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.' .$this->escape_column_name($order_property[$i], true).' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
						$order[] = self :: ALIAS_LEARNING_OBJECT_TABLE. '.' .$this->escape_column_name('title').' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
					}
				}
				if (count($order))
				{
					$query .= ' ORDER BY '.implode(', ', $order);
				}

				$statement = $this->connection->prepare($query);
				$param = $user->get_id();
			}
		}
		else
		{
			$query = 'SELECT * FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_PROFILE).'=?';
			$statement = $this->connection->prepare($query);
			$param = $object_id;
		}

		$res = $statement->execute($param);

		$publication_attr = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$publication = $this->record_to_profile_publication($record);

			$info = new LearningObjectPublicationAttributes();
			$info->set_id($publication->get_id());
			$info->set_publisher_user_id($publication->get_publisher());
			$info->set_publication_date($publication->get_published());
			$info->set_application('Profiler');
			//TODO: i8n location string
			$info->set_location(Translation :: get('List'));
			$info->set_url('index_profiler.php?go=view&profile='.$publication->get_id());
			$info->set_publication_object_id($publication->get_profile());

			$publication_attr[] = $info;
		}
		return $publication_attr;
	}

	//Indered.
	function get_learning_object_publication_attribute($publication_id)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		$this->connection->setLimit(0,1);
		$res = $statement->execute($publication_id);

		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		$publication = $this->record_to_profile_publication($record);

		$info = new LearningObjectPublicationAttributes();
		$info->set_id($publication->get_id());
		$info->set_publisher_user_id($publication->get_publisher());
		$info->set_publication_date($publication->get_published());
		$info->set_application('Profiler');
		//TODO: i8n location string
		$info->set_location(Translation :: get('List'));
		$info->set_url('index_profiler.php?go=view&profile='.$publication->get_id());
		$info->set_publication_object_id($publication->get_profile());

		return $info;
	}

	//Inherited.
	function count_publication_attributes($user, $type = null, $condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER).'=?';;

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($user->get_id());
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	//Inherited.
	function create_profile_publication($publication)
	{
		$props = array();
		foreach ($publication->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(ProfilePublication :: PROPERTY_ID)] = $publication->get_id();

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
	
	function get_next_category_id()
	{
		return $this->db->get_next_id('profiler_category');
	}
	
	function delete_category($category)
	{
		$condition = new EqualityCondition(ProfilerCategory :: PROPERTY_ID, $category->get_id());
		$succes = $this->db->delete('profiler_category', $condition);
		
		$query = 'UPDATE '.$this->db->escape_table_name('profiler_category').' SET '.
				 $this->db->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER).'='.
				 $this->db->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER).'-1 WHERE '.
				 $this->db->escape_column_name(ProfilerCategory :: PROPERTY_DISPLAY_ORDER).'>? AND ' .
				 $this->db->escape_column_name(ProfilerCategory :: PROPERTY_PARENT) . '=?';
		$statement = $this->db->get_connection()->prepare($query); 
		$statement->execute(array($category->get_display_order(), $category->get_parent()));
		
		return $succes;
	}
	
	function update_category($category)
	{ 
		$condition = new EqualityCondition(ProfilerCategory :: PROPERTY_ID, $category->get_id());
		return $this->db->update($category, $condition);
	}
	
	function create_category($category)
	{
		return $this->db->create($category);
	}
	
	function count_categories($conditions = null)
	{
		return $this->db->count_objects('profiler_category', $conditions);
	}
	
	function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->db->retrieve_objects('profiler_category', $condition, $offset, $count, $order_property, $order_direction);
	}
	
	function select_next_category_display_order($parent_category_id)
	{
		$query = 'SELECT MAX(' . ProfilerCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . 
		$this->db->escape_table_name('profiler_category');
	
		$condition = new EqualityCondition(ProfilerCategory :: PROPERTY_PARENT, $parent_category_id);
		//print_r($condition);
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this->db, $params, $prefix_properties = false);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->db->get_connection()->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
	
		return $record[0] + 1;
	}
}
?>