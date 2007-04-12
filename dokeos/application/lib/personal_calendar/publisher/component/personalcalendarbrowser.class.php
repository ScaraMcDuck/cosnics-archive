<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../personalcalendarpublishercomponent.class.php';
require_once dirname(__FILE__).'/publishertable/publishertable.class.php';
/**
 * Browser component of the personal calendar event publisher. This component
 * can be used to browse the available calendar events in the repository and
 * select events to publish in the personal calendar.
 */
class PersonalCalendarBrowser extends PersonalCalendarPublisherComponent
{
	/**
	* Gets a HTML representation of this component.
	* @return string
	* @todo Implmentation
	*/
	public function as_html()
	{
		if(isset($_GET['action']) && $_GET['action'] == 'publish')
		{
			$dm = RepositoryDataManager::get_instance();
			$learning_object = $dm->retrieve_learning_object($_GET['learning_object_id'],'calendar_event');
			$event = new PersonalCalendarEvent(0,$this->get_user_id(),$learning_object);
			$event->create();
			header('Location: '.$this->get_url(array('publish'=>0)));
		}
		$publish_url_format = $this->get_url(array (PersonalCalendarPublisher :: PARAM_ACTION => 'browser','action' => 'publish', 'learning_object_id' => '__ID__'),false);
		$publish_url_format = str_replace('__ID__', '%d', $publish_url_format);
		$table = new PublisherTable($this->get_user_id(), 'calendar_event', $this->get_query(), $publish_url_format, '');
		return $table->as_html();
	}
	protected function get_query()
	{
		return null;
	}
}
?>