<?php
/**
 * $Id$
 * @package application.weblcms
 */
/**
==============================================================================
 *	This class represents a category for publication within a repository tool.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class LearningObjectPublicationCategory {
   /**#@+
    * Constant defining a property of the category
 	*/
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_COURSE_ID = 'course';
	const PROPERTY_TOOL = 'tool';
	const PROPERTY_PARENT_CATEGORY_ID = 'parent';
	/**#@-*/

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
    function get_parent_category_id()
    {
    	return $this->parent;
    }

    /**
     * Sets the numeric identifier of the category.
     * @param int $id The identifier.
     */
    function set_id($id)
    {
    	$this->id = $id;
    }

    /**
     * Sets the title of the category.
     * @param string $title The title.
     */
    function set_title($title)
    {
    	$this->title = $title;
    }

    /**
     * Returns the numeric identifier of the parent category.
     * @param int $parent The parent category identifier.
     */
    function set_parent_category_id($parent)
    {
    	$this->parent = $parent;
    }

	/**
	 * Creates this category in persistent storage
	 * @see WeblcmsDataManager::create_learning_object_publication_category()
	 */
    function create()
	{
		$dm = WeblcmsDataManager :: get_instance();
		$id = $dm->get_next_learning_object_publication_category_id();
		$this->set_id($id);
		return $dm->create_learning_object_publication_category($this);
	}
	/**
	 * Updates this category in persistent storage
	 * @see WeblcmsDataManager::update_learning_object_publication_category()
	 */
	function update()
	{
		return WeblcmsDataManager :: get_instance()->update_learning_object_publication_category($this);
	}
	/**
	 * Deletes this category from persistent storage
	 * @see WeblcmsDataManager::delete_learning_object_publication_category()
	 */
	function delete()
	{
		return WeblcmsDataManager :: get_instance()->delete_learning_object_publication_category($this);
	}
}
?>