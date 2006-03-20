<?php
class LearningObjectPublisher
{
	private $owner;

	private $types;

	private $course;

	private $parent;

	function LearningObjectPublisher($parent, $types, $course, $owner)
	{
		$this->parent = $parent;
		$this->owner = $owner;
		$this->types = (is_array($types) ? $types : array ($types));
		$this->course = $course;
		$parent->set_parameter('publish_action', $this->get_action());
	}

	function as_html()
	{
		$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		foreach (array ('browser', 'finder', 'publicationcreator') as $a)
		{
			$out .= '<li><a href="'.$this->get_url(array ('publish_action' => $a)).'">'.get_lang(ucfirst($a).'Title').'</a></li>';
		}
		$out .= '</ul><div class="tabbed-pane-content">';
		$action = $this->get_action();
		require_once dirname(__FILE__).'/publisher/learningobject'.$action.'.class.php';
		$class = 'LearningObject'.ucfirst($action);
		$f = new $class ($this, $this->get_owner(), $this->get_types());
		$out .= $f->as_html().'</div></div>';
		return $out;
	}

	function get_parent()
	{
		return $this->parent;
	}

	function get_owner()
	{
		return $this->owner;
	}

	function get_types()
	{
		return $this->types;
	}

	function get_course()
	{
		return $this->course;
	}

	function get_action()
	{
		return ($_GET['publish_action'] ? $_GET['publish_action'] : 'browser');
	}
	
	function get_url($parameters = array())
	{
		return $this->parent->get_url($parameters);
	}
	
	function get_parameters()
	{
		return $this->parent->get_parameters();
	}
	
	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}
	
	function get_categories()
	{
		return $this->parent->get_categories($this->course, $_GET['tool']);
	}
}
?>