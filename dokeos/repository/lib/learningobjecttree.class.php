<?php
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__).'/../../repository/lib/condition/equalitycondition.class.php';

class LearningObjectTree extends HTML_Menu
{
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;
	/**
	 * The category type at the end of the tree
	 */
	private $endType;
	/**
	 * The types used in get_items()
	 */
	private $typeArray;

	/**
	 * Creates a new forum navigation menu.
	 * @param int $parent The ID of the displayed forum.
	 * @param array $type_array An array with the types used in get_items().
	 * @param array $end_type An array with the types at the end of tree.
	 * @param string $url_format The format to use for the URL.
	 *                           Passed to sprintf(). Defaults to the string
	 *                           "?id=%s".
	 */
	public function LearningObjectTree($parent, $type_array, $end_type, $url_format = '?id=%s' )
	{
		$this->parent = $parent;
		$this->urlFmt = $url_format;
		$this->endType = $end_type;
		$this->typeArray = $type_array;	
		parent :: __construct($this->get_items($parent));
	}
	/**
	 * Returns the tree items
	 * @param int $parent The ID of the parent item.
	 * @return array An array with all the tree items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	private function get_items($parent)
	{
		if(isset($this->typeArray) && count($this->typeArray))
		{
			$equality_array = array();
			foreach($this->typeArray as $condition)
			{
				$equality_array[]	= new EqualityCondition('type',$condition);
				
			}
			$cond2 = new OrCondition($equality_array);			
		}
		else
			$cond2 = new EqualityCondition('type', null);
		$cond1 = new EqualityCondition('parent', $parent);
		$condition = new AndCondition($cond1, $cond2);
		$datamanager = RepositoryDataManager :: get_instance();
		$objects = $datamanager->retrieve_learning_objects(null, $condition);
		$items = array ();
		foreach ($objects as $index => $item)
		{
			$items[$item->get_parent_id()][] = $item;
		}
		$sub_tree = array();
		foreach ($items[$parent] as $index => $item)
		{
			$menu_item = array(); 
			$menu_item['title'] = $item->get_title();
			$menu_item['url'] = $this->get_id_url($item->get_id());
			$menu_item['id'] = $item->get_id();
			$items = $this->get_items($item->get_id());
			if(!in_array($item->get_type(), $this->endType))
				$menu_item['sub'] = $items;
			$sub_tree[$item->get_id()] = $menu_item;
		}
		return $sub_tree;
	}

	private function get_id_url($id)
	{
		return sprintf($this->urlFmt, $id);
	}
}