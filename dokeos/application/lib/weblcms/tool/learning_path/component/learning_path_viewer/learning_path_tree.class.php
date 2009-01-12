<?php
/**
 * @package repository
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects.
 * @author Sven Vanpoucke
 */
class LearningPathTree extends HTML_Menu
{
	
	private $current_step;
	private $lp_id;
	
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;

	private $objects;
	
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
	function LearningPathTree($lp_id, $current_step, $url_format)
	{
		$this->current_step = $current_step;
		$this->lp_id = $lp_id;
		$this->urlFmt = $url_format;
		$this->dm = RepositoryDataManager :: get_instance();
		$menu = $this->get_menu($lp_id);
		parent :: __construct($menu);
		$this->array_renderer = new HTML_Menu_ArrayRenderer();
		$this->forceCurrentUrl($this->get_url($current_step));
	}
	
	function get_menu($lp_id)
	{
		$menu = array();
		$datamanager = $this->dm;
		$lo = $datamanager->retrieve_learning_object($lp_id);
		$menu_item = array();
		$menu_item['title'] = $lo->get_title();
		//$menu_item['url'] = $this->get_url($lp_id);
	
		$sub_menu_items = $this->get_menu_items($lp_id);
		if(count($sub_menu_items) > 0)
		{
			$menu_item['sub'] = $sub_menu_items;
		}
		$menu_item['class'] = 'type_' . $lo->get_type();
		//$menu_item['class'] = 'type_category';
		$menu_item[OptionsMenuRenderer :: KEY_ID] = -1;
		$menu[$lp_id] = $menu_item;
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
	 
	private $step = 1;
	 
	private function get_menu_items($parent)
	{
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $parent);
		$datamanager = $this->dm;
		$objects = $datamanager->retrieve_complex_learning_object_items($condition);
		
		while ($object = $objects->next_result())
		{
			$lo = $datamanager->retrieve_learning_object($object->get_ref());
				
			$menu_item = array();
			$menu_item['title'] = $lo->get_title();
			$menu_item['class'] = 'type_' . $lo->get_type();
			$menu_item[OptionsMenuRenderer :: KEY_ID] = -1;
			
			$sub_menu_items = array();
			
			if($lo->get_type() == 'learning_path_chapter')
				$sub_menu_items = $this->get_menu_items($object->get_ref());
			
			if(count($sub_menu_items) > 0)
			{
				$menu_item['sub'] = $sub_menu_items;
			}
			else
			{
				$menu_item['url'] = $this->get_url($this->step);
				$menu_item[OptionsMenuRenderer :: KEY_ID] = $this->step;
				$this->objects[$this->step] = $lo;
				$this->step++;
			}

			$menu[$object->get_ref()] = $menu_item;
		}
		
		return $menu;
	}
	
	function get_object($step)
	{
		return $this->objects[$step];
	}

	private function get_url($current_step)
	{
		return htmlentities(sprintf($this->urlFmt, $current_step));
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
	
	function count_steps()
	{
		return count($this->objects);
	}
}