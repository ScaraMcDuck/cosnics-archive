<?php

class DiagnoserCellRenderer
{
	function render_cell($default_property, $data)
	{
		return $data[$default_property]; 
	}
	
	function get_properties()
	{
		return array('', 
					 'Section', 
					 'Setting',
					 'Current', 
					 'Expected', 
					 'Comment');
	}
	
	function get_prefix()
	{
		return '';
	}
}
?>