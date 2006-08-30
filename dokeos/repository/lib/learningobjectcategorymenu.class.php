<?php
/**
 * $Id$
 * @package repository
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__).'/../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../../repository/lib/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/treemenurenderer.class.php';
require_once dirname(__FILE__).'/optionsmenurenderer.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects.
 * @author Bart Mollet
 */
class LearningObjectCategoryMenu extends HTML_Menu
{
	/**
	 * The owner of the categories
	 */
	private $owner;
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;
	/**
	 * The array renderer used to determine the breadcrumbs.
	 */
	private $array_renderer;
	/**
	 * Creates a new category navigation menu.
	 * @param int $owner The ID of the owner of the categories to provide in
	 * this menu.
	 * @param int $current_category The ID of the current category in the menu.
	 * @param string $url_format The format to use for the URL of a category.
	 *                           Passed to sprintf(). Defaults to the string
	 *                           "?category=%s".
	 * @param array $extra_items An array of extra tree items, added to the
	 *                           root.
	 */
	function LearningObjectCategoryMenu($owner, $current_category, $url_format = '?category=%s', & $extra_items = array())
	{
		$this->owner = $owner;
		$this->urlFmt = $url_format;
		$menu = $this->get_menu_items(& $extra_items);
		parent :: __construct($menu);
		$this->array_renderer = new HTML_Menu_ArrayRenderer();
		$this->forceCurrentUrl($this->get_category_url($current_category));
	}
	/**
	 * Returns the menu items.
	 * @param array $extra_items An array of extra tree items, added to the
	 *                           root.
	 * @return array An array with all menu items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	private function get_menu_items(& $extra_items)
	{
		$condition = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $this->owner);
		$datamanager = RepositoryDataManager :: get_instance();
		$objects = $datamanager->retrieve_learning_objects('category', $condition, array(LearningObject :: PROPERTY_TITLE), array(SORT_ASC));
		$categories = array ();
		while ($category = $objects->next_result())
		{
			$categories[$category->get_parent_id()][] = $category;
		}
		$menu = & $this->get_sub_menu_items($categories, 0);
		if (count($extra_items))
		{
			$menu = array_merge($menu, $extra_items);
		}
		return $menu;
	}
	/**
	 * Returns the items of the sub menu.
	 * @param array $categories The categories to include in this menu.
	 * @param int $parent The parent category ID.
	 * @return array An array with all menu items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	private function get_sub_menu_items(& $categories, $parent)
	{
		$sub_tree = array ();
		foreach ($categories[$parent] as $index => $category)
		{
			$menu_item = array();
			$menu_item['title'] = $category->get_title();
			$menu_item['url'] = $this->get_category_url($category->get_id());
			$sub_menu_items = $this->get_sub_menu_items($categories, $category->get_id());
			if(count($sub_menu_items) > 0)
			{
				$menu_item['sub'] = $sub_menu_items;
			}
			$menu_item['class'] = 'type_category';
			$menu_item[OptionsMenuRenderer :: KEY_ID] = $category->get_id();
			$sub_tree[$category->get_id()] = $menu_item;
		}
		return $sub_tree;
	}
	/**
	 * Gets the URL of a given category
	 * @param int $category The id of the category
	 * @return string The requested URL
	 */
	private function get_category_url ($category)
	{
		// TODO: Put another class in charge of the htmlentities() invocation
		return htmlentities(sprintf($this->urlFmt, $category));
	}
	/**
	 * Get the breadcrumbs which lead to the current category.
	 * @return array The breadcrumbs.
	 */
	function get_breadcrumbs()
	{
		$this->render($this->array_renderer, 'urhere');
		$breadcrumbs = $this->array_renderer->toArray();
		foreach ($breadcrumbs as & $crumb)
		{
			$crumb['name'] = $crumb['title'];
			unset($crumb['title']);
		}
		return $breadcrumbs;
	}
	/**
	 * Renders the menu as a tree
	 * @return string The HTML formatted tree
	 */
	function render_as_tree()
	{
		$renderer = new TreeMenuRenderer();
		$this->render($renderer, 'sitemap');
		return $renderer->toHTML();
	}
}