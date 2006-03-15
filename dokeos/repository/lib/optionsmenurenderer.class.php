<?php
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * Renderer which can be used to create an array of options to use in a select
 * list. The options are displayed in a hierarchical way in the select list.
 */
class OptionsMenuRenderer extends HTML_Menu_ArrayRenderer
{
	/**
	 * Returns an array wich can be used as a list of options in a select-list
	 * of a form.
	 * @param string $key Which element of the menu item should be used as key
	 * value in the resulting options list. Defaults to 'id'
	 * @param array $exclude Which items should be excluded (based on the $key
	 * value in the menu items). The whole submenu of which the elements of the
	 * exclude array are the root elements will be excluded.
	 */
	public function toArray($key = 'id', $exclude = array())
	{
		$exclude = is_array($exclude) ? $exclude : array($exclude);
		$array = parent::toArray();
		$choices = array();
		while (list($index, $item) = each($array))
		{
			if(!in_array($item[$key],$exclude))
			{
				$prefix = '';
				if($item['level'] > 0)
				{
					$prefix = str_repeat('&nbsp;&nbsp;&nbsp;',$item['level']-1).'&mdash; ';
				}
				$choices[$item[$key]] = $prefix.$item['title'];
			}
			else
			{
				$exclude_level = $item['level'];
				$next_item = next($array);
				$next_item = next($array);
				while($next_item['level'] > $exclude_level)
				{
					$next_item = next($array);
				}
			}
		}
		return $choices;
	}
}
?>