<?php
class LearningObjectPublisher
{
	private $owner;
	
	private $types;
		
    function LearningObjectPublisher($owner, $types)
    {
    	$this->owner = $owner;
    	$this->types = (is_array($types) ? $types : array($types));
    }
    
    function display()
    {
    	$action = ($_GET['publish_action'] ? $_GET['publish_action'] : 'browser'); 
    	echo '<div class="tabbed-pane">';
    	echo '<ul class="tabbed-pane-tabs">';
    	foreach (array('browser', 'finder', 'creator') as $a)
    	{
    		echo '<li><a href="?tool='.$_GET['tool'].'&amp;publish_action=' . $a . '">'.get_lang(ucfirst($a).'Title').'</a></li>';
    	}
    	echo '</ul>';
    	echo '<div class="tabbed-pane-content">';
    	require_once dirname(__FILE__) . '/publisher/learningobject'.$action.'.class.php';
    	$class = 'LearningObject' . ucfirst($action);
    	$f = new $class($this->get_owner(), $this->get_types());
    	$f->set_additional_parameter('tool', $_GET['tool']);
    	$f->set_additional_parameter('publish_action', $action);
    	$f->display();
    	echo '</div>';
    	echo '</div>';
    }
    
    function get_owner()
	{
		return $this->owner;
	}
    
    function get_types()
    {
    	return $this->types;
    }
}
?>