<?php
/**
 * $Id: link_publication_list_renderer.class.php 16603 2008-10-23 10:09:53Z vanpouckesven $
 * Link tool - list renderer
 * @package application.weblcms.tool
 * @subpackage link
 */
require_once dirname(__FILE__).'/../../../../browser/list_renderer/learning_object_publication_details_renderer.class.php';
class GeolocationDetailsRenderer extends LearningObjectPublicationDetailsRenderer
{
	function GeolocationDetailsRenderer ($browser)
	{
		parent :: __construct($browser);
	}

	function render_description($publication)
	{
		$lo = $publication->get_learning_object();

		$html = array();
		
		$html[] = $lo->get_description();
		
		$html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/google_maps.js');
		$html[] = '<div id="map_canvas" style="border: 1px solid black; height:500px"></div>';
		$html[] = '<script type="text/javascript">';
		$html[] = 'initialize();';
		$html[] = 'codeAddress(\'' . $lo->get_location() . '\', \'' . $lo->get_title() . '\');'; 
		$html[] = '</script>';
		
		return implode("\n", $html);
	}
	
	/*function render_title($publication)
	{
		$url = $publication->get_learning_object()->get_url();
		return '<a target="about:blank" href="'.htmlentities($url).'">'.parent :: render_title($publication).'</a>';
	}*/
}
?>