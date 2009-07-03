<?php

class LocalPackageBrowserCellRenderer
{
	function render_cell($default_property, $data)
	{
		$data = $data[$default_property];
		
		if(is_null($data))
		{
			$data = '-';
		}

		return $data;
	}
	
	function get_properties()
	{
		$properties = array();
		$properties[] = 'Name';
		$properties[] = '';
		return $properties;
	}
	
	function get_prefix()
	{
		return '';
	}
}
?>