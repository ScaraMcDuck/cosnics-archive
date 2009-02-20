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
			$track = new WeblcmsAssessmentAttemptsTracker();
			$condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $uaid);
			$uass = $track->retrieve_tracker_items($condition);
			$user_assessment = $uass[0];
			$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($user_assessment->get_assessment_id());
			$assessment = $publication->get_learning_object();
			$user = UserDataManager::get_instance()->retrieve_user($user_assessment->get_user_id());
		
			//$this->addElement('html', '<h3>Assessment: '.$assessment->get_title().'</h3><br/>');
			$this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');
			
			$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/assessment.png);">';
			$html[] = '<div class="title">';
			$html[] = $assessment->get_title();
			$html[] = '</div>';
			$html[] = $assessment->get_description();
			$html[] = '</div><br />';
			
			$this->addElement('html', implode("\n", $html));
		} 
		else if (isset($_GET[AssessmentTool :: PARAM_PUBLICATION_ID]))
		{
			$aid = $_GET[AssessmentTool :: PARAM_PUBLICATION_ID];
			$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($aid);
		
			$this->addElement('html', '<h3>Assessment: '.$publication->get_learning_object()->get_title().'</h3><br/>');
			$this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');
		}
		
		$options = Export::get_supported_filetypes(array('ical'));
		$this->addElement('select', 'filetype', 'Export to filetype:', $options);
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Export'), array('class' => 'positive export'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}
}
?>