<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/browser/learningobjectpublicationlistrenderer.class.php';
require_once dirname(__FILE__).'/browser/learningobjectpublicationcategorytree.class.php';

/**
==============================================================================
 *	This class allows the user to browse through learning object publications.
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
	 * The list renderer used to display objects.
	 */
	protected $listRenderer;

	/**
	 * The tree view used to display categories.
	 */
	private $categoryTree;

	/**
	 * The tool that instantiated this browser.
	 */
	private $parent;
	
	private $publication_id; 

	/**
	 * Constructor.
	 * @param RepositoryTool $parent The tool that instantiated this browser.
	 * @param mixed $types The types of learning objects for which
	 *                     publications need to be displayed.
	 */
	function LearningObjectPublicationBrowser($parent, $types)
	{
		$this->parent = $parent;
		$this->types = is_array($types) ? $types : array ($types);
	}

	/**
	 * Returns the publication browser's content in HTML format.
	 * @return string The HTML.
	 */
	function as_html()
	{
		$categories = $this->get_categories();
		if (!isset($this->categoryTree) || count($categories[0]['sub'])== 0)
		{
			return $this->listRenderer->as_html();
		}
		return '<div style="float: left; width: 20%">'
			. $this->categoryTree->as_html()
			. '</div>'
			. '<div style="float: right; width: 80%">'
			. $this->listRenderer->as_html()
			. '</div>'
			. '<div class="clear">&nbsp;</div>';
	}

	/**
	 * Returns the learning object publication list renderer associated with
	 * this object.
	 * @return LearningObjectPublicationRenderer The renderer.
	 */
	function get_publication_list_renderer()
	{
		return $this->listRenderer;
	}
	/**
	 * Sets the renderer for the publication list.
	 * @param LearningObjectPublicationRenderer $renderer The renderer.
	 */
	function set_publication_list_renderer($renderer)
	{
		$this->listRenderer = $renderer;
	}
	/**
	 * Gets the publication category tree.
	 * @return LearningObjectPublicationCategoryTree The category tree.
	 */
	function get_publication_category_tree()
	{
		return $this->categoryTree;
	}
	
	function get_publication_id()
	{
		return $this->publication_id;
	}
	
	function set_publication_id($publication_id)
	{
		$this->publication_id = $publication_id;  
	}
	
	/**
	 * Sets the publication category tree.
	 * @param LearningObjectPublicationCategoryTree $tree The category tree.
	 */
	function set_publication_category_tree($tree)
	{
		$this->categoryTree = $tree;
	}

	/**
	 * Returns the repository tool that this browser is associated with.
	 * @return RepositoryTool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
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
	
	function get_user_info($user_id)
	{
		return $this->parent->get_user_info($user_id);
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
	 * @see RepositoryTool :: get_parameter()
	 */
	 function get_parameter($name)
	 {
	 	return $this->parent->get_parameter($name);
	 }

	/**
	 * @see Tool :: is_allowed()
	 */
	function is_allowed ($right)
	{
		return $this->parent->is_allowed($right);
	}

	/**
	 * @see WebLcms::get_last_visit_date()
	 */
	function get_last_visit_date()
	{
		return $this->parent->get_last_visit_date();
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
	
	function get_path($path_type)
	{
		return $this->get_parent()->get_parent()->get_path($path_type);
	}
}
?>