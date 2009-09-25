<?php
/**
 * $Id: link_publication_list_renderer.class.php 16603 2008-10-23 10:09:53Z vanpouckesven $
 * Link tool - list renderer
 * @package application.weblcms.tool
 * @subpackage link
 */
require_once dirname(__FILE__).'/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';
class LinkDetailsRenderer extends ContentObjectPublicationDetailsRenderer
{
	function LinkDetailsRenderer ($browser)
	{
		parent :: __construct($browser);
	}

	function render_title($publication)
	{
		$url = $publication->get_content_object()->get_url();
		return '<a target="about:blank" href="'.htmlentities($url).'">'.parent :: render_title($publication).'</a>';
	}
}
?>