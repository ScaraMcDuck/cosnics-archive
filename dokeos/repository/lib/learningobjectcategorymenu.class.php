<?php
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
		parent :: HTML_Menu($menu);
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
			$menu_item['id'] = $category->get_id();
			$menu_item['sub'] = $this->get_sub_menu_items($categories, $category->get_id());
			$menu_item['class'] = 'type_category';
			$sub_tree[$category->get_id()] = $menu_item;
		}
		return $sub_tree;
	}
	private function get_category_url ($category)
	{
		// TODO: Put another class in charge of the htmlentities() invocation
		return htmlentities(sprintf($this->urlFmt, $category));
	}
	/**
	 * Get the breadcrumbs which lead to the current category.
	 * @return array The breadcrumbs.
	 */
	public function get_breadcrumbs()
	{
		$renderer =& new HTML_Menu_ArrayRenderer();
		$this->render($renderer,'urhere');
		$breadcrumbs = $renderer->toArray();
		//$current_location = array_pop($breadcrumbs);
		foreach($breadcrumbs as $index => $breadcrumb)
		{
			$interbredcrump[] = array ("url" => $breadcrumb['url'], "name" => $breadcrumb['title']);
		}
		return $interbredcrump;
	}

	function render_as_tree()
	{
		$renderer = new TreeMenuRenderer();
		$this->render($renderer, 'sitemap');
		return $renderer->toHTML();
	}
}