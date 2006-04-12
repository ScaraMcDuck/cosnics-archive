<?php
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';

class LinkBrowser extends LearningObjectPublicationBrowser
{
	function LinkBrowser($parent)
	{
		parent :: __construct($parent, 'link', intval($_GET['category']));
		$this->set_header(0, get_lang('Title'), false);
		$this->set_header(1, get_lang('Description'), false);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$dm = WebLCMSDataManager :: get_instance();
		$orderBy = null;
		$orderDir = null;
		$pubs = $dm->retrieve_learning_object_publications($this->get_course_id(), $this->get_category(), $this->get_user_id(), $this->get_groups(), $this->get_condition(), $orderBy, $orderDir);
		$data = array ();
		foreach ($pubs as $publication)
		{
			$lo = $publication->get_learning_object();
			$row = array ();
			$row[] = '<a href="'.$lo->get_url(array(), true).'">'.htmlentities($lo->get_title()).'</a>';
			$row[] = $lo->get_description();
			$data[] = $row;
		}
		return $data;
	}

	function get_publication_count()
	{
		$dm = WebLCMSDataManager :: get_instance();
		return $dm->count_learning_object_publications($this->get_course_id(), $this->get_category(), $this->get_user_id(), $this->get_groups(), $this->get_condition());
	}

	private function get_condition()
	{
		// TODO: Share sensible default condition with other tools.
		$time = time();
		$tool_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'link');
		$shown_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_HIDDEN, 0);
		$date_from_zero = new EqualityCondition(LearningObjectPublication :: PROPERTY_FROM_DATE, 0);
		$date_to_zero = new EqualityCondition(LearningObjectPublication :: PROPERTY_TO_DATE, 0);
		$date_from_passed = new InequalityCondition(LearningObjectPublication :: PROPERTY_FROM_DATE, InequalityCondition :: LESS_THAN, $time);
		$date_to_coming = new InequalityCondition(LearningObjectPublication :: PROPERTY_TO_DATE, InequalityCondition :: GREATER_THAN, $time);
		$date1 = new OrCondition($date_from_zero, $date_from_passed);
		$date2 = new OrCondition($date_to_zero, $date_to_coming);
		$date_cond = new AndCondition($date1, $date2);
		return new AndCondition($shown_cond, $date_cond, $tool_cond);
	}
}
?>