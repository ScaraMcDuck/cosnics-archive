<?php
require_once dirname(__FILE__).'/assessment_tester_form.class.php';

class AssessmentTesterDisplay 
{
	private $assessment;
	private $parent;
	
	private $form;
	private $qpp;
	private $questions;
	
	function AssessmentTesterDisplay($parent, $assessment)
	{
		$this->parent = $parent;
		$this->assessment = $assessment;
	}

	function as_html()
	{
		if ($qpp > 0)
		{
			$html[] = '<h3>This is page: '.$page.'/'.$questions/$qpp.'</h3>';
		}
			
		$html[] = $this->form->toHtml();
		return implode('', $html);
	}
	
	function build_form($url, $page) 
	{
		$_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE] = $page;
		$qpp = $this->assessment->get_questions_per_page();
		$tester_form = new AssessmentTesterForm($this->assessment, $url, $page);
		if ($qpp > 0)
		{
			$questions = 0;
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->assessment->get_id());
			$clo_questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items($condition);
			while ($clo_question = $clo_questions->next_result())
				$questions++;
			// subtract 1, last page may be less questions than questions per page
			if (($page - 1) * $qpp >= $questions)
			{
				//test done, redirect to scores
				$_SESSION[AssessmentTool :: PARAM_ASSESSMENT_PAGE] = null;
				$answers = $_SESSION['formvalues'];
				$_SESSION['formvalues'] = null;
				$this->parent->redirect_to_score_calculator($answers);
			}
		}
		
		if (!$tester_form->validate()) 
		{
			$this->set_formvalues($tester_form);
			$this->form = $tester_form;
			$this->qpp = $qpp;
			$this->questions = $questions;
			return 'form';
		} 
		else
		{	
			if ($tester_form != null)
				$values = $tester_form->exportValues();
						
			if (isset($values['submit']))
			{
				if ($qpp == 0) 
				{
					return $this->parent->redirect_to_score_calculator($values);
				}
				else
				{
					$old_values = $_SESSION['formvalues'];
					foreach ($old_values as $key => $value)
					{
						$new_values[$key] = $value;
					}
					foreach ($values as $key => $value)
					{
						$new_values[$key] = $value;
					}
					$_SESSION['formvalues'] = $new_values;
					$_POST = null;
					$this->build_form($url, $page + 1);
					return 'form';
				}
			}
			else
			{
				$old_values = $_SESSION['formvalues'];
				foreach ($old_values as $key => $value)
				{
					$new_values[$key] = $value;
				}
				foreach ($values as $key => $value)
				{
					$new_values[$key] = $value;
				}
				$_SESSION['formvalues'] = $new_values;
				$this->parent->redirect_to_repoviewer();
			}
		}
	}
	
	function set_formvalues($tester_form)
	{
		$formvalues = $_SESSION['formvalues'];
		if ($formvalues != null)
		{
			foreach ($formvalues as $id => $value)
			{
				$parts = split('_', $id);
				if ($parts[0] == 'repoviewer')
				{
					$control_id = $parts[1].'_'.$parts[2];
					
					if (isset($_GET['object']))
					{
						$objects = $_GET['object'];
						if (is_array($objects))
							$object = $objects[0];
						else
							$object = $objects;
							
						$formvalues[$control_id] = $objects;
						$doc = RepositoryDataManager :: get_instance()->retrieve_learning_object($objects);
						$formvalues[$control_id.'_name'] = $doc->get_title();
					}
				}
			}		
			$tester_form->setDefaults($formvalues);
		}
	}
}
?>