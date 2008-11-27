<?php
/**
 * @package application.lib.encyclopedia
 */
require_once Path :: get_library_path() . 'redirect.class.php';
require_once Path :: get_repository_path() . 'lib/abstract_learning_object.class.php';
require_once dirname(__FILE__).'/component/publication_candidate_table/publication_candidate_table.class.php';

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class Publisher
{
	const PARAM_ACTION = 'publish_action';
	const PARAM_EDIT = 'edit';
	const PARAM_ID = 'object';
	
	const PARAM_PUBLISH_SELECTED = 'publish_selected';

	/**
	 * The types of learning object that this publisher is aware of and may
	 * publish.
	 */
	private $types;

	/**
	 * The default learning objects, which are used for form defaults.
	 */
	private $default_learning_objects;
	
	private $parent;
	
	private $publisher_actions;
	
	private $parameters;
	
	private $mail_option;
	
	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function Publisher($parent, $types, $mail_option = false)
	{
		$this->parent = $parent;
		$this->default_learning_objects = array();
		$this->parameters = array();
		$this->types = (is_array($types) ? $types : array ($types));
		$this->mail_option = $mail_option;
		$this->set_publisher_actions(array ('creator','browser', 'finder'));
		$this->set_parameter(Publisher :: PARAM_ACTION, ($_GET[Publisher :: PARAM_ACTION] ? $_GET[Publisher :: PARAM_ACTION] : 'creator'));
	}

	/**
	 * Returns the tool which created this publisher.
	 * @return Tool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
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

	/**
	 * Returns the types of learning object that this object may publish.
	 * @return array The types.
	 */
	function get_types()
	{
		return $this->types;
	}

	/**
	 * Returns the action that the user selected, or "publicationcreator" if none.
	 * @return string The action.
	 */
	function get_action()
	{
		return $this->get_parameter(Publisher :: PARAM_ACTION);
	}
	
	function set_action($action)
	{
		$this->set_parameter(Publisher :: PARAM_ACTION, $action);
	}

	function get_url($parameters = array(), $encode_entities = false, $filter = array())
	{
		$parameters = array_merge($this->parent->get_parameters(), $parameters);
		return Redirect :: get_url($parameters, $filter, $encode_entities);
		//return $this->parent->get_url($parameters, $encode);
	}

	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	
	function get_parameters()
	{
		return $this->parameters;
	}

	function set_parameters($parameters = array())
	{
		$this->parameters = $parameters;
	}
	
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
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
	
	function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
	{
		if (isset($message))
		{
			$parameters[$error_message ? Redirect :: PARAM_ERROR_MESSAGE :  Redirect :: PARAM_MESSAGE] = $message;
		}
		
		$parameters = array_merge($this->get_parent()->get_parameters(), $parameters);
		Redirect :: url($parameters, $filter, $encode_entities);
	}
	
	function get_publisher_actions()
	{
		return $this->publisher_actions;
	}
	
	function set_publisher_actions($publisher_actions)
	{
		$this->publisher_actions = $publisher_actions;
	}
	
	/**
	 * Determines if this learning object publisher supports the option to send
	 * published learning objects by email.
	 * @return boolean
	 */
	function with_mail_option()
	{
		return $this->mail_option;
	}
	
	function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_publication_ids = $_POST[PublicationCandidateTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
			
			switch ($_POST['action'])
			{
				case self :: PARAM_PUBLISH_SELECTED :
					$this->set_action('multipublisher');
					break;
			}
		}
	}
}
?>