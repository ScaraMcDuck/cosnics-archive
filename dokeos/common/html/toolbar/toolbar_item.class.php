<?php
require_once Path :: get_library_path().'html/toolbar/toolbar.class.php';

class ToolbarItem
{
	const DISPLAY_ICON = 1;
	const DISPLAY_LABEL = 2;
	const DISPLAY_ICON_AND_LABEL = 3;
	
	private $label;
	private $display;
	private $image;
	private $href;
	private $confirmation;
	
    function ToolbarItem($label = null, $image = null, $href = null, $display = self :: DISPLAY_ICON_AND_LABEL, $confirmation = false)
    {
    	$this->label = $label;
    	$this->display = $display;
    	$this->image = $image;
    	$this->href = $href;
    	$this->confirmation = $confirmation;
    }
    
    function get_label()
    {
    	return $this->label;
    }
    
    function get_display()
    {
    	return $this->display;
    }
    
    function get_image()
    {
    	return $this->image;
    }
    
    function get_href()
    {
    	return $this->href;
    }
    
    function get_confirmation()
    {
    	return $this->confirmation;
    }
    
    function needs_confirmation()
    {
    	return $this->confirmation;
    }
    
    function as_html()
    {
		$label = ($this->get_label() ? htmlentities($this->get_label()) : null);
		if (!$this->get_display())
		{
			$this->display = self :: DISPLAY_ICON;
		}
		$display_label = ($this->display & self :: DISPLAY_LABEL) == self :: DISPLAY_LABEL && !empty($label);
		
		$button = '';
		if (($this->display & self :: DISPLAY_ICON) == self :: DISPLAY_ICON && isset ($this->image))
		{			  
			$button .= '<img src="'.htmlentities($this->image).'" alt="'.$label.'" title="'.$label.'"'. ($display_label ? ' class="labeled"' : '').'/>';
		}
		
		if ($display_label)
		{
			$button .= '<span>'.$label.'</span>';
		}
		
		if ($this->get_href())
		{
			$button = '<a href="'.htmlentities($this->href).'" title="'.$label.'"'. ($this->needs_confirmation() ? ' onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"' : '').'>'.$button.'</a>';
		}
		
		return $button;
    }
}
?>