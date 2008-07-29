<?php
/**
 * @package admin
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_setting_result_set.class.php';
require_once dirname(__FILE__).'/database/database_language_result_set.class.php';
require_once dirname(__FILE__).'/database/database_registration_result_set.class.php';
require_once dirname(__FILE__).'/../admin_data_manager.class.php';
require_once dirname(__FILE__).'/../language.class.php';
require_once dirname(__FILE__).'/../registration.class.php';
require_once dirname(__FILE__).'/../setting.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_library_path().'database/database.class.php';
require_once 'MDB2.php';

class DatabaseAdminDataManager extends AdminDataManager
{
	private $db;
	
	function initialize()
	{
		$this->db = new Database(array('language' => 'lang', 'setting' => 'setting', 'registration' => 'reg'));
		$this->db->set_prefix('admin_'); 
	}
	
    function retrieve_languages($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->db->retrieve_objects('language', 'Language', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
    function retrieve_settings($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->db->retrieve_objects('setting', 'Setting', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_registrations($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->db->retrieve_objects('registration', 'Registration', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_language_from_english_name($english_name)
	{
		$condition = new EqualityCondition(Language :: PROPERTY_ENGLISH_NAME, $english_name);
		$languages = $this->retrieve_languages($condition);
		return $languages->next_result();
	}
	
	function retrieve_setting_from_variable_name($variable, $application = 'admin')
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $application);
		$conditions[] = new EqualityCondition(Setting :: PROPERTY_VARIABLE, $variable);
		$condition = new AndCondition($conditions);
	
		$settings = $this->retrieve_settings($condition);
		return $settings->next_result();
	}
	
	function update_setting($setting)
	{
		$condition = new EqualityCondition(Setting :: PROPERTY_ID, $setting->get_id());
		return $this->db->update($setting, 'setting', $condition);
	}
	
	function update_registration($registration)
	{
		$condition = new EqualityCondition(Registration :: PROPERTY_ID, $registration->get_id());
		return $this->db->update($registration, 'registration', $condition);
	}
	
	function delete_registration($registration)
	{
		$condition = new EqualityCondition(Registration :: PROPERTY_ID, $registration->get_id());
		return $this->db->delete('registration', $condition);
	}
	
	// Inherited.
	function get_next_language_id()
	{
		return $this->db->get_next_id('language');
	}
	
	// Inherited.
	function get_next_registration_id()
	{
		return $this->db->get_next_id('registration');
	}
	
	// Inherited.
	function get_next_setting_id()
	{
		return $this->db->get_next_id('setting');
	}
	
	function create_language($language)
	{
		return $this->db->create($language, 'language');
	}
	
	function create_registration($registration)
	{
		return $this->db->create($registration, 'registration');
	}
	
	function create_setting($setting)
	{
		return $this->db->create($setting, 'setting');
	}	
	
	function create_storage_unit($name,$properties,$indexes)
	{
		return $this->db->create_storage_unit($name,$properties,$indexes);
	}

}
?>