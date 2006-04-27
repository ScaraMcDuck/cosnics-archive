<?php
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * Renderer which can be used to create an array of options to use in a select
 * list. The options are displayed in a hierarchical way in the select list.
 * @package repository
 */
class OptionsMenuRenderer extends HTML_Menu_ArrayRenderer
{
	/**
	 * Create a new OptionsMenuRenderer
	 * @param array $exclude Which items should be excluded (based on the $key
	 * value in the menu items). The whole submenu of which the elements of the
	 * exclude array are the root elements will be excluded.
	 */
	function OptionsMenuRenderer($exclude = array())
	{
		$exclude = is_array($exclude) ? $exclude : array($exclude);
		$this->exclude = $exclude;
	}
	/*
	 * Inherited
	 */
	function renderEntry($node, $level, $type)
    {
    	// If this node is in the exclude list, add all its child-nodes to the exclude list
    	if(in_array($node['id'],$this->exclude))
    	{
    		foreach($node['sub'] as $child_id => $child)
    		{
    			if(!in_array($child_id,$this->exclude))
    			{
	    			$this->exclude[] = $child_id;
    			}
    		}
    	}
    	// Else add this node to the array
    	else
    	{
        	unset($node['sub']);
        	$node['level'] = $level;
        	$node['type']  = $type;
        	$this->_menuAry[] = $node;
    	}
    }
	/**
	 * Returns an array wich can be used as a list of options in a select-list
	 * of a form.
	 * @param string $key Which element of the menu item should be used as key
	 * value in the resulting options list. Defaults to 'id'
	 */
	public function toArray($key = 'id')
	{
		$array = parent::toArray();
		$choices = array();
		foreach($array as $index => $item)
		{
			$prefix = '';
			if($item['level'] > 0)
			{
				$prefix = str_repeat('&nbsp;&nbsp;&nbsp;',$item['level']-1).'&mdash; ';
			}
			$choices[$item[$key]] = $prefix.$item['title'];
		}
		return $choices;
	}
}
?>