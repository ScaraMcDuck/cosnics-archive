<?php
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/calendarlistrenderer.class.php';

class CalendarBrowser extends LearningObjectPublicationBrowser
{
	private $publications;
	function CalendarBrowser($parent, $types)
	{
		parent :: __construct($parent, 'calendar');
		$renderer = new CalendarListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}

	function get_publications($from, $count, $column, $direction)
	{
		if( isset($this->publications))
		{
			return $this->publications;
		}
		$datamanager = WebLCMSDataManager :: get_instance();
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'calendar');
		$this->publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $this->publications;
	}

	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>