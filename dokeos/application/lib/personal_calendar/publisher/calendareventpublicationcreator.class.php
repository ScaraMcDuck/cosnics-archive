<?php
/**
 * $Id: personalcalendarpublicationcreator.class.php 14492 2008-03-04 10:40:47Z Scara84 $
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../calendareventpublishercomponent.class.php';
require_once dirname(__FILE__).'/../personalcalendardatamanager.class.php';
require_once Path :: get_repository_path(). 'lib/learningobjectform.class.php';
/**
 * Creation component of the personal calendar event publisher. This component
 * can be used to create a new calendar event in the repository and at the same
 * time publish it in the personal calendar.
 */
class CalendarEventPublicationcreator extends CalendarEventPublisherComponent
{
	/**
	 * Gets a HTML representation of this component.
	 * @return string
	 */
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