<?php
/**
 * $Id: wikipublicationlistrenderer.class.php 9206 2006-09-05 10:12:59Z bmol $
 * Chat tool - list renderer
 * @package application.weblcms.tool
 * @subpackage chat
 */
require_once dirname(__FILE__).'/../../../../browser/list_renderer/list_learning_object_publication_list_renderer.class.php';

class ChatPublicationListRenderer extends ListLearningObjectPublicationListRenderer
{
	function render_description($publication)
	{
		$chatbox =  $publication->get_learning_object();
		$html[] = $chatbox->get_description();
		$display = LearningObjectDisplay::factory($chatbox);
		$html[] = $display->get_chatbox_html();
		return implode("\n",$html);
	}
}
?>