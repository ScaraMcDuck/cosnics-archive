<?php
/**
 * @package repository
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once dirname(__FILE__).'/tree_menu_renderer.class.php';
require_once dirname(__FILE__).'/options_menu_renderer.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects.
 * @author Sven Vanpoucke
 */
class ComplexLearningObjectMenu extends HTML_Menu
{
	
	private $current_item;
	private $root;
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;
	/**
	 * The array renderer used to determine the breadcrumbs.
	 */
	private $array_renderer;
	
	private $dm;
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
	function ComplexLearningObjectMenu($root, $current_item, $url_format = '?go=browsecomplex&cloi_id=%s&cloi_root_id=%s')
	{
		$this->current_item = $current_item;
		$this->root = $root;
		$this->urlFmt = $url_format;
		$this->dm = RepositoryDataManager :: get_instance();
		$menu = $this->get_menu($root);
		parent :: __construct($menu);
		$this->array_renderer = new HTML_Menu_ArrayRenderer();
		$this->forceCurrentUrl($this->get_cloi_url($current_item->get_id()));
	}
	
	function get_menu($root)
	{
		$menu = array();
		$datamanager = $this->dm;
		$lo = $datamanager->retrieve_learning_object($root->get_ref());
		$menu_item = array();
		$menu_item['title'] = $lo->get_title();
		$menu_item['url'] = $this->get_cloi_url($root->get_id());
	
		$sub_menu_items = $this->get_menu_items($root);
		if(count($sub_menu_items) > 0)
		{
			$menu_item['sub'] = $sub_menu_items;
		}
	
		$menu_item['class'] = 'type_' . $lo->get_type();
		$menu_item[OptionsMenuRenderer :: KEY_ID] = $root->get_id();
		$menu[$root->get_id()] = $menu_item;
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
	private function get_menu_items($cloi)
	{
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $cloi->get_id());
		$datamanager = $this->dm;
		$objects = $datamanager->retrieve_complex_learning_object_items($condition);
		
		while ($object = $objects->next_result())
		{
			if($object->is_extended())
			{
				if($object->is_complex_ref())
					$object = $datamanager->retrieve_complex_learning_object_item($object->get_ref());
				
				$lo = $datamanager->retrieve_learning_object($object->get_ref());
				$menu_item = array();
				$menu_item['title'] = $lo->get_title();
				$menu_item['url'] = $this->get_cloi_url($object->get_id());
			
				$sub_menu_items = $this->get_menu_items($object);
				if(count($sub_menu_items) > 0)
				{
					$menu_item['sub'] = $sub_menu_items;
				}
			
				$menu_item['class'] = 'type_' . $lo->get_type();
				$menu_item[OptionsMenuRenderer :: KEY_ID] = $object->get_id();
				$menu[$object->get_id()] = $menu_item;
			}
		}
		
		return $menu;
	}

	private function get_cloi_url($cloi_id)
	{
		return htmlentities(sprintf($this->urlFmt, $cloi_id, $this->root->get_id()));
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