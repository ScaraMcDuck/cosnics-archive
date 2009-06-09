<?php
/**
 * @package application.lib.portfolio
 */
require_once Path :: get_repository_path(). 'lib/abstract_learning_object.class.php';

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class PortfolioPublisher
{
	const PARAM_ACTION = 'publish_action';
	const PARAM_EDIT = 'edit';
	const PARAM_LEARNING_OBJECT_ID = 'object';

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
	
	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function PortfolioPublisher($parent, $types, $mail_option = false)
	{
		$this->parent = $parent;
		$this->default_learning_objects = array();
		$this->types = (is_array($types) ? $types : array ($types));
		$this->mail_option = $mail_option;
		$parent->set_parameter(PortfolioPublisher :: PARAM_ACTION, $this->get_action());
	}

	/**
	 * Returns the publisher's output in HTML format.
	 * @return string The output.
	 */
	function as_html()
	{
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		foreach (array ('publicationcreator','browser', 'finder') as $action)
		{
			$out .= '<li><a';
			if ($this->get_action() == $action) $out .= ' class="current"';
			$out .= ' href="'.$this->get_url(array (PortfolioPublisher :: PARAM_ACTION => $action), true).'">'.htmlentities(Translation :: get(ucfirst($action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';
		$action = $this->get_action();
		require_once dirname(__FILE__).'/publisher/portfolio_'.$action.'.class.php';
		$class = 'Portfolio'.ucfirst($action);
		$component = new $class ($this);
		$out .= $component->as_html().'</div></div>';
		return $out;
	}

	/**
	 * Returns the tool which created this publisher.
	 * @return RepositoryTool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
	}

	/**
	 * @see RepositoryTool::get_user_id()
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
		return (Request :: get(PortfolioPublisher :: PARAM_ACTION) ? Request :: get(PortfolioPublisher :: PARAM_ACTION) : 'publicationcreator');
	}

	/**
	 * @see RepositoryTool::get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}

	/**
	 * @see RepositoryTool::get_parameters()
	 */
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	/**
	 * @see RepositoryTool::set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
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
	function get_path($path_type)
	{
		return $this->parent->get_path($path_type);
	}
}
?>