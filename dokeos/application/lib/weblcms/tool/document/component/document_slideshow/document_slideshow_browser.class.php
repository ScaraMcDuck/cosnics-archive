<?php
/**
 * $Id: documentbrowser.class.php 12939 2007-09-05 16:36:36Z ceetee $
 * Document tool - slideshow
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/../../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/document_publication_slideshow_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/document/document.class.php';

class DocumentSlideshowBrowser extends LearningObjectPublicationBrowser
{
	function DocumentSlideshowBrowser($parent, $types)
	{
		parent :: __construct($parent, 'document');
		$tree_id = 'pcattree';
		//$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
		$parent->set_parameter($tree_id, Request :: get($tree_id));
		$renderer = new DocumentPublicationSlideshowRenderer($this);
		$this->set_publication_list_renderer($renderer);
		//$this->set_publication_category_tree($tree);
		$this->set_category(Request :: get($tree_id));
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = array();
			$course_groups = array();
		}
		else
		{
			$user_id = $this->get_user_id();
			$course_groups = $this->get_course_groups();
		}
		
		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = $this->get_condition($this->get_category());
		
		$access = array();
		$access[] = new InCondition('user', $user_id, $datamanager->get_database()->get_alias('learning_object_publication_user'));
		$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('learning_object_publication_course_group'));
		if (!empty($user_id) || !empty($course_groups))
		{
			$access[] = new AndCondition(array(new EqualityCondition('user', null, $datamanager->get_database()->get_alias('learning_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('learning_object_publication_course_group'))));
		}
		$conditions[] = new OrCondition($access);
		
		$subselect_condition = new EqualityCondition('type', 'document');
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$publications = $datamanager->retrieve_learning_object_publications_new($condition, new ObjectTableOrder(Document :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
		$visible_publications = array ();
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$document = $publication->get_learning_object();
			if($document->is_image())
			{
				$visible_publications[] = $publication;
			}

		}
		return $visible_publications;
	}

	function get_publication_count($category = null)
	{
		if(is_null($category))
		{
			$category = $this->get_category();
		}
		
		$dm = WeblcmsDataManager :: get_instance();

		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = $this->get_condition($category);
		
		$user_id = $this->get_user_id();
		$course_groups = $this->get_course_groups();

		$access = array();
		$access[] = new InCondition('user', $user_id, $dm->get_database()->get_alias('learning_object_publication_user'));
		$access[] = new InCondition('course_group_id', $course_groups, $dm->get_database()->get_alias('learning_object_publication_course_group'));
		if (!empty($user_id) || !empty($course_groups))
		{
			$access[] = new AndCondition(array(new EqualityCondition('user', null, $dm->get_database()->get_alias('learning_object_publication_user')), new EqualityCondition('course_group_id', null, $dm->get_database()->get_alias('learning_object_publication_course_group'))));
		}

		$conditions[] = new OrCondition($access);
		$condition = new AndCondition($conditions);

		return $dm->count_learning_object_publications_new($condition);
	}

	function get_condition($category = 0)
	{
		$tool_cond= new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'document');
		$category_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID,$category );
		return new AndCondition($tool_cond, $category_cond);
	}

	function get_category()
	{
		$cat = Request :: get('pcattree');
		return $cat?$cat:0;
	}
}
?>