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

	function AssessmentViewerWizard($parent, $assessment)
	{
		parent :: HTML_QuickForm_Controller('AssessmentViewerWizard', true);
		
		$this->parent = $parent;
		$this->assessment = $assessment;

		$this->addpages($assessment);
		
		$this->addAction('process', new AssessmentViewerWizardProcess($this));
		$this->addAction('display', new AssessmentViewerWizardDisplay($this));
	}
	
	function addpages($assessment)
	{
		$questions = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment->get_id()));
		$this->addPage(new QuestionsAssessmentViewerWizardPage('page_1', $this, 1, $questions));
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
		return 1;
	}
	
}
?>