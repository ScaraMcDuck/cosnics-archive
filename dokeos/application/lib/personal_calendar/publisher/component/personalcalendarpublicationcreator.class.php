<?php
/**
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personalcalendarpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../personalcalendardatamanager.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learningobjectform.class.php';

class PersonalCalendarPublicationcreator extends PersonalCalendarPublisherComponent
{
	public function as_html()
	{
		$default_lo = new AbstractLearningObject('calendar_event', $this->get_user_id());
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $default_lo, 'create', 'post', $this->get_url(array ('type' => 'calendar_event')));
		if ($form->validate())
		{
			$object = $form->create_learning_object();
			$dm = PersonalCalendarDatamanager::get_instance();
			$event = new PersonalCalendarEvent(0,$this->get_user_id(),$object);
			$event->create();
			$url = $this->get_url(array('publish'=>'0'));
			header('Location: '.$url);
			exit;
		}
		else
		{
			return $form->toHTML();
		}
	}
}
?>