<?php
require_once dirname(__FILE__).'/results_viewer.class.php';

class AssignmentResultsViewer extends ResultsViewer
{
	function build()
	{
		$assessment = parent :: get_assessment();
		$assessment_id = $assessment->get_id();
		
		$this->addElement('html', '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">');
		$this->addElement('html', '<div class="title" style="font-size: 14px">');
		$this->addElement('html', Translation :: get('View assignment results').': '.$assessment->get_title());
		$this->addElement('html', '</div>');
		$this->addElement('html', '<div class="description">');
		$this->addElement('html', $assessment->get_description());
		$this->addElement('html', '</div>');
		$this->addElement('html', '</div>');
		
		$uaid = parent :: get_user_assessment()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$db = WeblcmsDataManager :: get_instance();
		
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, parent :: get_user_assessment()->get_assessment_id());
		$clo_questions = $dm->retrieve_complex_learning_object_items($condition);
		while($clo_question = $clo_questions->next_result())
		{
			$question = $dm->retrieve_learning_object($clo_question->get_ref(), 'question');
			$conditionQ = new EqualityCondition(UserQuestion :: PROPERTY_QUESTION_ID, $clo_question->get_ref());
			$conditionA = new EqualityCondition(UserQuestion :: PROPERTY_USER_ASSESSMENT_ID, $uaid);
			$condition = new AndCondition($conditionQ, $conditionA);
			$user_question = $db->retrieve_user_questions($condition)->next_result();
			$question_result = QuestionResult :: create_question_result($this, $question, $user_question, $this->get_edit_rights());
			$question_result->display_exercise();
		}
		if ($_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
		{
			$this->addElement('submit', 'submit', Translation :: get('Save feedback'));
		}
		//$this->addElement('html', $this->toHtml());
		$max_total_score = $assessment->get_maximum_score();
		$pct_score = round((parent :: get_user_assessment()->get_total_score() / $max_total_score) * 10000) / 100;
		$this->addElement('html', '<br/><h3>'.Translation :: get('Total score').': '.parent :: get_user_assessment()->get_total_score()."/".$max_total_score.' ('.$pct_score.'%)</h3>');
		
	}
}
?>