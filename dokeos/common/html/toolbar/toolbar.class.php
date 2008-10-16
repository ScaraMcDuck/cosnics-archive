<?php
require_once Path :: get_library_path().'html/toolbar/toolbar_item.class.php';

class Toolbar
{
	private $items = array();
	private $class_names = array();
	private $css = null;

    function Toolbar($class_names = array(), $css = null)
    {
    	$this->class_names = $class_names;
    	$this->css = $css;
    }
    
    function set_items($items)
    {
    	$this->items = $items;
    }
    
    function add_item($item)
    {
    	$this->items[] = $item;
    }
    
    function as_html()
    {
    	$toolbar_data = $this->items;
    	$class_names = $this->class_names;
    	$css = $this->css;
    	
		if (!is_array($class_names))
		{
			$class_names = array ($class_names);
		}
		$class_names[] = 'toolbar';
		
		$html = array ();
		$html[] = '<ul class="' . implode(' ', $class_names) . '"' . (isset($css) ? ' style="'.$css.'"' : '') . '>';
		
		foreach ($toolbar_data as $index => $toolbar_item)
		{
			$classes = array();
			
			if ($index == 0)
			{
				$classes[] = 'first';
			}

			if ($index == count($toolbar_data) - 1)
			{
				$classes[] = 'last';
			}
			
			$html[] = '<li' . (count($classes) ? ' class="' . implode(' ', $classes) . '"' : '') . '>' . $toolbar_item->as_html() . '</li>';
		}
		
		$html[] = '</ul>';
		return implode($html);
    }
}
?>