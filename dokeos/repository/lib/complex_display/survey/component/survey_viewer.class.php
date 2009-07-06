<?php

require_once dirname(__FILE__).'/../survey_display.class.php';
require_once dirname(__FILE__).'/../survey_display_component.class.php';
require_once dirname(__FILE__).'/viewer/survey_viewer_wizard.class.php';

class SurveyDisplaySurveyViewerComponent extends SurveyDisplayComponent 
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$wizard = new SurveyViewerWizard($this, $this->get_root_lo());
		$wizard->run();
	}	
}
?>