<?php
/**
 * Document tool - browser
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/documentpublicationlistrenderer.class.php';

class DocumentBrowser extends LearningObjectPublicationBrowser
{
	function DocumentBrowser($parent, $types)
	{
		parent :: __construct($parent, 'document');
		$tree_id = 'pcattree';
		$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
		$renderer = new DocumentPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
		$this->set_publication_category_tree($tree);		
	}
	
	function get_publications($from, $count, $column, $direction)
	{
		$dm = WeblcmsDataManager :: get_instance();
		$pubs = $dm->retrieve_learning_object_publications($this->get_course_id(), $this->get_category(), $this->get_user_id(), $this->get_groups(), $this->get_condition());
		$data = array ();
		$renderer = $this->get_publication_list_renderer();
		$index = 0;
		while ($publication = $pubs->next_result())
		{
			$first = ($index == 0);
			$last = ($index == count($pubs) - 1);
			$row = array ();
			$row[] = $renderer->render_title($publication);
			$row[] = $renderer->render_description($publication);
			$row[] = $renderer->render_publication_date($publication);
			$row[] = $renderer->render_publisher($publication);
			$row[] = $renderer->render_publication_targets($publication);
			$row[] = $renderer->render_publication_actions($publication, $first, $last);
			$data[] = $row;
			$index++;
		}
		return $data;
	}

	function get_publication_count()
	{
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->count_learning_object_publications($this->get_course_id(), $this->get_category(), $this->get_user_id(), $this->get_groups(), $this->get_condition());
	}
	
	function get_condition()
	{
		$tool_cond= new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'document');
		$shown_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_HIDDEN, 0);
		$category_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID, $this->get_publication_category_tree()->get_current_category_id());
		return new AndCondition($tool_cond, $shown_cond, $category_cond);	
	}
}
?>