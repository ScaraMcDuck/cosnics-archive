<?php
require_once dirname(__FILE__).'/webservice_data_manager.class.php';
require_once dirname(__FILE__).'/webservice_rights.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';

/**
 * @package webservice
 */
/**
 *	@author Stefan Billiet
 */

class WebserviceRegistration
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_PARENT = 'parent';
	const PROPERTY_ACTIVE = 'active';
	const PROPERTY_APPLICATION = 'application';	
	const PROPERTY_CATEGORY = 'category';    
	
	/**
	 * Default properties of the webservice object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new webservice object.
	 * @param int $id The numeric ID of the webservice object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the webservice
	 *                                 object. Associative array.
	 */
	function Webservice($id = 0, $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property of this webservice object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this webservice.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all webservices.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PARENT, self :: PROPERTY_ACTIVE);
	}
	
	/**
	 * Sets a default property of this webservice by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default webservice
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	/**
	 * Returns the id of this webservice.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the application of this webservice.
	 */
	function get_application()
	{
		return $this->get_default_property(self :: PROPERTY_APPLICATION);
	}	

	/**
	 * Returns the name of this webservice.
	 * @return String The name
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the description of this webservice.
	 * @return String The description
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	function get_parent()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT);
	}
	
	function get_category()
	{
		return $this->get_default_property(self :: PROPERTY_CATEGORY);
	}
	
	function get_active()
	{
		return $this->get_default_property(self :: PROPERTY_ACTIVE);
	}
	

	/**
	 * Sets the webservice_id of this webservice.
	 * @param int $webservice_id The webservice_id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}	
	
	function set_category($category)
	{
		$this->set_default_property(self :: PROPERTY_CATEGORY, $category);
	}

	/**
	 * Sets the application of this webservice.
	 */
	function set_application($application)
	{
		$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
	}
	
	
	/**
	 * Sets the name of this webservice.
	 * @param String $name the name.
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Sets the description of this webservice.
	 * @param String $description the description.
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	function set_active($active)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $active);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
/**
	 * Instructs the Datamanager to delete this webservice.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return WebserviceDataManager :: get_instance()->delete_webservice($this);
	}
	
	function truncate()
	{
		return WebserviceDataManager :: get_instance()->truncate_webservice($this);
	}
	
	function create()
	{       
		$wdm = WebserviceDataManager :: get_instance();
		$this->set_id($wdm->get_next_webservice_id());

        $wdm->create_webservice($this);
        
        $location = new Location();
		$location->set_location($this->get_name());
		$location->set_application('webservice');
		$location->set_type('webservice');
		$location->set_identifier($this->get_id());

        //echo $location->get_location();
        
        if($this->get_parent())
        {
			$parent = WebserviceRights :: get_location_id_by_identifier('webservice_category', $this->get_parent());
            $location->set_parent($parent);
        }
		else
			$location->set_parent(WebserviceRights :: get_root_id());

        //echo 'parent : ' . $location->get_parent();
        
		if (!$location->create())
		{
			return false;
		}

		return true;
	}
	
	function update() 
	{
		$wdm = WebserviceDataManager :: get_instance();
		$success = $wdm->update_webservice($this);
		if (!$success)
		{
			return false;
		}

		return true;	
	}
    
}
?>