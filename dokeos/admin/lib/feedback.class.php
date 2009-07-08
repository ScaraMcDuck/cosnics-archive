<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of feedback
 *
 * @author Pieter Hens
 */

require_once dirname(__FILE__).'/admin_data_manager.class.php';

class Feedback {
    const CLASS_NAME				= __CLASS__;

	const PROPERTY_ID				= 'id';
	const PROPERTY_APPLICATION		= 'application';
	const PROPERTY_PID              = 'pid';
	const PROPERTY_CID              = 'cid';
    const PRPOPERTY_FID             = 'fid';

	private $id;
	private $defaultProperties;

	/**
	 * Creates a new PM object.
	 * @param int $id The numeric ID of the feedabck object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the feedback
	 *                                 object. Associative array.
	 */
	function Feedback($id = 0, $defaultProperties = array ())
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
	 * Get the default properties of all feedbacks.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_APPLICATION, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE);
	}

	/**
	 * Sets a default property of this feedback by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Checks if the given identifier is the name of a default feedback
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
	 * Returns the id of this feedback.
	 * @return int The feedback id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the application of this feedback object
	 * @return string The feedback application
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
	  * Returns feedback id
	  * @return integer the fid
	  */
	function get_fid()
	{
		return $this->get_default_property(self :: PROPERTY_FID);
	}

	/**
	 * Sets the id of this feedback.
	 * @param int $id The feedback id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Sets the application of this feedback.
	 * @param string $application the feedback application.
	 */
	function set_application($application)
	{
		$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
	}

	/**
	 * Sets the pid of this feedback.
	 * @param integer $pid the pid.
	 */
	function set_pid($pid)
	{
		$this->set_default_property(self :: PROPERTY_VARIABLE, $pid);
	}

	/**
	 * Sets the cid of this feedback.
	 * @param integer $cid the cid.
	 */
	function set_cid($cid)
	{
		$this->set_default_property(self :: PROPERTY_VALUE, $cid);
	}

    /**
	 * Sets the fid of this feedback.
	 * @param integer $fid the fid.
	 */
    function set_fid($fid)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $fid);
    }

	/**
	 * Instructs the data manager to create the feedback, making it
	 * persistent. Also assigns a unique ID to the feedback
	 * @return boolean True if creation succeeded, false otherwise.
	 */
	function create()
	{
		$adm = AdminDataManager :: get_instance();
		$id = $adm->get_next_feedback_id();
		$this->set_id($id);
		return $adm->create_feedback($this);
	}

	/**
	 * Deletes this feedback from persistent storage
	 * @see PAdminDataManager::delete_feedback()
	 */
	function delete()
	{
		return AdminDataManager :: get_instance()->delete_feedback($this);
	}

	/**
	 * Updates this feedback in persistent storage
	 * @see AdminDataManager::update_feedback()
	 */
	function update()
	{
		return AdminDataManager :: get_instance()->update_feedback($this);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>
