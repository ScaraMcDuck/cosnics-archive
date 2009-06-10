<?php
/**
 * @package admin
 * @subpackage datamanager
 */
require_once Path :: get_admin_path() . 'lib/data_manager/database/database_setting_result_set.class.php';
require_once Path :: get_admin_path() . 'lib/data_manager/database/database_language_result_set.class.php';
require_once Path :: get_admin_path() . 'lib/data_manager/database/database_registration_result_set.class.php';
require_once Path :: get_admin_path() . 'lib/admin_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/language.class.php';
require_once Path :: get_admin_path() . 'lib/registration.class.php';
require_once Path :: get_admin_path() . 'lib/setting.class.php';
require_once Path :: get_admin_path() . 'lib/category_manager/admin_category.class.php';
require_once Path :: get_admin_path() . 'lib/system_announcement_publication.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_library_path().'database/database.class.php';
require_once 'MDB2.php';

class DatabaseAdminDataManager extends AdminDataManager
{
	private $database;

	function initialize()
	{
		$this->database = new Database(array('admin_category' => 'cat', 'language' => 'lang', 'setting' => 'setting', 'registration' => 'reg', 'system_announcement_publication' => 'sa'));
		$this->database->set_prefix('admin_');
	}

    function retrieve_languages($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->database->retrieve_objects(Language :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

    function retrieve_settings($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->database->retrieve_objects(Setting :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

	function retrieve_registrations($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->database->retrieve_objects(Registration :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

	function retrieve_language_from_english_name($english_name)
	{
		$condition = new EqualityCondition(Language :: PROPERTY_ENGLISH_NAME, $english_name);
		return $this->database->retrieve_object(Language :: get_table_name(), $condition);
	}

	function retrieve_setting_from_variable_name($variable, $application = 'admin')
	{
		$conditions[] = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $application);
		$conditions[] = new EqualityCondition(Setting :: PROPERTY_VARIABLE, $variable);
		$condition = new AndCondition($conditions);

		return $this->database->retrieve_object(Setting :: get_table_name(), $condition);
	}

	function update_setting($setting)
	{
		$condition = new EqualityCondition(Setting :: PROPERTY_ID, $setting->get_id());
		return $this->database->update($setting, $condition);
	}

	function update_registration($registration)
	{
		$condition = new EqualityCondition(Registration :: PROPERTY_ID, $registration->get_id());
		return $this->database->update($registration, $condition);
	}

	function update_system_announcement_publication($system_announcement_publication)
	{
		// Delete existing target users and groups
		$parameters['id'] = $system_announcement_publication->get_id();
		$query = 'DELETE FROM '.$this->database->escape_table_name('system_announcement_publication_user').' WHERE system_announcement_publication = ?';
		$statement = $this->database->get_connection()->prepare($query);
		$statement->execute($parameters['id']);
		$query = 'DELETE FROM '.$this->database->escape_table_name('system_announcement_publication_group').' WHERE system_announcement_publication = ?';
		$statement = $this->database->get_connection()->prepare($query);
		$statement->execute($parameters['id']);

		// Add updated target users and course_groups
		$users = $system_announcement_publication->get_target_users();
		$this->database->get_connection()->loadModule('Extended');
		foreach($users as $user_id)
		{
			$props = array();
			$props[$this->database->escape_column_name('system_announcement_publication')] = $system_announcement_publication->get_id();
			$props[$this->database->escape_column_name('user')] = $user_id;
			$this->database->get_connection()->extended->autoExecute($this->database->get_table_name('system_announcement_publication_user'), $props, MDB2_AUTOQUERY_INSERT);
		}
		$groups = $system_announcement_publication->get_target_groups();
		foreach($groups as $group_id)
		{
			$props = array();
			$props[$this->database->escape_column_name('system_announcement_publication')] = $system_announcement_publication->get_id();
			$props[$this->database->escape_column_name('group_id')] = $group_id;
			$this->database->get_connection()->extended->autoExecute($this->database->get_table_name('system_announcement_publication_group'), $props, MDB2_AUTOQUERY_INSERT);
		}


		$condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_ID, $system_announcement_publication->get_id());
		return $this->database->update($system_announcement_publication, $condition);
	}

	function delete_registration($registration)
	{
		$condition = new EqualityCondition(Registration :: PROPERTY_ID, $registration->get_id());
		return $this->database->delete($registration->get_table_name(), $condition);
	}

	function delete_system_announcement_publication($system_announcement_publication)
	{
		$condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_ID, $system_announcement_publication->get_id());
		return $this->database->delete($system_announcement_publication->get_table_name(), $condition);
	}

	// Inherited.
	function get_next_language_id()
	{
		return $this->database->get_next_id(Language :: get_table_name());
	}

	// Inherited.
	function get_next_registration_id()
	{
		return $this->database->get_next_id(Registration :: get_table_name());
	}

	// Inherited.
	function get_next_setting_id()
	{
		return $this->database->get_next_id(Setting :: get_table_name());
	}

	function get_next_system_announcement_publication_id()
	{
		return $this->database->get_next_id(SystemAnnouncementPublication :: get_table_name());
	}

	function create_language($language)
	{
		return $this->database->create($language);
	}

	function create_registration($registration)
	{
		return $this->database->create($registration);
	}

	function create_setting($setting)
	{
		return $this->database->create($setting);
	}

	function create_system_announcement_publication($system_announcement_publication)
	{
		if ($this->database->create($system_announcement_publication))
		{
			$users = $system_announcement_publication->get_target_users();
    		foreach($users as $user_id)
    		{
    			$props = array();
    			$props[$this->database->escape_column_name('system_announcement_publication')] = $system_announcement_publication->get_id();
    			$props[$this->database->escape_column_name('user')] = $user_id;
    			$this->database->get_connection()->extended->autoExecute($this->database->get_table_name('system_announcement_publication_user'), $props, MDB2_AUTOQUERY_INSERT);
    		}
    		$groups = $system_announcement_publication->get_target_groups();
    		foreach($groups as $group_id)
    		{
    			$props = array();
    			$props[$this->database->escape_column_name('system_announcement_publication')] = $system_announcement_publication->get_id();
    			$props[$this->database->escape_column_name('group_id')] = $group_id;
    			$this->database->get_connection()->extended->autoExecute($this->database->get_table_name('system_announcement_publication_group'), $props, MDB2_AUTOQUERY_INSERT);
    		}

    		return true;
		}
		else
		{
		    return false;
		}
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function count_system_announcement_publications($condition = null)
	{
		return $this->database->count_objects(SystemAnnouncementPublication :: get_table_name(), $condition);
	}

	function retrieve_system_announcement_publication($id)
	{
		$condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(SystemAnnouncementPublication :: get_table_name(), $condition);
	}

	function retrieve_system_announcement_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->database->retrieve_objects(SystemAnnouncementPublication :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

	function retrieve_system_announcement_publication_target_groups($system_announcement_publication)
	{
		$query = 'SELECT * FROM '.$this->database->escape_table_name('system_announcement_publication_group').' WHERE system_announcement_publication = ?';
		$sth = $this->database->get_connection()->prepare($query);
		$res = $sth->execute($system_announcement_publication->get_id());

		$groups = array();
		while($target_group = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$groups[] = $target_group['group_id'];
		}

		return $groups;
	}

	function retrieve_system_announcement_publication_target_users($system_announcement_publication)
	{
		$query = 'SELECT * FROM '.$this->database->escape_table_name('system_announcement_publication_user').' WHERE system_announcement_publication = ?';
		$sth = $this->database->get_connection()->prepare($query);
		$res = $sth->execute($system_announcement_publication->get_id());

		$users = array();
		while($target_user = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$users[] = $target_user['user'];
		}

		return $users;
	}

	function get_next_category_id()
	{
		return $this->database->get_next_id('admin_category');
	}

	function delete_category($category)
	{
		$condition = new EqualityCondition(AdminCategory :: PROPERTY_ID, $category->get_id());
		$succes = $this->database->delete('admin_category', $condition);

		$query = 'UPDATE '.$this->database->escape_table_name('admin_category').' SET '.
				 $this->database->escape_column_name(AdminCategory :: PROPERTY_DISPLAY_ORDER).'='.
				 $this->database->escape_column_name(AdminCategory :: PROPERTY_DISPLAY_ORDER).'-1 WHERE '.
				 $this->database->escape_column_name(AdminCategory :: PROPERTY_DISPLAY_ORDER).'>? AND ' .
				 $this->database->escape_column_name(AdminCategory :: PROPERTY_PARENT) . '=?';
		$statement = $this->database->get_connection()->prepare($query);
		$statement->execute(array($category->get_display_order(), $category->get_parent()));

		return $succes;
	}

	function update_category($category)
	{
		$condition = new EqualityCondition(AdminCategory :: PROPERTY_ID, $category->get_id());
		return $this->database->update($category, $condition);
	}

	function create_category($category)
	{
		return $this->database->create($category);
	}

	function count_categories($conditions = null)
	{
		return $this->database->count_objects('admin_category', $conditions);
	}

	function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->database->retrieve_objects('admin_category', $condition, $offset, $count, $order_property, $order_direction);
	}

	function select_next_display_order($parent_category_id)
	{
		$query = 'SELECT MAX(' . AdminCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' .
		$this->database->escape_table_name('admin_category');

		$condition = new EqualityCondition(AdminCategory :: PROPERTY_PARENT, $parent_category_id);

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this->database, $params, $prefix_properties = false);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$sth = $this->database->get_connection()->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();

		return $record[0] + 1;
	}

	public function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$query = 'SELECT * FROM '.$this->database->get_table_name('system_announcement_publication').' WHERE '.$this->database->escape_column_name('publisher').'=?';

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

				$statement = $this->database->get_connection()->prepare($query);
				$res = $statement->execute(Session :: get_user_id());
			}
		}
		else
		{
			$query = 'SELECT * FROM '.$this->database->get_table_name('system_announcement_publication').' WHERE '.$this->database->escape_column_name('learning_object').'=?';
			$statement = $this->database->get_connection()->prepare($query);
			$res = $statement->execute($object_id);
		}
		$publication_attr = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$info = new LearningObjectPublicationAttributes();
			$info->set_id($record['id']);
			$info->set_publisher_user_id($record['publisher']);
			$info->set_publication_date($record['published']);
			$info->set_application('admin');
			//TODO: i8n location string
			$info->set_location('');
			//TODO: set correct URL
			$info->set_url('index_admin.php?go=sysviewer&announcement='. $record['id']);
			$info->set_publication_object_id($record['learning_object']);
			$publication_attr[] = $info;
		}
		return $publication_attr;
	}

	public function get_learning_object_publication_attribute($publication_id)
	{
		$condition = new EqualityCondition('id',$publication_id);
		$record = $this->database->next_result();

		$info = new LearningObjectPublicationAttributes();
		$info->set_id($record->get_id());
		$info->set_publisher_user_id($record->get_publisher());
		$info->set_publication_date($record->get_publication_date());
		$info->set_application('admin');
		//TODO: i8n location string
		$info->set_location('');
		//TODO: set correct URL
		$info->set_url('index_admin.php?go=sysviewer&announcement='. $record->get_id());
		$info->set_publication_object_id($record->get_learning_object());
		return $info;
	}

	public function any_learning_object_is_published($object_ids)
	{
		$condition = new InCondition(SystemAnnouncementPublication :: PROPERTY_LEARNING_OBJECT_ID,$object_ids);
		return $this->database->count_objects('system_announcement_publication',$condition)>=1;
	}

	public function count_publication_attributes($type = null, $condition = null)
	{
		$condition = new EqualityCondition('publisher', Session :: get_user_id());
		return $this->database->count_objects('system_announcement_publication', $condition);
	}

	public function delete_learning_object_publications($object_id)
	{
		$condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_LEARNING_OBJECT_ID,$object_id);
		$this->database->delete('system_announcement_publication',$condition);
	}
}
?>