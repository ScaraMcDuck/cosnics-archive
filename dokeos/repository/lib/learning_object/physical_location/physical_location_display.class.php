<?php
/**
 * $Id: physical_location_display.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage physical_location
 */
/**
 * This class can be used to display physical_locations
 */
class PhysicalLocationDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$html = parent :: get_full_html();
		$object = $this->get_learning_object();
		$replace = array();
		
		$replace[] = '<div class="learning_object">';
		$replace[] = '<div class="title">';
		$replace[] = $object->get_location();
		$replace[] = '</div>';
		$replace[] = '<div class="description">';
		$replace[] = $this->get_javascript($object);
		$replace[] = '</div>';
		$replace[] = '</div>';
		
		return str_replace(self::DESCRIPTION_MARKER, implode("\n", $replace), $html);
	}
	
	function get_short_html()
	{
		$object = $this->get_learning_object();
        return '<span class="learning_object">' . htmlentities($object->get_title()) . ' - ' . htmlentities($object->get_location()) . '</span>';
	}
	
	function get_javascript($object)
	{
		$html = array();
		
		$html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/google_maps.js');
		$html[] = '<div id="map_canvas" style="width:100%; border: 1px solid black; height:500px"></div>';
		$html[] = '<script type="text/javascript">';
		$html[] = 'initialize();';
		$html[] = 'codeAddress(\'' . $object->get_location() . '\', \'' . $object->get_title() . '\');'; 
		$html[] = '</script>';
		
		
		return implode("\n", $html);
	}
}
?>