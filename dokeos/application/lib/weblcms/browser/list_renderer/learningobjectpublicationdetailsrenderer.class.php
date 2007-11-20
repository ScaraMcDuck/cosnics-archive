<?php
/**
 * @package application.weblcms
 * @subpackage browser.detailrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
/**
 * Renderer to display all details of learning object publication
 */
class LearningObjectPublicationDetailsRenderer extends LearningObjectPublicationListRenderer
{
	/**
	 * Returns the HTML output of this renderer.
	 * @return string The HTML output
	 */
	function as_html()
	{
		$publication_id = $this->browser->get_publication_id();
		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_learning_object_publication($publication_id);
		$html[] = $this->render_publication($publication);
		$html[] = $this->render_publication_feedback($publication);
		return implode("\n", $html);
	}

	/**
	 * Renders a single publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @return string The rendered HTML.
	 */
	function render_publication($publication,$first= false, $last= false)
	{
		$html = array ();
		$last_visit_date = $this->browser->get_last_visit_date();
		$icon_suffix = '';
		if($publication->is_hidden())
		{
			$icon_suffix = '_na';
		}
		elseif( $publication->get_publication_date() >= $last_visit_date)
		{
			$icon_suffix = '_new';
		}
		$html[] = '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/'.$publication->get_learning_object()->get_icon_name().$icon_suffix.'.gif);">';
		$html[] = '<div class="title'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_title($publication);
		$html[] = '</div>';
		$html[] = '<div class="description'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_description($publication);
		$html[] = $this->render_attachments($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_info'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_publication_information($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_actions">';
		//$html[] = $this->render_publication_actions($publication,$first,$last);
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	/**
	 * Renders a list of all LearningObjects of the type 'feedback' attached to this LearningObject
	 * @param  LearningObjectPublication $publication The publication.
	 * @return string The rendered HTLM. 
	 */
	function render_publication_feedback($publication){
		$html = array();
		$html[] = $publication->get_feedback();
		return implode("\n", $html);
	}
}
?>