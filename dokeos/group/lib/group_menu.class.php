<?php
/**
 * @package application.weblcms.course
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
require_once dirname(__FILE__) . '/group.class.php';
require_once dirname(__FILE__) . '/group_data_manager.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through
 * categories of courses.
 * @author Bart Mollet
 */
class GroupMenu extends HTML_Menu
{
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;
	/**
	 * The array renderer used to determine the breadcrumbs.
	 */
	private $array_renderer;
	
	private $include_root;
	
	private $exclude_children;
	
	private $current_category;
	
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
	function GroupMenu($current_category, $url_format = '?go=browse&group_id=%s', $include_root = true, $exclude_children = false)
	{
		$this->include_root = $include_root;
		$this->exclude_children = $exclude_children;
		$this->current_category = $current_category;
		
		$this->urlFmt = $url_format;
		$menu = $this->get_menu();
		//print_r($menu);
		parent :: __construct($menu);
		$this->array_renderer = new HTML_Menu_ArrayRenderer();
		$this->forceCurrentUrl($this->get_url($current_category));
	}
	
	function get_menu()
	{
		$include_root = $this->include_root;
		
		$condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
		$group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, array(Group :: PROPERTY_SORT), array(SORT_ASC))->next_result();
		
		if (!$include_root)
		{
			return $this->get_menu_items($group->get_id());
		}
		else
		{
			$menu = array();
			
			$menu_item = array();
			$menu_item['title'] = $group->get_name();
			$menu_item['url'] = $this->get_url($group->get_id());
		
			$sub_menu_items = $this->get_menu_items($group->get_id());
			if(count($sub_menu_items) > 0)
			{
				$menu_item['sub'] = $sub_menu_items;
			}
		
			$menu_item['class'] = 'home';
			$menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();
			$menu[$group->get_id()] = $menu_item;
			return $menu;
		}
	}
	
	/**
	 * Returns the menu items.
	 * @param array $extra_items An array of extra tree items, added to the
	 *                           root.
	 * @return array An array with all menu items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	private function get_menu_items($parent_id = 0)
	{ 
		$exclude_children = $this->exclude_children;
		$current_category = $this->current_category;
		
		$condition = new EqualityCondition(Group :: PROPERTY_PARENT, $parent_id);
		$groups = GroupDataManager :: get_instance()->retrieve_groups($condition, null, null, array(Group :: PROPERTY_SORT), array(SORT_ASC));
		
		while ($group = $groups->next_result())
		{
			$group_id = $group->get_id();
			
			if (!($exclude_children && $group_id == $current_category))
			{
				$menu_item = array();
				$menu_item['title'] = $group->get_name();
				$menu_item['url'] = $this->get_url($group->get_id());
				
				$sub_menu_items = $this->get_menu_items($group->get_id());
				
				if(count($sub_menu_items) > 0)
				{
					$menu_item['sub'] = $sub_menu_items;
				}
				
				$menu_item['class'] = 'type_category';
				$menu_item[OptionsMenuRenderer :: KEY_ID] = $group->get_id();
				$menu[$group->get_id()] = $menu_item;
			}
		}
		
		return $menu;
	}
	
	/**
	 * Gets the URL of a given category
	 * @param int $category The id of the category
	 * @return string The requested URL
	 */
	private function get_url ($group)
	{
		// TODO: Put another class in charge of the htmlentities() invocation
		return htmlentities(sprintf($this->urlFmt, $group));
	}
	
	private function get_home_url ($category)
	{
		// TODO: Put another class in charge of the htmlentities() invocation
		return htmlentities(str_replace('&group_id=%s', '', $this->urlFmt));
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
		$this->render($renderer, 'sitemap');
		return $renderer->toHTML();
	}
}