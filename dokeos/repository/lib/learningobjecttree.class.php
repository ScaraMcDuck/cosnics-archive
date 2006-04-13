<?php
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__).'/../../repository/lib/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../../repository/lib/condition/orcondition.class.php';

class LearningObjectTree extends HTML_Menu
{
	/**
	 * The string passed to sprintf() to construct URLs.
	 */
	private $urlFmt;
	/**
	 * The types of learning objects to display as tree leaves.
	 */
	private $leafTypes;
	/**
	 * The types of learning objects to retrieve.
	 */
	private $types;
	/**
	 * The ID of the root learning object.
	 */
	private $root;
	/**
	 * A Condition instance that limits retrieval to the given types of
	 * learning objects.
	 */
	private $typeCondition;
	
	/**
	 * The ID of the root learning object.
	 */
	private $rootID;

	/**
	 * Creates a new learning object tree.
	 * @param int $root The ID of the root learning object.
	 * @param array $types The types of learning objects to retrieve, or null
	 *                     to accept any type.
	 * @param array $leaf_types The types of learning objects to display as
	 *                          tree leaves, or null for exhaustive retrieval.
	 * @param string $url_format The format to use for the URL. Passed to
	 *                           sprintf(). Defaults to the string "?id=%s".
	 */
	function LearningObjectTree($root, $types, $leaf_types, $url_format = '?id=%s')
	{
		$this->root = $root;
		$this->urlFmt = $url_format;
		$this->leafTypes = $leaf_types;
		$this->types = $types;
		$this->typeCondition = self :: get_type_condition($types);
		$this->rootID = $root;
		$items = & $this->build();
		parent :: __construct($items);
	}
	
	/**
	 * Sets the active item to the item with the given ID.
	 * @param int $id The ID of the active item.
	 */
	function set_active_item($id)
	{
		$this->forceCurrentUrl($this->get_url($id));
	}
	
	/**
	 * Builds the tree.
	 * @return array An array representing the tree.
	 */
	private function build()
	{
		$datamanager = RepositoryDataManager :: get_instance();
		$object = $datamanager->retrieve_learning_object($this->rootID);
		$menu_item['title'] = $object->get_title();
		$menu_item['url'] = $this->get_url($object->get_id()); 
		$menu_item['id'] = $this->rootID;
		if(!in_array($object->get_type(), $this->leafTypes))
		{
			$menu_item['sub'] = $this->get_children($this->rootID);
		}
		$tree[] = $menu_item;
		return $tree;	
	}
	
	/**
	 * Returns the items that have the learning object with the given ID as
	 * their parent object.
	 * @param int $parent The ID of the parent learning object.
	 * @return array An array with all the subtree items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	private function get_children($parent)
	{
		$parentCond = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $parent);
		$condition = (isset($this->typeCondition) ? new AndCondition($parentCond, $this->typeCondition) : $parentCond);
		$datamanager = RepositoryDataManager :: get_instance();
		$objects = $datamanager->retrieve_learning_objects(null, $condition);
		$sub_tree = array();
		foreach ($objects as $object)
		{
			$menu_item = array(); 
			$menu_item['title'] = $object->get_title();
			$menu_item['url'] = $this->get_url($object->get_id());
			$menu_item['id'] = $object->get_id();
			if (!$this->is_leaf_type($object->get_type()))
			{
				$menu_item['sub'] = $this->get_children($object->get_id());
			}
			$sub_tree[] = $menu_item;
		}
		return $sub_tree;
	}
	
	/**
	 * Checks if the given learning object type should be displayed as a leaf.
	 * @param string $type The type name.
	 * @return boolean True if the type is a leaf type, false otherwise.
	 */
	private function is_leaf_type($type)
	{
		return in_array($type, $this->leafTypes);
	}
	
	/**
	 * Gets the URL for the learning object with the given ID.
	 * @param int $id The ID.
	 * @return string The URL.
	 */
	private function get_url($id)
	{
		return sprintf($this->urlFmt, $id);
	}
	
	/**
	 * Gets the condition that corresponds to the given types.
	 * @param array $types The types.
	 * @return Condition The condition.
	 */
	private static function get_type_condition($types)
	{
		if (!isset($types))
		{
			return null;
		}
		if (is_array($types))
		{
			$cond = array();
			foreach ($types as $type)
			{
				$cond[] = self :: get_type_condition($type);
			}
			return new OrCondition($cond);
		}
		else
		{
			return new EqualityCondition(LearningObject :: PROPERTY_TYPE, $types);
		}
	}
}