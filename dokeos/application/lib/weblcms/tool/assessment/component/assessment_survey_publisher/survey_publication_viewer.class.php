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
		$toolbar = $this->parent->get_toolbar();
		
		$wdm = WeblcmsDataManager :: get_instance();
		$rdm = RepositoryDataManager :: get_instance();
		
		$pid = $_GET[Tool::PARAM_PUBLICATION_ID];
		$publication = $wdm->retrieve_learning_object_publication($pid);
		$survey = $publication->get_learning_object();
		
		$table = new SurveyUserTable($this, $this->get_user, $survey);
		
		$this->parent->display_header($trail);
		echo $toolbar->as_html();
		echo '<br/><br/>Users invited to take this survey: <br/>';
		echo $table->as_html();
		$this->parent->display_footer();
	}
}
?>