<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../../../repository/lib/abstractlearningobject.class.php';

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class PersonalMessagePublisher
{
	const PARAM_ACTION = 'publish_action';
	//const PARAM_EDIT = 'edit';
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
	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function PersonalMessagePublisher($types, $mail_option = false)
	{
		$this->default_learning_objects = array();
		$this->types = (is_array($types) ? $types : array ($types));
		$this->mail_option = $mail_option;
		$parent->set_parameter(PErsonalMessagePublisher :: PARAM_ACTION, $this->get_action());
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
			$out .= ' href="'.$this->get_url(array (PersonalMessagePublisher :: PARAM_ACTION => $action), true).'">'.htmlentities(get_lang(ucfirst($action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';
		$action = $this->get_action();
		require_once dirname(__FILE__).'/publisher/personalmessage'.$action.'.class.php';
		$class = 'PersonalMessage'.ucfirst($action);
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
		return ($_GET[LearningObjectPublisher :: PARAM_ACTION] ? $_GET[LearningObjectPublisher :: PARAM_ACTION] : 'publicationcreator');
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
}
?>