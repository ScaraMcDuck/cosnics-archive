<?php
/**
==============================================================================
 *	This class represents a category for publication within a repository tool.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class LearningObjectPublicationCategory {
	/**
	 * The numeric identifier of the category.
	 */
	private $id;
	
	/**
	 * The title of the category.
	 */
	private $title;
	
	/**
	 * The identifier of the course.
	 */
	private $course;
	
	/**
	 * The name of the tool from which the publication was made.
	 */
	private $tool;
	
	/**
	 * The numeric identifier of this category's parent category.
	 */
	private $parent;
	
	/**
	 * Constructor.
	 * @param int $id The numeric identifier of the category.
	 * @param string $title The title of the category.
	 * @param string $course The identifier of the course.
	 * @param string $tool The name of the tool from which the publication was
	 *                     made.
	 * @param int $parent The numeric identifier of the category's parent
	 *                    category. If omitted, a value of 0 (the root
	 *                    category) is assumed.
	 */
    function LearningObjectPublicationCategory($id, $title, $course, $tool, $parent = 0)
    {
    	$this->id = $id;
    	$this->title = $title;
    	$this->course = $course;
    	$this->tool = $tool;
    	$this->parent = $parent;
    }
    
    /**
     * Returns the numeric identifier of the category.
     * @return int The identifier.
     */
    function get_id()
    {
    	return $this->id;
    }
    
    /**
     * Returns the title of the category.
     * @return string The title.
     */
    function get_title()
    {
    	return $this->title;
    }
    
    /**
     * Returns the identifier of the course.
     * @return string The course identifier.
     */
    function get_course()
    {
    	return $this->course;
    }
    
    /**
     * Returns the name of the tool in which the publication occurred.
     * @return string The tool name.
     */
    function get_tool()
    {
    	return $this->tool;
    }
    
    /**
     * Returns the numeric identifier of the parent category.
     * @return int The parent category identifier.
     */
    function get_parent()
    {
    	return $this->parent;
    }
}
?>