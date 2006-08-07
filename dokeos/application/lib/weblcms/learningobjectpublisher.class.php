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

class LearningObjectPublisher
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
	 * The RepositoryTool instance from which this publisher was created.
	 */
	private $parent;

	/**
	 * The default learning objects, which are used for form defaults.
	 */
	private $default_learning_objects;

	/**
	 * Constructor.
	 * @param RepositoryTool $parent The tool that is creating this object.
	 * @param array $types The learning object types that may be published.
	 */
	function LearningObjectPublisher($parent, $types)
	{
		$this->parent = $parent;
		$this->default_learning_objects = array();
		$this->types = (is_array($types) ? $types : array ($types));
		$parent->set_parameter(LearningObjectPublisher :: PARAM_ACTION, $this->get_action());
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
			$out .= ' href="'.$this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => $action), true).'">'.htmlentities(get_lang(ucfirst($action).'Title')).'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';
		$action = $this->get_action();
		require_once dirname(__FILE__).'/publisher/learningobject'.$action.'.class.php';
		$class = 'LearningObject'.ucfirst($action);
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
	 * @see RepositoryTool::get_course_id()
	 */
	function get_course_id()
	{
		return $this->parent->get_course_id();
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

	/**
	 * @see RepositoryTool::get_categories()
	 */
	function get_categories($list = false)
	{
		return $this->parent->get_categories($list);
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

	/**
	 * Returns the default learning object, of which the properties can be used
	 * as default form values.
	 * @param string $type The learning object type.
	 * @return LearningObject The object, or null if it is unavailable.
	 */
	function get_default_learning_object($type)
	{
		if(isset($this->default_learning_objects[$type]))
		{
			return $this->default_learning_objects[$type];
		}
		return new AbstractLearningObject($type, $this->get_user_id());
	}
}
?>