<?php
/**
 * @package repository
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/menu/drag_and_drop_tree_menu_renderer.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * reservations categories
 * @author Sven Vanpoucke
 */
class CategoryMenu extends HTML_Menu
{
	
	private $current_item;
	/**
	 * The array renderer used to determine the breadcrumbs.
	 */
	private $array_renderer;
	
	private $category_manager;
	
	/**
	 * Creates a new category navigation menu.
	 * @param int $owner The ID of the owner of the categories to provide in
	 * this menu.
	 * @param int $current_category The ID of the current category in the menu.
	 * @param string $url_format The format to use for the URL of a category.
	 *                           Passed to sprintf(). Defaults to the string
	 *                           "?category=%s".
	 */
	function CategoryMenu($current_item, $category_manager)
	{
		$this->current_item = $current_item;
		$this->category_manager = $category_manager;
		$menu = $this->get_menu();
		parent :: __construct($menu);
		
		$this->array_renderer = new HTML_Menu_ArrayRenderer();
		$this->forceCurrentUrl($this->get_url($current_item));
	}
	
	function get_menu()
	{
		$menu = array();
		
		$menu_item = array();
		$menu_item['title'] = Translation :: get('Categories');
		$menu_item['url'] = $this->get_url();
	
		$sub_menu_items = $this->get_menu_items(0);
		if(count($sub_menu_items) > 0)
		{
			$menu_item['sub'] = $sub_menu_items;
		}
	
		$menu_item['class'] = 'type_category';
		$menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
		$menu[0] = $menu_item;
		return $menu;
	}
	
	/**
	 * Returns the menu items.
	 * @param array $extra_items An array of extra tree items, added to the
	 *                           root.
	 * @return array An array with all menu items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	private function get_menu_items($parent_id)
	{
		$condition = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $parent_id);
		$objects = $this->category_manager->retrieve_categories($condition, null, null, array(PlatformCategory :: PROPERTY_DISPLAY_ORDER), array(SORT_ASC));
		
		while ($object = $objects->next_result())
		{
			$menu_item = array();
			$menu_item['title'] = $object->get_name();
			$menu_item['url'] = $this->get_url($object->get_id());
			
			$sub_menu_items = $this->get_menu_items($object->get_id());
			
			if(count($sub_menu_items) > 0)
			{
				$menu_item['sub'] = $sub_menu_items;
			}
			
			$menu_item['class'] = 'type_category';
			$menu_item[OptionsMenuRenderer :: KEY_ID] = $object->get_id();
			$menu[$object->get_id()] = $menu_item;
		}
		
		return $menu;
	}

	private function get_url($id)
	{
		if(!$id) $id = 0;
		
		return $this->category_manager->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $id));
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
		$renderer = new DragAndDropTreeMenuRenderer('category_changer');
		$this->render($renderer, 'sitemap'); 
		return $renderer->toHTML();
	}
}