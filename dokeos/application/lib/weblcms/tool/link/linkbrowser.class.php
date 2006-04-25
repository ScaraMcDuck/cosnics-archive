<?php
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/linkpublicationlistrenderer.class.php';

class LinkBrowser extends LearningObjectPublicationBrowser
{
	function LinkBrowser($parent, $types)
	{
		parent :: __construct($parent, 'link');
		// TODO: Assign a dynamic tree name.
		$tree_id = 'pcattree';
		$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
		$renderer = new LinkPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
		$this->set_publication_category_tree($tree);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$dm = WeblcmsDataManager :: get_instance();
		$pubs = $dm->retrieve_learning_object_publications($this->get_course_id(), $this->get_category(), $this->get_user_id(), $this->get_groups(), $this->get_condition());
		$data = array ();
		$renderer = $this->get_publication_list_renderer();
		foreach ($pubs as $publication)
		{
			$row = array ();
			$row[] = $renderer->render_title($publication);
			$row[] = $renderer->render_description($publication);
			$data[] = $row;
		}
		return $data;
	}

	function get_publication_count()
	{
		$dm = WeblcmsDataManager :: get_instance();
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
		$category_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID, $this->get_publication_category_tree()->get_current_category_id());
		return new AndCondition($shown_cond, $date_cond, $tool_cond, $category_cond);
	}
}
?>