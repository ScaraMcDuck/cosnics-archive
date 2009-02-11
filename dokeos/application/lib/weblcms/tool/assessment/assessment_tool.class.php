<?php
/**
 * $Id: announcementtool.class.php 9200 2006-09-04 13:40:47Z bmol $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage assessment
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/assessment_tool_component.class.php';
require_once Path :: get_application_path().'lib/weblcms/tool/tool.class.php';
/**
 * This tool allows a user to publish assessments in his or her course.
 */
class AssessmentTool extends Tool
{
	const ACTION_VIEW_ASSESSMENTS = 'view';
	const ACTION_VIEW_USER_ASSESSMENTS = 'view_user';
	const ACTION_TAKE_ASSESSMENT = 'take';
	const ACTION_VIEW_RESULTS = 'result';
	const ACTION_EXPORT_QTI = 'exportqti';
	const ACTION_IMPORT_QTI = 'importqti';
	const ACTION_SAVE_DOCUMENTS = 'save_documents';
	const ACTION_EXPORT_RESULTS = 'export_results';
	const ACTION_PUBLISH_SURVEY = 'publish_survey';
	const ACTION_VIEW = 'view';
	const ACTION_REPOVIEWER = 'repoview';
	const ACTION_DELETE_QUESTION_FEEDBACK = 'delete_qfeedback';
	const ACTION_EDIT_QUESTION_FEEDBACK = 'edit_qfeedback';
	
	const PARAM_USER_ASSESSMENT = 'uaid';
	const PARAM_QUESTION_ATTEMPT = 'qaid';
	const PARAM_ASSESSMENT = 'aid';
	const PARAM_ADD_FEEDBACK = 'feedback';
	const PARAM_ANONYMOUS = 'anonymous';
	const PARAM_INVITATION_ID = 'invitation_id';
	const PARAM_PUBLICATION_ACTION = 'publication_action';
	const PARAM_REPO_TYPES = 'types';
	const PARAM_ASSESSMENT_PAGE = 'assessment_page';
	/*
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component) return;
		
		switch($action) 
		{
			case self :: ACTION_PUBLISH:
				$component = AssessmentToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_ASSESSMENTS:
				$component = AssessmentToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_TAKE_ASSESSMENT:
				$component = AssessmentToolComponent :: factory('Tester', $this);
				break;
			case self :: ACTION_VIEW_RESULTS:
				$component = AssessmentToolComponent :: factory('ResultsViewer', $this);
				break;
			case self :: ACTION_EXPORT_QTI:
				$component = AssessmentToolComponent :: factory('QtiExport', $this);
				//$component->set_redirect_params(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_ASSESSMENTS));
				break;
			case self :: ACTION_IMPORT_QTI:
				$component = AssessmentToolComponent :: factory('QtiImport', $this);
				break;
			case self :: ACTION_SAVE_DOCUMENTS:
				$component = AssessmentToolComponent :: factory('DocumentSaver', $this);
				break;
			case self :: ACTION_EXPORT_RESULTS:
				$component = AssessmentToolComponent :: factory('ResultsExport', $this);
				break;
			case self :: ACTION_PUBLISH_SURVEY:
				$component = AssessmentToolComponent :: factory('SurveyPublisher', $this);
				break;
			case self :: ACTION_REPOVIEWER:
				$component = AssessmentToolComponent :: factory('Repoviewer', $this);
				break;
			case self :: ACTION_DELETE_QUESTION_FEEDBACK:
				$component = AssessmentToolComponent :: factory('QuestionFeedbackDeleter', $this);
				break;
			case self :: ACTION_EDIT_QUESTION_FEEDBACK:
				$component = AssessmentToolComponent :: factory('QuestionFeedbackEditor', $this);
				break;
			default:
				$component = AssessmentToolComponent :: factory('Viewer', $this);
				break;
		}
		
		$component->run();
	}
}
?>