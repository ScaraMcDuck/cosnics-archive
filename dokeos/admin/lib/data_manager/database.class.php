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
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_library_path().'database/database.class.php';
require_once 'MDB2.php';

class DatabaseAdminDataManager extends AdminDataManager
{
	private $database;
	
	function initialize()
	{
		$this->database = new Database(array('language' => 'lang', 'setting' => 'setting', 'registration' => 'reg'));
		$this->database->set_prefix('admin_'); 
	}
	
    function retrieve_languages($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->database->retrieve_objects('language', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
    function retrieve_settings($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->database->retrieve_objects('setting', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_registrations($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->database->retrieve_objects('registration', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_language_from_english_name($english_name)
	{
		$condition = new EqualityCondition(Language :: PROPERTY_ENGLISH_NAME, $english_name);
		return $this->database->retrieve_object('language', $condition);
	}
	
	function retrieve_setting_from_variable_name($variable, $application = 'admin')
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $application);
		$conditions[] = new EqualityCondition(Setting :: PROPERTY_VARIABLE, $variable);
		$condition = new AndCondition($conditions);
	
		return $this->database->retrieve_object('setting', $condition);
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
	
	function delete_registration($registration)
	{
		$condition = new EqualityCondition(Registration :: PROPERTY_ID, $registration->get_id());
		return $this->database->delete('registration', $condition);
	}
	
	// Inherited.
	function get_next_language_id()
	{
		return $this->database->get_next_id('language');
	}
	
	// Inherited.
	function get_next_registration_id()
	{
		return $this->database->get_next_id('registration');
	}
	
	// Inherited.
	function get_next_setting_id()
	{
		return $this->database->get_next_id('setting');
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
	
	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

}
?>