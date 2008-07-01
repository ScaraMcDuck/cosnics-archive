<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../search_portal_block.class.php';

/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class SearchPortalExtra extends SearchPortalBlock
{
	function run()
	{
		return $this->as_html();
	}
	
	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		
		$html[] = $this->display_header();
		$html[] = 'Search Portal test block ...';
		$html[] = $this->display_footer();
		
		return implode("\n", $html);
	}
}
?>