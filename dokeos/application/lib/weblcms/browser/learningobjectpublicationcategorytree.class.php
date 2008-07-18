<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage browser
 */
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
require_once 'HTML/Menu.php';
/**
 * A tree menu to display categories in a tool
 */
class LearningObjectPublicationCategoryTree extends HTML_Menu
{
	/**
	 * The browser to which this category tree is associated
	 */
	private $browser;
	/**
	 * An id for this tree
	 */
	private $tree_id;
	/**
	 * Create a new category tree
	 * @param PublicationBrowser $browser The browser to associate this category
	 * tree with.
	 * @param string $tree_id An id for the tree
	 */
	function LearningObjectPublicationCategoryTree($browser, $tree_id)
	{
		$this->browser = $browser;
		$this->tree_id = $tree_id;
		parent :: __construct($this->get_as_tree($browser->get_categories()));
		$this->forceCurrentUrl($this->get_category_url($this->get_current_category_id()));
	}
	/**
	 * Returns the HTML output of this category tree.
	 * @return string The HTML output
	 */
	function as_html()
	{
		$renderer =& new TreeMenuRenderer();
		$this->render($renderer, 'sitemap');
		return $renderer->toHtml();
	}
	/**
	 * Gets the current selected category id.
	 * @return int The current category id
	 */
	function get_current_category_id()
	{
		return intval($_GET[$this->tree_id]);
	}
	/**
	 * Creates a tree of categories from a given list of categories.
	 * @param array $categories The list of categories on which the tree will be
	 * based
	 */
	private function get_as_tree($categories)
	{
		return $this->convert_tree($categories);
	}
	/**
	 * Recursive function to turn a list of categories into a tree structure.
	 * @param array $tree The list of categories
	 * @return array A tree structured representation of the given list of
	 * categories
	 */
	private function convert_tree($tree)
	{
		$new_tree = array ();
		$i = 0;
		foreach ($tree as $oldNode)
		{
			$node = array ();
			$obj = $oldNode['obj'];
			$node['url'] = $this->get_category_url($obj->get_id());
			$node['sub'] = $this->convert_tree($oldNode['sub']);
			$node['count'] = 0;
			$node['count'] = $this->browser->get_publication_count($obj->get_id());
			foreach($node['sub'] as $index => $subnode)
			{
				$node['count'] += $subnode['count'];
			}
			$node['title'] = $obj->get_title().' ('.$node['count'].')';
			$new_tree[$i ++] = $node;
		}
		return $new_tree;
	}
	/**
	 * Gets the URL of a category
	 * @param int $category_id The id of the category of which the URL is
	 * requested
	 * @return string The URL
	 */
	private function get_category_url ($category_id)
	{
		return $this->browser->get_url(array($this->tree_id => $category_id));
	}
}
?>