<?php
require_once Path :: get_library_path().'/export/export.class.php';
require_once dirname(__FILE__).'/../../../../trackers/weblcms_assessment_attempts_tracker.class.php';

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
			//$user_assessment = $wdm->retrieve_user_assessment($uaid);
			$track = new WeblcmsAssessmentAttemptsTracker();
			$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $uaid);
			$uass = $track->retrieve_tracker_items($condition);
			$user_assessment = $uass[0];
			$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($user_assessment->get_assessment_id());
			$assessment = $publication->get_learning_object();
			$user = UserDataManager::get_instance()->retrieve_user($user_assessment->get_user_id());
		
			$this->addElement('html', 'Export results:');
			$this->addElement('html', '<br/><br/>Assessment: '.$assessment->get_title());
			$this->addElement('html', '<br/><br/>From user: '.$user->get_fullname());
		} 
		else if (isset($_GET[AssessmentTool :: PARAM_PUBLICATION_ID]))
		{
			$aid = $_GET[AssessmentTool :: PARAM_PUBLICATION_ID];
			//$assessment = $rdm->retrieve_learning_object($pid, 'assessment');
			$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($pid);
		
			$this->addElement('html', 'Export results:');
			$this->addElement('html', '<br/><br/>Assessment: '.$publication->get_learning_object()->get_title());
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