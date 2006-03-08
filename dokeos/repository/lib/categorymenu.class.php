<?php
require_once 'HTML/Menu.php';
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
	public function CategoryMenu($owner,$current_category)
	{
		$this->owner = $owner;
		$menu = $this->get_menu_items();
		parent::HTML_Menu($menu);
		$this->forceCurrentUrl('index.php?category='.$current_category);
	}
	/**
	 * Get the menu items.
	 * @return array An array with all menu items. The structure of this array
	 * is the structure needed by PEAR::HTML_Menu on which this class is based.
	 */
	private function get_menu_items()
	{
		$condition = new ExactMatchCondition('owner',$this->owner);
		$datamanager = RepositoryDataManager::get_instance();
		$objects = $datamanager->retrieve_learning_objects('category',$condition);
		$categories = array();
		foreach($objects as $index => $category)
		{
			$categories[$category->get_category_id()][] = $category;
		}
		return $this->get_sub_menu_items($categories,0);
	}
	/**
	 * Get the menu items.
	 * @param array $categories An array of all categories to use in this menu
	 * @param int $parent The parent category id
	 * @return array An array with all menu items. The structure of this array
	 * is the structure needed by PEAR::HTML_Menu on which this class is based.
	 */
	private function get_sub_menu_items(&$categories,$parent)
	{
		$sub_tree = array();
		foreach($categories[$parent] as $index => $category)
		{
			$menu_item['title'] = $category->get_title();
			$menu_item['url'] = 'index.php?category='.$category->get_id();
			if(count($categories[$category->get_id()]) > 0)
			{
				$menu_item['sub'] = $this->get_sub_menu_items($categories,$category->get_id());
			}
			$sub_tree[$category->get_id()] = $menu_item;
		}
		return $sub_tree;
	}
}
/**
 * TODO Write a good MenuRenderer
 */
?>