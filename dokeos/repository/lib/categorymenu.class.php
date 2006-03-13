<?php
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/DirectTreeRenderer.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__).'/../../repository/lib/condition/equalitycondition.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects
 */
class CategoryMenu extends HTML_Menu
{
	/**
	 * The owner of the categories
	 */
	private $owner;
	/**
	 * Creates a new navigation menu
	 * @param int $owner The id of the owner of the categories to provide in
	 * this menu
	 * @param int $current_category The id of the current category in the menu
	 */
	public function CategoryMenu($owner, $current_category)
	{
		$this->owner = $owner;
		$menu = $this->get_menu_items();
		parent :: HTML_Menu($menu);
		$this->forceCurrentUrl('index.php?category='.$current_category);
	}
	/**
	 * Get the menu items.
	 * @return array An array with all menu items. The structure of this array
	 * is the structure needed by PEAR::HTML_Menu on which this class is based.
	 */
	private function get_menu_items()
	{
		$condition = new EqualityCondition('owner', $this->owner);
		$datamanager = RepositoryDataManager :: get_instance();
		$objects = $datamanager->retrieve_learning_objects('category', $condition);
		$categories = array ();
		foreach ($objects as $index => $category)
		{
			$categories[$category->get_category_id()][] = $category;
		}
		return $this->get_sub_menu_items($categories, 0);
	}
	/**
	 * Get the menu items.
	 * @param array $categories An array of all categories to use in this menu
	 * @param int $parent The parent category id
	 * @return array An array with all menu items. The structure of this array
	 * is the structure needed by PEAR::HTML_Menu on which this class is based.
	 */
	private function get_sub_menu_items(& $categories, $parent)
	{
		$sub_tree = array ();
		foreach ($categories[$parent] as $index => $category)
		{
			$menu_item['title'] = $category->get_title();
			$menu_item['url'] = 'index.php?category='.$category->get_id();
			$menu_item['id'] = $category->get_id();
			if (count($categories[$category->get_id()]) > 0)
			{
				$menu_item['sub'] = $this->get_sub_menu_items($categories, $category->get_id());
			}
			$sub_tree[$category->get_id()] = $menu_item;
		}
		return $sub_tree;
	}
	/**
	 * Get the breadcrumbs which lead to the current category
	 * @return array The array with the breadcrumbs
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
}
/**
 * Renderer which can be used to include a tree menu in your page.
 */
class TreeMenuRenderer extends HTML_Menu_DirectTreeRenderer
{
	/**
	 * Constructor
	 */
	public function TreeMenuRenderer()
	{
		$entryTemplates = array (HTML_MENU_ENTRY_INACTIVE => '<a href="{url}">{title}</a>', HTML_MENU_ENTRY_ACTIVE => '<!--ACTIVE--><a href="{url}" class="treeMenuActive">{title}</a>', HTML_MENU_ENTRY_ACTIVEPATH => '<a href="{url}">{title}</a>');
		parent :: setEntryTemplate($entryTemplates);
		parent :: setItemTemplate('<li>', '</li>');
	}
	/**
	 * @see HTML_Menu_DirectTreeRenderer::finishLevel()
	 */
	function finishLevel($level)
	{
		if ($level == 0)
		{
			parent :: setLevelTemplate('<ul id="treeMenu">', '</ul>');
		}
		parent :: finishLevel($level);
		if ($level == 0)
		{
			parent :: setLevelTemplate('<ul>', '</ul>');
		}
	}
	/**
	 * @see HTML_Menu_DirectTreeRenderer::toHtml()
	 */
	function toHtml()
	{
		$html = parent::toHtml();
		$html = str_replace('<li><!--ACTIVE-->','<li id="treeMenuSelect">',$html);
		return $html;
	}
}
?>