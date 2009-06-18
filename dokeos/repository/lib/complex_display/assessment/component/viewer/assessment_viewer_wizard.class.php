<?php
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__) . '/wizard/assessment_viewer_wizard_display.class.php';
require_once dirname(__FILE__) . '/wizard/assessment_viewer_wizard_process.class.php';
require_once dirname(__FILE__) . '/wizard/assessment_viewer_wizard_page.class.php';
require_once dirname(__FILE__) . '/wizard/questions_assessment_viewer_wizard_page.class.php';


class AssessmentViewerWizard extends HTML_QuickForm_Controller
{

	private $parent;
	private $assessment;
	private $total_pages;

	function AssessmentViewerWizard($parent, $assessment)
	{
		parent :: HTML_QuickForm_Controller('AssessmentViewerWizard', true);
		
		$this->parent = $parent;
		$this->assessment = $assessment;

		$this->addpages();
		
		$this->addAction('process', new AssessmentViewerWizardProcess($this));
		$this->addAction('display', new AssessmentViewerWizardDisplay($this));
	}
	
	function addpages()
	{
		$assessment = $this->assessment;
		$total_questions = RepositoryDataManager :: get_instance()->count_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment->get_id()));
		$questions_per_page = $assessment->get_questions_per_page();
		
		if($questions_per_page == 0)
		{
			$this->total_pages = 1;
		}
		else
		{
			$this->total_pages = ceil($total_questions / $questions_per_page);
		}
		
		for($i = 1; $i <= $this->total_pages; $i++ )
			$this->addPage(new QuestionsAssessmentViewerWizardPage('question_page_' . $i, $this, $i));
			
		if(!isset($_SESSION['questions']))
			$_SESSION['questions'] = 'all';
			
	}
	
	function get_questions($page_number)
	{
		$assessment = $this->assessment;
		$questions_per_page = $this->assessment->get_questions_per_page();
		$start = (($page_number - 1) * $questions_per_page);
		$stop = $questions_per_page;
		
		$questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment->get_id()), array(), array(), $start, $stop);
		return $questions;
	}
	
	function get_parent()
	{
		return $this->parent;
	}
	
	function get_assessment()
	{
		return $this->assessment;
	}
	
	function get_total_pages()
	{
		return $this->total_pages;
	}
	
}
?>