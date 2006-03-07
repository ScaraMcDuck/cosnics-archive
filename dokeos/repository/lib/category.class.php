<?php
/**
==============================================================================
 *	This class represents a category that groups learning objects together.
 *	Categories may be nested: each category stores the ID of its parent
 *	category; a value of zero means that the category is the root category.
 *	Learning objects are part of exactly one category.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class Category {
	/**
	 * Numeric identifier of the category.
	 */
	private $id;
	
	/**
	 * Title of the category.
	 */
	private $title;
	
	/**
	 * Numeric identifier of this category's parent category.
	 */
	private $parent;
	
	/**
	 * Constructor.
	 * @param int $id Numeric identifier of the category.
	 * @param string $title Title of the category.
	 * @param int $parent Numeric identifier of the category's parent
	 *                    category.
	 */
    function Category($id = 0, $title = '', $parent = 0)
    {
    	$this->id = $id;
    	$this->title = $title;
    	$this->parent = $parent;
    }
    
    /**
     * Returns the category's numeric identifier.
     * @return int The identifier.
     */
    function get_id ()
    {
    	return $this->id;
    }
    
    /**
     * Returns the category's title.
     * @return string The title.
     */
    function get_title ()
    {
    	return $this->title;
    }
    
    /**
     * Returns the numeric identifier of the category's parent category.
     * @return int The identifier.
     */
    function get_parent_category_id ()
    {
    	return $this->parent;
    }
    
    /**
     * Sets the category's numeric identifier to the passed value.
     * @param int $id The new identifier.
     */
    function set_id ($id)
    {
    	$this->id = $id;
    }
    
    /**
     * Sets the category's title to the passed value.
     * @param string $title The new title.
     */
    function set_title ($title)
    {
    	$this->title = $title;
    }
    
    /**
     * Sets the numeric identifier of the category's parent category to the
     * passed value.
     * @param int $parent The identifier.
     */
    function set_parent_category_id ($parent)
    {
    	$this->parent = $parent;
    }
    
    /**
     * Instructs the data manager to create the category, making it
     * persistent. Returns the newly assigned numeric identifier for the
     * category.
     */
    function create ()
    {
    	return DataManager :: get_instance()->create_category($this);
    }
    
    /**
     * Instructs the data manager to update the category, making any changes
     * persistent.
     */
    function update()
    {
    	return DataManager :: get_instance()->update_category($this);
    }
    
    /**
     * Instructs the data manager to delete the category, removing it from
     * persistent storage. Learning objects belonging to the category are
     * not automatically deleted.
     */
	function delete()
	{
		return DataManager :: get_instance()->delete_category($this);
	}
}
?>