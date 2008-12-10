<?php
require_once Path::get_library_path().'html/formvalidator/FormValidator.class.php';

class SurveyPublicationForm extends FormValidator
{
	
	function SurveyPublicationForm($parent, $survey, $url = '')
	{
		parent :: __construct('assessment', 'post', $url);
		
		$this->addElement('html', Translation :: get('PublishSurvey'));
		$course = $parent->get_course();
		$user_relations = WeblcmsDataManager :: get_instance()->retrieve_course_users($course);
		while ($user_relation = $user_relations->next_result())
		{
			$user = $user_relation->get_user_object();
			$course_users[$user->get_id()] = $user->get_fullname();
		}

		$this->addElement('advmultiselect', 'course_users', Translation :: get('SelectUsers'), $course_users, 'style="width: 250px;"');
		$this->addElement('textarea', 'additional_users', Translation :: get('AdditionalUsers'), array ('cols' => 50, 'rows' => 2));
		$this->addElement('text', 'email_header', Translation :: get('EmailTitle'), array('size' => 80));
		$this->addRule('email_header', Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('html_editor', 'email_content', Translation :: get('EmailContent'));
		$this->addRule('email_content', Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addElement('html', Translation :: get('PublishSurveySendMailInfo'));		
		$this->addElement('checkbox', 'resend', Translation :: get('ResendEmail'));
		$this->addElement('html', Translation :: get('PublishSurveyResendMailInfo'));
		$this->addElement('submit', 'submit', Translation :: get('SendMail'));
	}
}
?>