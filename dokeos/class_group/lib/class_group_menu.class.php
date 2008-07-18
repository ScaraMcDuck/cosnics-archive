<?php
/**
 * @package application.weblcms.course
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of courses.
 * @author Bart Mollet
 */
class ClassGroupMenu extends HTML_Menu
{
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
	function ClassGroupMenu($current_category, $url_format = '?firstletter=%s' , $extra_items = array())
	{
		$this->urlFmt = $url_format;
		$menu = $this->get_menu_items($extra_items);
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
	private function get_menu_items($extra_items)
	{
		$menu = array();
		if (count($extra_items))
		{
			$menu = array_merge($menu, $extra_items);
		}
		
		$home = array ();
		$home['title'] = Translation :: get('Home');
		$home['url'] = $this->get_home_url();
		$home['class'] = 'home';
		$home_item[] = $home;
		for ($i = 0; $i <= 7; $i++)
		{
			$menu_item['title'] = Translation :: get(chr(65 + (3*$i)).chr(67 + (3*$i)));
			$menu_item['url'] = $this->get_category_url(chr(65 + (3*$i)));
			$menu_item['class'] = 'type_category';
			$home_item[] = $menu_item;
		}
		$menu_item = array ();
		$menu_item['title'] = Translation :: get('YZ');
		$menu_item['url'] = $this->get_category_url(chr(89));
		$menu_item['class'] = 'type_category';
		$home_item[] = $menu_item;
		$menu = array_merge($home_item, $menu);
		return $menu;
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
	
	private function get_home_url ($category)
	{
		// TODO: Put another class in charge of the htmlentities() invocation
		return htmlentities(str_replace('&firstletter=%s', '', $this->urlFmt));
	}
	/**
	 * Get the breadcrumbs which lead to the current category.
	 * @return array The breadcrumbs.
	 */
	function get_breadcrumbs()
	{
		$this->render($this->array_renderer, 'urhere');
		$breadcrumbs = $this->array_renderer->toArray();
		foreach ($breadcrumbs as $crumb)
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
		$this->render($renderer, 'tree');
		return $renderer->toHTML();
	}
}