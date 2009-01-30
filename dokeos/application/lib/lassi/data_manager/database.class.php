<?php
require_once dirname(__FILE__).'/../lassi_data_manager.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';
/**
 * This is an implementation of a personal calendar datamanager using the PEAR::
 * MDB2 package as a database abstraction layer.
 */
class DatabaseLassiDatamanager extends LassiDatamanager
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
		return false;
	}
	
	public function any_learning_object_is_published($object_ids)
	{
		$condition = new InCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT,$object_ids);
		return false;
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
		return array();
	}
	/**
	 * @see Application::get_learning_object_publication_attribute()
	 */
	public function get_learning_object_publication_attribute($publication_id)
	{
		$info = new LearningObjectPublicationAttributes();
		return $info;
	}
	/**
	 * @see Application::count_publication_attributes()
	 */
	public function count_publication_attributes($type = null, $condition = null)
	{
		return 0;
	}
	/**
	 * @see Application::delete_learning_object_publications()
	 */
	public function delete_learning_object_publications($object_id)
	{
	}
	/**
	 * @see Application::update_learning_object_publication_id()
	 */
	function update_learning_object_publication_id($publication_attr)
	{
		return true;
	}
}
?>