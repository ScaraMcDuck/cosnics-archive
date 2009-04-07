<?php

class BreadcrumbTrail
{
	//
	private $breadcrumbtrail;

    function BreadcrumbTrail($include_main_index = true)
    {
    	$this->breadcrumbtrail = array();
    	if($include_main_index)
    	{
    		$this->add(new BreadCrumb($this->get_path(WEB_PATH).'index.php', $this->get_setting('site_name', 'admin')));
    	}
    }
    
    function add($breadcrumb)
    {
    	$this->breadcrumbtrail[] = $breadcrumb;
    }
    
    function remove($breadcrumb)
    {
    	// TODO: Write function to remove a specific element
    }
    
    function get_first()
    {
    	return $this->breadcrumbtrail[0];
    }
    
    function get_last()
    {
    	$breadcrumbtrail = $this->breadcrumbtrail;
    	$last_key = count($breadcrumbtrail) - 1;
    	return $breadcrumbtrail[$last_key];
    }    
    
    function truncate()
    {
    	$this->breadcrumbtrail = array();
    }
    
    function render()
    {
    	$breadcrumbtrail = $this->breadcrumbtrail;
    	$html = array();
    	
		if (is_array($breadcrumbtrail) && count($breadcrumbtrail) > 0)
		{
			foreach ($breadcrumbtrail as $breadcrumb)
			{
				$html[] = '<a href="'.$breadcrumb->get_url().'" target="_top">'.$breadcrumb->get_name().'</a>';
			}
		}
    	
    	return implode("\n".'&nbsp;&gt;&nbsp;', $html);
    }
    
    function size()
    {
    	return count($this->breadcrumbtrail);
    }
    
    function display()
    {
    	$html = $this->render();
    	echo $html;
    }
    
    function get_breadcrumbtrail()
    {
    	return $this->breadcrumbtrail;
    }
    
    function set_breadcrumbtrail($breadcrumbtrail)
    {
    	$this->breadcrumbtrail = $breadcrumbtrail;
    }
    
	function get_setting($variable, $application)
	{
		return PlatformSetting :: get($variable, $application);
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	
	function get_breadcrumbs()
	{
		return $this->breadcrumbtrail;
	}
	
	function merge($trail)
	{
		$this->breadcrumbtrail = array_merge($this->breadcrumbtrail, $trail->get_breadcrumbtrail()); 
	}
}
?>