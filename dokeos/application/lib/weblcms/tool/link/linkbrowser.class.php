<?php
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';

class LinkBrowser extends LearningObjectPublicationBrowser
{
	function LinkBrowser()
	{
		parent :: __construct('link', api_get_course_id(), api_get_user_id());
		$this->set_column_titles(get_lang('Title'), get_lang('Description'));
	}

	function get_table_data($from, $number_of_items, $column, $direction)
	{
		$dm = WebLCMSDataManager::get_instance();
		$orderBy = null;
		$orderDir = null;
		$pubs = $dm->retrieve_learning_object_publications($this->get_condition(), $orderBy, $orderDir);
		$data = array();
		foreach ($pubs as $publication)
		{
			$lo = $publication->get_learning_object();
			$row = array();
			$row[] = '<a href="' . $lo->get_url() . '">' . htmlentities($lo->get_title()) . '</a>';
			$row[] = $lo->get_description();
			$data[] = $row;
		}
		return $data;
	}

	function get_table_row_count()
	{
		$dm = WebLCMSDataManager::get_instance();
		return $dm->count_learning_object_publications($this->get_condition());
	}
	
	private function get_condition()
	{
		// TODO: Share sensible default condition with other tools.
		$time = time();
		$shown_cond = new ExactMatchCondition('hidden', 0);
		$date_from_zero = new ExactMatchCondition('from_date', 0);
		$date_to_zero = new ExactMatchCondition('to_date', 0);
		$date_from_passed = new InequalityCondition('from_date', InequalityCondition :: LESS_THAN, $time);
		$date_to_coming = new InequalityCondition('to_date', InequalityCondition :: GREATER_THAN, $time);
		$date1 = new OrCondition($date_from_zero, $date_from_passed);
		$date2 = new OrCondition($date_to_zero, $date_to_coming);
		$date_cond = new AndCondition($date1, $date2);
		$user_cond = new ExactMatchCondition('user', $this->get_user());
		$course_cond = new ExactMatchCondition('course', $this->get_course());
		return new AndCondition($shown_cond, $date_cond, $user_cond, $course_cond);
	}
}
?>