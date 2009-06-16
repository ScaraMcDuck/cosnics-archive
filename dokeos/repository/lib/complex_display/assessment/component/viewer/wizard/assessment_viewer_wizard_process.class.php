<?php

class AssessmentViewerWizardProcess extends HTML_QuickForm_Action
{
	private $parent;
	private $values;

	public function AssessmentViewerWizardProcess($parent)
	{
		$this->parent = $parent;
	}
	
	function perform($page, $actionName)
	{
		$this->values = $page->controller->exportValues();

		dump($this->values);
	}
}
?>