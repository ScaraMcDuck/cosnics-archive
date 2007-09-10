<?php
require_once dirname(__FILE__).'/../../browser/learningobjectpublicationlistrenderer.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	private $url_template;
	
	function __construct($parent, $url_template)
	{
		parent :: __construct($parent);
		$this->url_template = $url_template;
	}
	
	function as_html()
	{
		$publications = $this->get_publications();
		$html = '<ul>';
		foreach ($publications as $publication)
		{
			$html .= $this->render_publication($publication);
		}
		$html .= '</ul>';
		return $html;
	}
	
	function render_publication($publication)
	{
		$lo = $publication->get_learning_object();
		return '<li>'
			. '<div class="learning-style-survey-profile-title">'
			. '<a href="'
			. htmlspecialchars(str_replace('__ID__', $lo->get_id(), $this->url_template))
			. '">' . htmlspecialchars($lo->get_title()) . '</a>'
			. '</div>'
			. $lo->get_description()
			. '</li>';
	}
}
?>