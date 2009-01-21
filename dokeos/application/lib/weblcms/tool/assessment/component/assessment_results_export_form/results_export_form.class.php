<?php
require_once Path :: get_library_path().'/export/export.class.php';

class AssessmentResultsExportForm extends FormValidator 
{
	function AssessmentResultsExportForm($url)
	{
		parent::__construct('assessment', 'post', $url);
		$this->initialize();
	}
	
	function initialize()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$rdm = RepositoryDataManager :: get_instance();
		
		if (isset($_GET[AssessmentTool :: PARAM_USER_ASSESSMENT]))
		{
			$uaid = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
			$user_assessment = $wdm->retrieve_user_assessment($uaid);
			$assessment = $rdm->retrieve_learning_object($user_assessment->get_assessment_id(), 'assessment');
			$user = UserDataManager::get_instance()->retrieve_user($user_assessment->get_user_id());
		
			$this->addElement('html', 'Export results:');
			$this->addElement('html', '<br/><br/>Assessment: '.$assessment->get_title());
			$this->addElement('html', '<br/><br/>From user: '.$user->get_fullname());
		} 
		else if (isset($_GET[AssessmentTool :: PARAM_ASSESSMENT]))
		{
			$aid = $_GET[AssessmentTool :: PARAM_ASSESSMENT];
			$assessment = $rdm->retrieve_learning_object($aid, 'assessment');
		
			$this->addElement('html', 'Export results:');
			$this->addElement('html', '<br/><br/>Assessment: '.$assessment->get_title());
		}
		
		$options = Export::get_supported_filetypes(array('ical'));
		$this->addElement('select', 'filetype', 'Export to filetype:', $options);
		//$this->addElement('submit', 'submit', 'Export results');
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Ok'), array('class' => 'positive'));
		//$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}
}
?>