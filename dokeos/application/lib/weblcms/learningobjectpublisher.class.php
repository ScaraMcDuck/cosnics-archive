<?php

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class LearningObjectPublisher
{
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
	 * Constructor.
	 * @param RepositoryTool $parent The tool that is creating this object.
	 * @param array $types The learning object types that may be published.
	 */
	function LearningObjectPublisher($parent, $types)
	{
		$this->parent = $parent;
		$this->types = (is_array($types) ? $types : array ($types));
		$parent->set_parameter('publish_action', $this->get_action());
	}

	/**
	 * Returns the publisher's output in HTML format.
	 * @return string The output.
	 */
	function as_html()
	{
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		foreach (array ('browser', 'finder', 'publicationcreator') as $action)
		{
			$out .= '<li><a href="'.$this->get_url(array ('publish_action' => $action)).'">'.get_lang(ucfirst($action).'Title').'</a></li>';
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
	 * Returns the action that the user selected, or "browser" if none.
	 * @return string The action.
	 */
	function get_action()
	{
		return ($_GET['publish_action'] ? $_GET['publish_action'] : 'browser');
	}
	
	/**
	 * @see RepositoryTool::get_url()
	 */
	function get_url($parameters = array())
	{
		return $this->parent->get_url($parameters);
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
	function get_categories()
	{
		return $this->parent->get_categories();
	}
}
?>