<?php
/**
 * @package application.weblcms.tool
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';

class ListLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	function as_html()
	{
		$publications = $this->get_publications();
		foreach ($publications as $index => $publication)
		{
			$first = ($index == 0);
			$last = ($index == count($publications) - 1);
			$html[] = $this->render_publication($publication, $first, $last);
		}
		return implode("\n", $html);
	}

	/**
	 * Renders a single publication.
	 * @param LearningObjectPublication $publication The publication.
	 * @param boolean $first True if the publication is the first in the list
	 *                       it is a part of.
	 * @param boolean $last True if the publication is the last in the list
	 *                      it is a part of.
	 * @return string The rendered HTML.
	 */
	function render_publication($publication, $first = false, $last = false)
	{
		$html = array ();
		$html[] = '<div class="learning_object">';
		$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$publication->get_learning_object()->get_type().'.gif" alt="'.$publication->get_learning_object()->get_type().'"/></div>';
		$html[] = '<div class="title'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_title($publication);
		$html[] = '</div>';
		$html[] = '<div class="description'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_description($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_info'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_publication_information($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_actions">';
		$html[] = $this->render_publication_actions($publication,$first,$last);
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}
?>