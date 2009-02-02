<?php
require_once dirname(__FILE__).'/results_viewer.class.php';

class SurveyResultsViewer extends ResultsViewer
{
	function build()
	{
		$assessment = parent :: get_assessment();
		$assessment_id = $assessment->get_id();

		$this->addElement('html', '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">');
		$this->addElement('html', '<div class="title" style="font-size: 14px">');
		$this->addElement('html', Translation :: get('ViewSurveyResults').': '.$assessment->get_title());
		$this->addElement('html', '</div>');
		$this->addElement('html', '<div class="description">');
		$this->addElement('html', $assessment->get_finish_text());
		$this->addElement('html', '</div>');
		$this->addElement('html', '</div>');
	}
}
?>