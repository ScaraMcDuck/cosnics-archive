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
	 * Creates a new forum navigation menu.
	 * @param int $parent The ID of the displayed forum.
	 * @param string $url_format The format to use for the URL.
	 *                           Passed to sprintf(). Defaults to the string
	 *                           "?id=%s".
	 */
	public function LearningObjectTree($parent, $type_array, $url_format = '?id=%s' )
	{
		$this->parent = $parent;
		$this->urlFmt = $url_format;
		if (isset($type_array) && count($type_array))
		{
			$equality_array = array();
			foreach($type_array as $condition)
			{
				$equality_array[]	= new EqualityCondition('type',$condition);
			}
		}
		else
			$equality_array = new EqualityCondition('type',null);			
		parent :: __construct($this->get_items(1,$equality_array));
	}

	private function get_items($parent, $equality_array)
	{
		$cond2 = new EqualityCondition('type', null);
		if($equality_array != null)
			$cond2 = new OrCondition($equality_array);
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
			$menu_item['title'] = $item->get_title();
			$menu_item['url'] = $this->get_id_url($item->get_id());
			$menu_item['id'] = $item->get_id();
			$items = $this->get_items($item->get_id(),$equality_array);
			if(count($items))
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