<?php
require_once dirname(__FILE__).'/survey_user_table/survey_user_table.class.php';

class SurveyPublicationViewer extends SurveyPublisherComponent
{
	function run()
	{
		if (!$this->parent->is_allowed(EDIT_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		$trail = new BreadCrumbTrail();
		$trail->add(new BreadCrumb($this->parent->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ACTION => AssessmentTool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => $_GET[Tool :: PARAM_PUBLICATION_ID])), Translation :: get('ViewInvitedUsers')));
		$toolbar = $this->parent->get_toolbar();

		$wdm = WeblcmsDataManager :: get_instance();

		$pid = $_GET[Tool::PARAM_PUBLICATION_ID];
		$publication = $wdm->retrieve_learning_object_publication($pid);
		$survey = $publication->get_learning_object();

		$table = new SurveyUserTable($this, $this->get_user, $pid);

		$this->parent->display_header($trail);
		echo $toolbar->as_html();
		//echo '<br/><br/>'.Translation :: get('UsersInvitedToTakeSurvey').': <br/>';
		echo '<h4>' . $survey->get_title() . '</h4>';
		echo $table->as_html();
		$this->parent->display_footer();
	}
}
?>