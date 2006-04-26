<?php
require_once dirname(__FILE__).'/../../browser/list_renderer/listlearningobjectpublicationlistrenderer.class.php';
class LinkPublicationListRenderer extends ListLearningObjectPublicationListRenderer
{
	function LinkPublicationListRenderer ($browser)
	{
		parent :: __construct($browser);
	}

	function render_title($publication)
	{
		$url = $publication->get_learning_object()->get_url();
		return '<a href="'.htmlentities($url).'">'.parent :: render_title($publication).'</a>';
	}
}
?>