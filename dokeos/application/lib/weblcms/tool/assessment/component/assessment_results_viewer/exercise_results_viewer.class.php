<?php
require_once dirname(__FILE__).'/results_viewer.class.php';
require_once dirname(__FILE__).'/question_result.class.php';

class ExerciseResultsViewer extends ResultsViewer
{
	
	function build()
	{
		$assessment = parent :: get_assessment();
		$assessment_id = $assessment->get_id();

//		$this->addElement('html', '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">');
//		$this->addElement('html', '<div class="title" style="font-size: 14px">');
		$this->addElement('html', '<h3>' . Translation :: get('ViewExerciseResu lts').': '.$assessment->get_title() . '</h3>');
//		$this->addElement('html', '</div>');
//		$this->addElement('html', '<div class="description">');
		$this->addElement('html', $assessment->get_description());
//		$this->addElement('html', '</div>');
//		$this->addElement('html', '</div>');
		$count = 1;
		$uaid = parent :: get_user_assessment()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$db = WeblcmsDataManager :: get_instance();
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication(parent :: get_user_assessment()->get_assessment_id());
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $publication->get_learning_object()->get_id());
		$clo_questions = $dm->retrieve_complex_learning_object_items($condition);
		while($clo_question = $clo_questions->next_result())
		{
			$question = $dm->retrieve_learning_object($clo_question->get_ref());
			$track = new WeblcmsQuestionAttemptsTracker();
			$condition_ass = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->get_user_assessment()->get_id());
			$condition_question = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $question->get_id());
			$condition = new AndCondition(array($condition_ass, $condition_question));
			$q_results = $track->retrieve_tracker_items($condition);
			$question_result = QuestionResult :: create_question_result($this, $question, $q_results, $this->get_edit_rights(), $count);
			$count++;
			$question_result->display_exercise();
		}
		if ($_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
		{
			$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
			$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

			$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		}
		$max_total_score = $assessment->get_maximum_score();
		$pct_score = round((parent :: get_user_assessment()->get_total_score() / $max_total_score) * 10000) / 100;
		$this->addElement('html', '<h3>'.Translation :: get('TotalScore').': '.parent :: get_user_assessment()->get_total_score()."/".$max_total_score.' ('.$pct_score.'%)</h3><br />');
		
	}
}
?>