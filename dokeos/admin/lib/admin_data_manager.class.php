<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'configuration/configuration.class.php';

abstract class AdminDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	protected function AdminDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return AdminDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'AdminDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
	
	abstract function create_storage_unit($name,$properties,$indexes);
	
	abstract function get_next_setting_id();
	
	abstract function get_next_language_id();
	
	abstract function get_next_registration_id();
	
	abstract function get_next_system_announcement_publication_id();
	
	abstract function create_language($language);
	
	abstract function create_registration($registration);
	
	abstract function create_setting($setting);
	
	abstract function create_system_announcement_publication($system_announcement_publication);

	abstract function retrieve_languages($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
	
	abstract function retrieve_settings($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
	
	abstract function retrieve_registrations($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
	
	abstract function retrieve_setting_from_variable_name($variable, $application = 'admin');
	
	abstract function retrieve_language_from_english_name($english_name);
	
	abstract function update_setting($setting);
	
	abstract function update_registration($registration);
	
	abstract function update_system_announcement_publication($system_announcement_publication);
	
	abstract function delete_registration($registration);
	
	abstract function delete_system_announcement_publication($system_announcement_publication);
	
	function get_languages()
	{
		$options = array();
		
		$languages = $this->retrieve_languages();
		while ($language = $languages->next_result())
		{
			$options[$language->get_folder()] = $language->get_original_name();
		}
		
		return $options;
	}
	
    /**
	 * Count the system announcements
	 * @param Condition $condition
	 * @return int
	 */	
    abstract function count_system_announcement_publications($condition = null);
    
	/**
	 * Retrieve a system announcement
	 * @param int $id
	 * @return SystemAnnouncementPublication
	 */	
	abstract function retrieve_system_announcement_publication($id);
    
    /**
	 * Retrieve a series of system announcements 
	 * @param Condition $condition
	 * @param array $orderBy
	 * @param array $orderDir
	 * @param int $offset
	 * @param int $maxObjects
	 * @return SystemAnnouncementPublicationResultSet
	 */	
    abstract function retrieve_system_announcement_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
    
    abstract function retrieve_system_announcement_publication_target_class_groups($system_announcement_publication);
    
    abstract function retrieve_system_announcement_publication_target_users($system_announcement_publication);

	abstract function get_next_category_id();
	abstract function select_next_display_order($parent_category_id);
	abstract function delete_category($category);
	abstract function update_category($category);
	abstract function create_category($category);
	abstract function count_categories($conditions = null);
	abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	
}
?>
