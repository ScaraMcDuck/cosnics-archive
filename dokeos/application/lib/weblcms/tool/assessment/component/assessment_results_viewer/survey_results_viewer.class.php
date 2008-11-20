<?php
require_once dirname(__FILE__).'/results_viewer.class.php';

class SurveyResultsViewer extends ResultsViewer
{
	function build()
	{
		$assessment = parent :: get_assessment();
		$assessment_id = $assessment->get_id();
		
		$this->addElement('html', '<div class="learning_object" style="background-image: url('. Theme :: get_common_img_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">');
		$this->addElement('html', '<div class="title" style="font-size: 14px">');
		$this->addElement('html', Translation :: get('View survey results').': '.$assessment->get_title());
		$this->addElement('html', '</div>');
		$this->addElement('html', '<div class="description">');
		$this->addElement('html', $assessment->get_description());
		$this->addElement('html', '</div>');
		$this->addElement('html', '</div>');
		
		$uaid = parent :: get_user_assessment()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$db = WeblcmsDataManager :: get_instance();
		
		$condition = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $uaid);
		$user_questions = $db->retrieve_user_questions($condition);
		while ($user_question = $user_questions->next_result())
		{
			$question_result = QuestionResult :: create_question_result($this, $user_question, $this->get_edit_rights());
			$question_result->display_survey();
		}
	}
}
?>