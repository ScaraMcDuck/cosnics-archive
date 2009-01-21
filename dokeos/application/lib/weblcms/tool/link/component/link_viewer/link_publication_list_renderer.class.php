<?php
/**
 * $Id$
 * Link tool - list renderer
 * @package application.weblcms.tool
 * @subpackage link
 */
require_once dirname(__FILE__).'/../../../../browser/list_renderer/list_learning_object_publication_list_renderer.class.php';
class LinkPublicationListRenderer extends ListLearningObjectPublicationListRenderer
{
	function LinkPublicationListRenderer ($browser)
	{
		parent :: __construct($browser);
	}

	function render_title($publication)
	{
		$url = $publication->get_learning_object()->get_url();
		return '<a target="about:blank" href="'.htmlentities($url).'">'.parent :: render_title($publication).'</a>';
	}
}
?>