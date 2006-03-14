<?php
class LearningObjectPublicationCategory {
	private $id;
	
	private $title;
	
	private $course;
	
	private $type;
	
	private $parent;
	
    function LearningObjectPublicationCategory($id, $title, $course, $type, $parent = null)
    {
    	$this->id = $id;
    	$this->title = $title;
    	$this->course = $course;
    	$this->type = $type;
    	$this->parent = $parent;
    }
    
    function get_id()
    {
    	return $this->id;
    }
    
    function get_title()
    {
    	return $this->title;
    }
    
    function get_course()
    {
    	return $this->course;
    }
    
    function get_type()
    {
    	return $this->type;
    }
    
    function get_parent()
    {
    	return $this->parent;
    }
}
?>