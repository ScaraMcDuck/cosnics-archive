<?php

class SurveyViewerWizardProcess extends HTML_QuickForm_Action
{
	private $parent;

	public function SurveyViewerWizardProcess($parent)
	{
		$this->parent = $parent;
	}

	function perform($page, $actionName)
	{
		foreach($_POST as $key => $value)
		{
			$value = Security :: remove_XSS($value);
			$split_key = split('_', $key);
			$question_id = $split_key[0];

			if(is_numeric($question_id))
			{
				$answer_index = $split_key[1];
				$values[$question_id][$answer_index] = $value;
			}
		}

		//$question_numbers = $_SESSION['questions'];

		$rdm = RepositoryDataManager :: get_instance();

		$questions_cloi = $rdm->retrieve_complex_learning_object_items(new EqualityCondition(
			ComplexLearningObjectItem :: PROPERTY_PARENT, $this->parent->get_survey()->get_id()));

		while($question_cloi = $questions_cloi->next_result())
		{	
			$answers = $values[$question_cloi->get_id()];
			$this->parent->get_parent()->save_answer($question_cloi->get_id(), serialize($answers));
		}
		
		echo '<div class="assessment">';
		echo '<div class="description">';
		echo $this->parent->get_survey()->get_finish_text();
		echo '</div></div>';
		
		echo '<a href="' . $this->parent->get_parent()->get_url(array('tool_action' => null, 'pid' => null)) . '">' . Translation :: get('GoBack') . '</a>';

	}
}
?>