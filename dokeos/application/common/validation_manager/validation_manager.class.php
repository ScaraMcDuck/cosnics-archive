<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of validation_manager
 *
 * @author Pieter Hens
 */

require_once dirname(__FILE__).'/validation_manager_component.class.php';
require_once PATH :: get_admin_path().'/lib/admin_data_manager.class.php';


 class ValidationManager {

    const PARAM_ACTION = 'validation_action';
	const PARAM_VALIDATION_ID = 'validation_id';
	//const PARAM_REMOVE_VALIDATION = 'remove_validation';
	const ACTION_BROWSE_VALIDATION = 'browse_validation';
	const ACTION_CREATE_VALIDATION = 'create_validation';
	//const ACTION_UPDATE_VALIDATION = 'update_validation';
	const ACTION_DELETE_VALIDATION = 'delete_validation';


	private $parent;

	private $parameters;

    private $application;


	function ValidationManager($parent,$application)
	{
		$this->parent = $parent;
        $this->application = $application;
		//$parent->set_parameter(self :: PARAM_ACTION, $this->get_action());
		//$this->parse_input_from_table();
	}

	function run()
	{
        
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_VALIDATION :
				$component = ValidationManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_CREATE_VALIDATION :
                
				$component = ValidationManagerComponent :: factory('Creator', $this);
				break;
			/*case self :: ACTION_UPDATE_VALIDATION :
				$component = ValidationManagerComponent :: factory('Updater', $this);
				break;*/
			case self :: ACTION_DELETE_VALIDATION :
				$component = ValidationManagerComponent :: factory('Deleter', $this);
				break;
			default :
				$component = ValidationManagerComponent :: factory('Browser', $this);
		}
		$component->run();

	}

	/**
	 * Returns the tool which created this publisher.
	 * @return Tool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
	}

	function display_header($breadcrumbtrail)
	{
		return $this->parent->display_header($breadcrumbtrail, false, false);
	}

	function display_footer()
	{
		return $this->parent->display_footer();
	}

	/**
	 * @see Tool::get_user_id()
	 */
	function get_user_id()
	{
		return $this->parent->get_user_id();
	}

	function get_user()
	{
		return $this->parent->get_user();
	}

    function get_application()
    {
        return $this->application;
    }

	/**
	 * Returns the action that the user selected, or "publicationcreator" if none.
	 * @return string The action.
	 */
	function get_action()
	{
		return $_GET[self :: PARAM_ACTION];
	}

	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}

	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}

   

	/**
	 * Sets a default learning object. When the creator component of this
	 * publisher is displayed, the properties of the given learning object will
	 * be used as the default form values.
	 * @param string $type The learning object type.
	 * @param LearningObject $learning_object The learning object to use as the
	 *                                        default for the given type.
	 */
	function set_default_learning_object($type, $learning_object)
	{
		$this->default_learning_objects[$type] = $learning_object;
	}

	function get_default_learning_object($type)
	{
		if(isset($this->default_learning_objects[$type]))
		{
			return $this->default_learning_objects[$type];
		}
		return new AbstractLearningObject($type, $this->get_user_id());
	}

	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
	}

	function repository_redirect($action = null, $message = null, $cat_id = 0, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $cat_id, $error_message, $extra_params);
	}

	function get_extra_parameters()
	{
		return $this->parameters;
	}

	function set_extra_parameters($parameters)
	{
		$this->parameters = $parameters;
	}


    function retrieve_validations($pid,$cid,$application)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_validations($pid,$cid,$application);
        
    }

    function retrieve_validation($id)
    {
        $adm = AdminDataManager :: get_instance();
        return $adm->retrieve_validation($id);
    }

	
}
?>
