<?php
/**
 * $Id: document_browser.class.php 22551 2009-07-31 11:20:38Z MichaelKyndt $
 * Document tool - browser
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/geolocation_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/learning_object_publication_details_renderer.class.php';

class GeolocationBrowser extends LearningObjectPublicationBrowser
{

	function GeolocationBrowser($parent, $types)
	{
		parent :: __construct($parent, 'geolocation');

		if(Request :: get('pid'))
		{
			$this->set_publication_id(Request :: get('pid'));
			$renderer = new LearningObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$renderer = new GeolocationPublicationListRenderer($this);
		}
		$this->set_publication_list_renderer($renderer);

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
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'geolocation');
		
		$access = array();
		$access[] = new InCondition('user', $user_id, $datamanager->get_database()->get_alias('learning_object_publication_user'));
		$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('learning_object_publication_course_group'));
		if (!empty($user_id) || !empty($course_groups))
		{
			$access[] = new AndCondition(array(new EqualityCondition('user', null, $datamanager->get_database()->get_alias('learning_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('learning_object_publication_course_group'))));
		}
		$conditions[] = new OrCondition($access);
		
		$subselect_conditions = array();
		$subselect_conditions[] = new EqualityCondition('type', 'physical_location');
		if($this->get_parent()->get_condition())
		{
			$subselect_conditions[] = $this->get_parent()->get_condition();
		}
		$subselect_condition = new AndCondition($subselect_conditions);
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$pubs = $datamanager->retrieve_learning_object_publications_new($condition, new ObjectTableOrder(Document :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
		$data = array ();
		$renderer = $this->get_publication_list_renderer();
		$index = 0;
		while ($publication = $pubs->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			// TODO: This sort of thing should really happen in advance, using a Condition; also, avoid code duplication (goes for all tools)
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$first = ($index == 0);
			$last = ($index == $pubs->size() - 1);
			$row = array ();
			$wrapper = '%s';
			if(!$publication->is_visible_for_target_users())
			{
				$wrapper = '<span class="invisible">%s</span>';
			}
			if($this->is_allowed(EDIT_RIGHT) || $this->is_allowed(DELETE_RIGHT))
			{
				$row[] = $publication->get_id();
			}
			//$row[] = $renderer->render_icon($publication);
			$row[] = sprintf($wrapper,$renderer->render_title($publication));
			$row[] = sprintf($wrapper,$renderer->render_description($publication));
			$row[] = sprintf($wrapper,$renderer->render_publication_date($publication));
			$row[] = sprintf($wrapper,$renderer->render_repo_viewer($publication));
			$row[] = sprintf($wrapper,$renderer->render_publication_targets($publication));
			//if($this->is_allowed(EDIT_RIGHT) || $this->is_allowed(DELETE_RIGHT))
			{
				$row[] = $renderer->render_publication_actions($publication, $first, $last);
			}
			$data[] = $row;
			$index++;
		}
		return $data;
	}

	function get_publication_count($category = null)
	{
		$dm = WeblcmsDataManager :: get_instance();

		$conditions = array();		
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		
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
		$subselect_condition = new EqualityCondition('type', 'physical_location');
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);

		$condition = new AndCondition($conditions);

		return $dm->count_learning_object_publications_new($condition);
	}

	function get_condition()
	{
		$tool_cond= new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'geolocation');
		return new AndCondition($tool_cond, $category_cond);
	}
}
?>