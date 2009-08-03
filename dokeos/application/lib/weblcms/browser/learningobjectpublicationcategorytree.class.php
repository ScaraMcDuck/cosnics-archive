<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage browser
 */
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';
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
	
	private $data_manager;
	
	private $url_params;
	/**
	 * Create a new category tree
	 * @param PublicationBrowser $browser The browser to associate this category
	 * tree with.
	 * @param string $tree_id An id for the tree
	 */
	function LearningObjectPublicationCategoryTree($browser, $tree_id, $url_params = array())
	{
		$this->browser = $browser;
		$this->tree_id = $tree_id;
		$this->url_params = $url_params;
		$this->data_manager = WeblcmsDataManager :: get_instance();
		$menu = $this->get_menu_items();
		parent :: __construct($menu);
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
		return intval(Request :: get($this->tree_id));
	}
	
	private function get_menu_items($extra_items)
	{
		$menu = array();
		$menu_item = array();
		$menu_item['title'] = Translation :: get('Root').$this->get_category_count(0);
		$menu_item['url'] = $this->get_category_url(0);
		$sub_menu_items = $this->get_sub_menu_items(0);
		if(count($sub_menu_items) > 0)
		{
			$menu_item['sub'] = $sub_menu_items;
		}
		$menu_item['class'] = 'type_category';
		$menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
		$menu[0] = $menu_item;
		if (count($extra_items))
        {
        	$menu = array_merge($menu, $extra_items);
        }
		
		return $menu;
	}
	
	private function get_sub_menu_items($parent)
	{
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_PARENT, $parent);
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_COURSE, $this->browser->get_parent()->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_TOOL, $this->browser->get_parent()->get_tool_id());
		$condition = new AndCondition($conditions);
		
		$objects = $this->data_manager->retrieve_learning_object_publication_categories($condition);
		$categories = array ();
		while ($category = $objects->next_result())
		{
			$menu_item = array();
            $menu_item['title'] = $category->get_name().$this->get_category_count($category->get_id());
			$menu_item['url'] = $this->get_category_url($category->get_id());
			$sub_menu_items = $this->get_sub_menu_items($category->get_id());
			if(count($sub_menu_items) > 0)
			{
				$menu_item['sub'] = $sub_menu_items;
			}
			$menu_item['class'] = 'type_category';
			$menu_item[OptionsMenuRenderer :: KEY_ID] = $category->get_id();
			$categories[$category->get_id()] = $menu_item;
		}
		return $categories;
	}

    private function get_category_count($category_id)
    {
        $count = $this->browser->get_publication_count($category_id);
        return ($count>0)?' (' . $count . ')':'';
    }
	
	/**
	 * Gets the URL of a category
	 * @param int $category_id The id of the category of which the URL is
	 * requested
	 * @return string The URL
	 */
	private function get_category_url ($category_id)
	{
		$this->url_params[$this->tree_id] = $category_id;
		return $this->browser->get_url($this->url_params);
	}

    function get_breadcrumbs()
	{
        $array_renderer = new HTML_Menu_ArrayRenderer();
		$this->render($array_renderer, 'urhere');
		$breadcrumbs = $array_renderer->toArray();
		foreach ($breadcrumbs as &$crumb)
		{
			$split = explode('(', $crumb['title']);
                        $crumb['title'] = $split[0];
		}
		return $breadcrumbs;
	}
}
?>