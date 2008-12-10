<?php
require_once dirname(__FILE__).'/assessment_survey_publisher/survey_publication_form.class.php';
require_once dirname(__FILE__).'/../survey_invitation.class.php';

class AssessmentToolSurveyPublisherComponent extends AssessmentToolComponent
{
	private $survey_invitation;
	
	function run()
	{
		if (!$this->is_allowed(EDIT_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		$trail = new BreadCrumbTrail();
		
		$wdm = WeblcmsDataManager :: get_instance();
		$rdm = RepositoryDataManager :: get_instance();
		
		$pid = $_GET[Tool::PARAM_PUBLICATION_ID];
		$publication = $wdm->retrieve_learning_object_publication($pid);
		$survey = $publication->get_learning_object();
		
		$form = new SurveyPublicationForm($this, $survey, $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ID => $pid)));
		
		if ($form->validate())
		{
			$values = $form->exportValues();
			//print_r($values);
			$this->parse_values($values, $survey);
		}
		else
		{
			$this->display_header($trail);
			echo $form->toHtml();
			$this->display_footer();
		}
	}
	
	function parse_values($values, $survey)
	{
		$users = $values['course_users'];
		foreach ($users as $key => $user)
		{
			$lo_user = UserDataManager :: get_instance()->retrieve_user($user);
			$mail_users[$lo_user->get_email()] = $lo_user->get_id();
		}
		$addresses = split(';', $values['additional_users']);
		foreach ($addresses as $address)
		{
			$mail_users[$address] = 0;
		}
		
		$email_title = $values['email_title'];
		$email_body = $values['email_content'];
		
		foreach ($mail_users as $mail => $user)
		{
			if ($this->create_invitation($user, $mail, $survey))
			{
				$this->send_mail($this->survey_invitation, $email_title, $email_body);
			}
		}
	}
		
	function create_invitation($user, $mail, $survey)
	{
		$survey_invitation = new SurveyInvitation();
		$survey_invitation->set_id(WeblcmsDataManager :: get_instance()->get_next_survey_invitation_id());
		$survey_invitation->set_valid(true);
		if ($user == 0)
			$survey_invitation->set_email($mail);
		
		$survey_invitation->set_user_id($user);
		$survey_invitation->set_invitation_code(md5(microtime()));
		$survey_invitation->set_survey_id($survey->get_id());
		
		$this->survey_invitation = $survey_invitation;
		return WeblcmsDataManager :: get_instance()->create_survey_invitation($this->survey_invitation);
	}
	
	function send_mail($survey_invitation, $title, $body)
	{
		//echo '<br/>send invitation: <br/>'.$title.' '.$body.'<br/>';
		//print_r($survey_invitation);
		$url = $this->get_url(array(Tool::PARAM_ACTION => AssessmentTool::ACTION_TAKE_ASSESSMENT, AssessmentTool::PARAM_INVITATION_ID => $survey_invitation->get_invitation_code()));
		$text = '<br/><br/><a href='.$url.'>'.Translation::get('ClickToTakeSurvey').'</a>';
		$text .= '<br/><br/>'.Translation :: get('OrCopyAndPasteThisText').':';
		$text .= '<br/><a href='.$url.'>'.$url.'</a>';
		$fullbody = $body.$text;
		
		$email = $survey_invitation->get_email();
		if ($email == null)
		{
			$user = UserDataManager :: get_instance()->retrieve_user($survey_invitation->get_user_id());
			$email = $user->get_email();
		}
		
		echo $email.$title.$fullbody.'<br/>';
		mail($email, $title, $fullbody);
		
	}
}
?>