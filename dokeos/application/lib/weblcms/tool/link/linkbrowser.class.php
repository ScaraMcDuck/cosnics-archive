<?php
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';

class LinkBrowser extends LearningObjectPublicationBrowser
{
	function LinkBrowser($parent)
	{
		parent :: __construct($parent, 'link', intval($_GET['category']));
		$this->set_column_titles(get_lang('Title'), get_lang('Description'));
	}

	function get_publications($from, $number_of_items, $column, $direction)
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
		$tool_cond = new EqualityCondition('tool','link');
		$shown_cond = new EqualityCondition('hidden', 0);
		$date_from_zero = new EqualityCondition('from_date', 0);
		$date_to_zero = new EqualityCondition('to_date', 0);
		$date_from_passed = new InequalityCondition('from_date', InequalityCondition :: LESS_THAN, $time);
		$date_to_coming = new InequalityCondition('to_date', InequalityCondition :: GREATER_THAN, $time);
		$date1 = new OrCondition($date_from_zero, $date_from_passed);
		$date2 = new OrCondition($date_to_zero, $date_to_coming);
		$date_cond = new AndCondition($date1, $date2);
		return new AndCondition($shown_cond, $date_cond, $tool_cond);
	}
}
?>