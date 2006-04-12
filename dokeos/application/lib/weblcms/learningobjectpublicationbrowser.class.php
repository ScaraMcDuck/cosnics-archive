<?php
require_once dirname(__FILE__).'/browser/learningobjectpublicationtable.class.php';
require_once dirname(__FILE__).'/browser/learningobjectpublicationcategorytree.class.php';

/**
==============================================================================
 *	This class allows the user to browse through learning object publications.
 *	For now, its layout is restricted to a sortable table of objects,
 *	accompanied by a tree view of categories. The intent is that tools
 *	extend this class in order to centralize publication browsing.
 *
 *	@author Tim De Pauw
==============================================================================
 */
abstract class LearningObjectPublicationBrowser
{
	/**
	 * The types of learning objects for which publications need to be
	 * displayed.
	 */
	private $types;
	
	/**
	 * The ID of the category that is currently active.
	 */
	private $category;
	
	/**
	 * The table used to display objects.
	 */
	private $objectTable;
	
	/**
	 * The tree view used to display categories.
	 */
	private $categoryTree;
	
	/**
	 * The tool that instantiated this browser.
	 */
	private $parent;

	/**
	 * Constructor.
	 * @param RepositoryTool $parent The tool that instantiated this browser.
	 * @param mixed $types The types of learning objects for which
	 *                     publications need to be displayed.
	 * @param int $category The ID of the category that is currently active.
	 */
	function LearningObjectPublicationBrowser($parent, $types, $category = 0)
	{
		$this->parent = $parent;
		$this->types = is_array($types) ? $types : array ($types);
		$this->category = $category;
		$this->objectTable = new LearningObjectPublicationTable($this);
		$this->categoryTree = new LearningObjectPublicationCategoryTree($this, $category);
	}

	/**
	 * Sets column titles for the learning object table.
	 */
	function set_column_titles()
	{
		$this->objectTable->set_column_titles(func_get_args());
	}
	
	/**
	 * Sets a header for the learning object table.
	 * @param int $colmn The column index.
	 * @param string $label The column title.
	 * @param boolean $sortable True if the column's contents are sortable,
	 *                          false otherwise. 
	 */
	function set_header ($column, $label, $sortable = true)
	{
		$this->objectTable->set_header($column, $label, $sortable);
	}
	
	/**
	 * Returns the publication browser's content in HTML format.
	 * @return string The HTML.
	 */
	function as_html()
	{
		return '<div style="float: left; width: 20%">'
			. $this->categoryTree->as_html()
			. '</div>'
			. '<div style="float: right; width: 80%">'
			. $this->objectTable->as_html()
			. '</div>';
	}
	
	/**
	 * Returns the ID of the current category.
	 * @return int The category ID.
	 */
	function get_category()
	{
		return $this->category;
	}

	/**
	 * @see RepositoryTool :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->parent->get_user_id();
	}
	
	/**
	 * @see RepositoryTool :: get_groups()
	 */
	function get_groups()
	{
		return $this->parent->get_groups();
	}
	
	/**
	 * @see RepositoryTool :: get_course_id()
	 */
	function get_course_id()
	{
		return $this->parent->get_course_id();
	}
	
	/**
	 * @see RepositoryTool :: get_categories()
	 */
	function get_categories($list = false)
	{
		return $this->parent->get_categories($list);
	}
	
	/**
	 * @see RepositoryTool :: get_url()
	 */
	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}
	
	/**
	 * @see RepositoryTool :: get_parameters()
	 */
	function get_parameters ()
	{
		return $this->parent->get_parameters();
	}
	
	/**
	 * Returns the learning object publications to display.
	 * @param int $from The index of the first publication to return.
	 * @param int $count The maximum number of publications to return.
	 * @param int $column The index of the column to sort the table on.
	 * @param int $direction The sorting direction; either SORT_ASC or
	 *                       SORT_DESC.
	 * @return array The learning object publications.
	 */
	abstract function get_publications($from, $count, $column, $direction);

	/**
	 * Returns the number of learning object publications to display.
	 * @return int The number of publications.
	 */
	abstract function get_publication_count();
}
?>