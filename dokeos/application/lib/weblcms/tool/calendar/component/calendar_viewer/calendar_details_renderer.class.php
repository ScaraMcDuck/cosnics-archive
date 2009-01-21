<?php
/**
 * $Id: link_publication_list_renderer.class.php 16603 2008-10-23 10:09:53Z vanpouckesven $
 * Link tool - list renderer
 * @package application.weblcms.tool
 * @subpackage link
 */
require_once dirname(__FILE__).'/../../../../browser/list_renderer/learning_object_publication_details_renderer.class.php';
class CalendarDetailsRenderer extends LearningObjectPublicationDetailsRenderer
{
	function CalendarDetailsRenderer ($browser)
	{
		parent :: __construct($browser);
	}

	function render_description($publication)
	{
		$event = $publication->get_learning_object();
		$html[] = '<em>';
		//TODO: date formatting
		$html[] = htmlentities(Translation :: get('From')).': '.date('r',$event->get_start_date());
		$html[] = '<br />';
		//TODO: date formatting
		$html[] = htmlentities(Translation :: get('To')).': '.date('r',$event->get_end_date());
		$html[] = '</em>';
		$html[] = '<br />';
		$html[] = $event->get_description();
		return implode("\n",$html);
	}
	
	function as_html()
	{
		return parent :: as_html();
	}
}
?>