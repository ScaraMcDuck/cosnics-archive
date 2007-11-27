<?php
/**
 * Feedback list renderer
 * @package application.weblcms.list_renderer
 */
require_once dirname(__FILE__).'/listlearningobjectpublicationlistrenderer.class.php';
/**
 * Renderer to display a list of feedback publications.
 */
class ListPublicationFeedbackListRenderer extends ListLearningObjectPublicationListRenderer
{
	private $feedback;
	function ListPublicationFeedbackListRenderer($browser,$feedback)
	{
		parent::ListLearningObjectPublicationListRenderer($browser);
		$this->feedback = $feedback;
	}
	function get_publications()
	{
		return $this->feedback;
	}
	
	function render_up_action($publication, $first = false)
	{
		return '';
	}

	function render_down_action($publication, $last = false)
	{
		return '';
	}
	
	function render_visibility_action($publication)
	{
		return '';
	}

	function render_edit_action($publication)
	{
		return '';
	}

	function render_delete_action($publication)
	{
		return '';
	}
	
	function render_feedback_action($publication){
		return '';	
	}
	
	function render_move_to_category_action($publication)
	{
		return '';
	}
}
?>