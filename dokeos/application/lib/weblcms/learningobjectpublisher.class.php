<?php
class LearningObjectPublisher
{
	private $owner;
	
	private $types;
	
	private $course;
		
    function LearningObjectPublisher($types, $course, $owner)
    {
    	$this->owner = $owner;
    	$this->types = (is_array($types) ? $types : array($types));
    	$this->course = $course;
    }
    
    function as_html()
    {
    	$action = ($_GET['publish_action'] ? $_GET['publish_action'] : 'browser'); 
    	$out = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
    	foreach (array('browser', 'finder', 'creator') as $a)
    	{
    		$out .= '<li><a href="?tool='.$_GET['tool'].'&amp;publish_action=' . $a . '">'.get_lang(ucfirst($a).'Title').'</a></li>';
    	}
    	$out .= '</ul><div class="tabbed-pane-content">';
    	require_once dirname(__FILE__) . '/publisher/learningobject'.$action.'.class.php';
    	$class = 'LearningObject' . ucfirst($action);
    	$f = new $class($this->get_owner(), $this->get_types());
    	$f->set_additional_parameter('tool', $_GET['tool']);
    	$f->set_additional_parameter('publish_action', $action);
    	$out .= $f->as_html() . '</div></div>';
    	return $out;
    }
    
    function get_owner()
	{
		return $this->owner;
	}
    
    function get_types()
    {
    	return $this->types;
    }
    
    function get_course ()
    {
    	return $this->course;
    }
}
?>