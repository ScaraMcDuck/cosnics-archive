<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of validation
 *
 * @author Pieter Hens
 */

require_once dirname(__FILE__).'/admin_data_manager.class.php';

class Validation
{
    const CLASS_NAME				= __CLASS__;

	const PROPERTY_ID				= 'id';
	const PROPERTY_APPLICATION		= 'application';
	const PROPERTY_PID              = 'pid';
	const PROPERTY_CID              = 'cid';
    const PROPERTY_VALIDATED        = 'validated';
    const PROPERTY_OWNER            = 'owner';

	private $id;
	private $defaultProperties;

	/**
	 * Creates a new PM object.
	 * @param int $id The numeric ID of the feedabck object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the validation
	 *                                 object. Associative array.
	 */
	function Validation($id = 0, $defaultProperties = array ())
	{
		$this->set_id($id);
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property of this feedabck object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties of this feedabck.
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
	 * Get the default properties of all validations.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_APPLICATION, self :: PROPERTY_PID, self :: PROPERTY_CID, self :: PROPERTY_VALIDATED ,  self :: PROPERTY_OWNER);
	}

	/**
	 * Sets a default property of this validation by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Checks if the given identifier is the name of a default validation
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
	 * Returns the id of this validation.
	 * @return int The validation id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the application of this validation object
	 * @return string The validation application
	 */
	function get_application()
	{
		return $this->get_default_property(self :: PROPERTY_APPLICATION);
	}

	/**
	 * Returns publication id
	 * @return integer the pid
	 */
	function get_pid()
	{
	 	return $this->get_default_property(self :: PROPERTY_PID);
	}

	 /**
	  * Returns complex id (id within complex learning object)
	  * @return integer the cid
	  */
	function get_cid()
	{
		return $this->get_default_property(self :: PROPERTY_CID);
	}

  
    /**
	  * Returns validation id
	  * @return integer the fid
	  */
	function get_validated()
	{
		return $this->get_default_property(self :: PROPERTY_VALIDATED);
	}

    function get_owner()
	{
		return $this->get_default_property(self :: PROPERTY_OWNER);
	}

	/**
	 * Sets the id of this validation.
	 * @param int $id The validation id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Sets the application of this validation.
	 * @param string $application the validation application.
	 */
	function set_application($application)
	{
		$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
	}

	/**
	 * Sets the pid of this validation.
	 * @param integer $pid the pid.
	 */
	function set_pid($pid)
	{
		$this->set_default_property(self :: PROPERTY_PID, $pid);
	}

	/**
	 * Sets the cid of this validation.
	 * @param integer $cid the cid.
	 */
	function set_cid($cid)
	{
		$this->set_default_property(self :: PROPERTY_CID, $cid);
	}


    /**
	 * Sets the validated of this validation.
	 * @param integer $validated the validated.
	 */
    function set_validated($validated)
    {
        $this->set_default_property(self :: PROPERTY_VALIDATED, $validated);
    }

     function set_owner($owner)
    {
        $this->set_default_property(self :: PROPERTY_OWNER, $owner);
    }



	/**
	 * Instructs the data manager to create the validation, making it
	 * persistent. Also assigns a unique ID to the validation
	 * @return boolean True if creation succeeded, false otherwise.
	 */
	function create()
	{
		$adm = AdminDataManager :: get_instance();
		$id = $adm->get_next_validation_id();
		$this->set_id($id);
		return $adm->create_validation($this);
	}

	/**
	 * Deletes this validation from persistent storage
	 * @see PAdminDataManager::delete_validation()
	 */
	function delete()
	{
		return AdminDataManager :: get_instance()->delete_validation($this);
	}

	/**
	 * Updates this validation in persistent storage
	 * @see AdminDataManager::update_validation()
	 */
	function update()
	{
		return AdminDataManager :: get_instance()->update_validation($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    function get_validation_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_owner());
    }
}
?>
