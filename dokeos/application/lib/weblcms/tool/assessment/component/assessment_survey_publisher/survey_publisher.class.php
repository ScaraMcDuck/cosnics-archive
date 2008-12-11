<?php
require_once dirname(__FILE__).'/survey_publication_form.class.php';
require_once dirname(__FILE__).'/../../survey_invitation.class.php';

class SurveyPublisher extends SurveyPublisherComponent
{
	private $survey_invitation;
	
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
		
		$form = new SurveyPublicationForm($this->parent, $survey, $this->parent->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ID => $pid)));
		
		if ($form->validate())
		{
			$values = $form->exportValues();
			$this->parse_values($values, $survey);
			$this->parent->redirect(null, null, false, array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH_SURVEY, AssessmentTool :: PARAM_PUBLICATION_ACTION => AssessmentTool :: ACTION_VIEW, Tool :: PARAM_PUBLICATION_ID => $_GET[Tool :: PARAM_PUBLICATION_ID]));
		}
		else
		{
			$this->parent->display_header($trail);
			echo $toolbar->as_html();
			echo $form->toHtml();
			$this->parent->display_footer();
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
			if ($address != '')
				$mail_users[$address] = 0;
		}
		
		$email_title = $values['email_title'];
		$email_body = $values['email_content'];
		$resend = $values['resend'];
		
		foreach ($mail_users as $mail => $user)
		{
			if ($this->create_invitation($user, $mail, $survey, $resend))
			{
				$this->send_mail($this->survey_invitation, $email_title, $email_body);
			}
		}
	}
		
	function create_invitation($user, $mail, $survey, $resend)
	{
		//echo $user.$mail.$survey.$resend;
		//check for existing invitations for this user/mail
		if ($user == 0)
			$conditionu = new EqualityCondition(SurveyInvitation::PROPERTY_EMAIL, $mail);
		else
			$conditionu = new EqualityCondition(SurveyInvitation::PROPERTY_USER_ID, $user);
		$conditions = new EqualityCondition(SurveyInvitation::PROPERTY_SURVEY_ID, $survey->get_id());
		$condition = new AndCondition(array($conditionu, $conditions));
		//print_r($condition);
		$invitations = WeblcmsDataManager :: get_instance()->retrieve_survey_invitations($condition);
		$invitation = $invitations->next_result();
		//check if an existing invitation must be returned or a new one
		if (!$resend)
		{
			if ($invitation != null)
				return false;
			else
				return $this->create_new_invitation($user, $mail, $survey);
		}
		else
		{
			if ($invitation != null)
			{
				$this->survey_invitation = $invitation;
				//$this->survey_invitation->set_valid(true);
				//return WeblcmsDataManager :: get_instance()->update_survey_invitation($this->survey_invitation);
				return true;
			}
			else
				return $this->create_new_invitation($user, $mail, $survey);
		}
	}
	
	function create_new_invitation($user, $mail, $survey)
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
		$url = $this->parent->get_url(array(Tool::PARAM_ACTION => AssessmentTool::ACTION_TAKE_ASSESSMENT, AssessmentTool::PARAM_INVITATION_ID => $survey_invitation->get_invitation_code()));
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
		
		//echo $email.$title.$fullbody.'<br/>';
		$mail = Mail :: factory($title, $fullbody, $email, $webmaster_email);
		// Check whether it was sent successfully
		if ($mail->send() === FALSE) {
		
		}
		
	}
}
?>