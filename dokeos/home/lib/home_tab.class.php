<?php
require_once dirname(__FILE__).'/home_data_manager.class.php';

class HomeTab {

	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_USER = 'user';
	
	private $id;
	private $defaultProperties;

    function HomeTab($id = null, $defaultProperties = array ())
    {
    	$this->id = $id;
		$this->defaultProperties = $defaultProperties;
    }
    
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Get the default properties of all user course categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_USER);
	}
	
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
    
    function get_id()
    {
    	return $this->id;
    }
    
    function set_id($id)
	{
		$this->id = $id;
	}
	
    function get_sort()
    {
    	return $this->get_default_property(self :: PROPERTY_SORT);
    }
    
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}
    
    function get_title()
    {
    	return $this->get_default_property(self :: PROPERTY_TITLE);
    }
	
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
    function get_user()
    {
    	return $this->get_default_property(self :: PROPERTY_USER);
    }
	
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}
	
	function update()
	{
		$wdm = HomeDataManager :: get_instance();
		$success = $wdm->update_home_row($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function create()
	{
		$wdm = HomeDataManager :: get_instance();
		$id = $wdm->get_next_home_tab_id();
		$this->set_id($id);
		$success = $wdm->create_home_tab($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function delete()
	{
		$hdm = HomeDataManager :: get_instance();
		$success = $hdm->delete_home_row($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
}
?>