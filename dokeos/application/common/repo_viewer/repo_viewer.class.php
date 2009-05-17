<?php
/**
 * @package application.lib.encyclopedia
 */
require_once Path :: get_library_path() . 'redirect.class.php';
require_once Path :: get_repository_path() . 'lib/abstract_learning_object.class.php';
require_once dirname(__FILE__).'/component/learning_object_table/learning_object_table.class.php';

/**
==============================================================================
 *	This class provides the means to repoviewer a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class RepoViewer
{
	const PARAM_ACTION = 'repoviewer_action';
	const PARAM_EDIT = 'edit';
	const PARAM_ID = 'object';
	const PARAM_EDIT_ID = 'obj';

	const PARAM_PUBLISH_SELECTED = 'repoviewer_selected';

	/**
	 * The types of learning object that this repo_viewer is aware of and may
	 * repoviewer.
	 */
	private $types;

	/**
	 * The default learning objects, which are used for form defaults.
	 */
	private $default_learning_objects;

	private $parent;

	private $repo_viewer_actions;

	private $parameters;

	private $mail_option;

	private $maximum_select;

	private $excluded_objects;

	/**
	 * You have two choices for the select multiple
	 * 0 / SELECT MULTIPLE - you can select as many lo as you want
	 * A number > 0 - Max defined selected learning objects
	 */
	const SELECT_MULTIPLE = 0;
	const SELECT_SINGLE = 1;

	/**
	 * Constructor.
	 * @param array $types The learning object types that may be repoviewered.
	 * @param  boolean $email_option If true the repo_viewer has the option to
	 * send the repoviewered learning object by email to the selecter target users.
	 */
	function RepoViewer($parent, $types, $mail_option = false, $maximum_select = self :: SELECT_MULTIPLE, $excluded_objects = array())
	{
		$this->maximum_select = $maximum_select;
		$this->parent = $parent;
		$this->default_learning_objects = array();
		$this->parameters = array();
		$this->types = (is_array($types) ? $types : array ($types));
		$this->mail_option = $mail_option;
		$this->set_repo_viewer_actions(array ('creator','browser', 'finder'));
		$this->excluded_objects = $excluded_objects;
		$this->set_parameter(RepoViewer :: PARAM_ACTION, ($_GET[RepoViewer :: PARAM_ACTION] ? $_GET[RepoViewer :: PARAM_ACTION] : 'creator'));
	}

	function set_maximum_select($maximum_select)
	{
		$this->maximum_select = $maximum_select;
	}

	function get_maximum_select()
	{
		return $this->maximum_select;
	}

	/**
	 * Returns the tool which created this repo_viewer.
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
	 * Returns the types of learning object that this object may repoviewer.
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
		return $this->get_parameter(RepoViewer :: PARAM_ACTION);
	}

	function set_action($action)
	{
		$this->set_parameter(RepoViewer :: PARAM_ACTION, $action);
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

	private $creation_defaults;

	function set_creation_defaults($defaults)
	{
		$this->creation_defaults = $defaults;
	}

	function get_creation_defaults()
	{
		return $this->creation_defaults;
	}

	function redirect_complex($type)
	{
		return true;
	}

	/**
	 * Sets a default learning object. When the creator component of this
	 * repo_viewer is displayed, the properties of the given learning object will
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
		if(!$error_message)
		{
			$parameters[Redirect :: PARAM_MESSAGE] = $message;
		}
		else
		{
			$parameters[Redirect :: PARAM_ERROR_MESSAGE] = $message;
		}

		$parameters = array_merge($this->get_parent()->get_parameters(), $parameters);
		Redirect :: url($parameters, $filter, $encode_entities);
	}

	function get_repo_viewer_actions()
	{
		return $this->repo_viewer_actions;
	}

	function set_repo_viewer_actions($repo_viewer_actions)
	{
		$this->repo_viewer_actions = $repo_viewer_actions;
	}

	function with_mail_option()
	{
		return $this->mail_option;
	}

	function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_publication_ids = $_POST[LearningObjectTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

			if(!is_array($selected_publication_ids)) $selected_publication_ids = array($selected_publication_ids);

			switch ($_POST['action'])
			{
				case self :: PARAM_PUBLISH_SELECTED :
					if($this->get_maximum_select() > 0)
					{
						if(count($selected_publication_ids) > $this->get_maximum_select())
						{
							$_GET['message'] = sprintf(Translation :: get('MaximumSelectableLOReached'), count($selected_publication_ids), $this->get_maximum_select());
							$_POST['action'] = null;
							$_GET['action'] = null;
							return;
						}
					}
					$redirect_params = array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ID => $selected_publication_ids));

					$this->redirect(null, false, $redirect_params);
					break;
			}
		}
	}

	function get_excluded_objects()
	{
		return $this->excluded_objects;
	}

	function set_excluded_objects($excluded_objects)
	{
		$this->excluded_objects = $excluded_objects;
	}
}
?>