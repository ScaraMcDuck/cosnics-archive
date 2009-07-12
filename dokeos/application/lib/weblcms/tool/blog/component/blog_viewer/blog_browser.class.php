<?php
/**
 * $Id: blog_browser.class.php 16944 2008-11-26 13:32:18Z vanpouckesven $
 * Blog tool - browser
 * @package application.weblcms.tool
 * @subpackage blog
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/learning_object_publication_details_renderer.class.php';
//require_once Path :: get_repository_path() . 'lib/learning_object/blog/blog.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/description/description.class.php';
/**
 * Browser to allow the user to view the published blogs
 */
class BlogBrowser extends LearningObjectPublicationBrowser
{
	/**
	 * @see LearningObjectPublicationBrowser::LearningObjectPublicationBrowser()
	 */
	private $publications;

	function BlogBrowser($parent)
	{
		parent :: __construct($parent, 'blog');
		if(Request :: get('pid') && $parent->get_action() == 'view')
		{
			$this->set_publication_id(Request :: get('pid'));
			$parent->set_parameter(Tool :: PARAM_ACTION, BlogTool :: ACTION_VIEW_BLOGS);
			$renderer = new LearningObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$tree_id = 'pcattree';
			$value = Request :: get($tree_id)?Request :: get($tree_id):0;
			$parent->set_parameter($tree_id, $value);

			$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
			$this->set_publication_category_tree($tree);

			$renderer = new ListLearningObjectPublicationListRenderer($this);
			$actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'),
						 Tool :: ACTION_HIDE => Translation :: get('Hide'),
						 Tool :: ACTION_SHOW => Translation :: get('Show'),
						 Tool :: ACTION_MOVE_SELECTED_TO_CATEGORY => Translation :: get('MoveSelected'));
			$renderer->set_actions($actions);


		}

		$this->set_publication_list_renderer($renderer);
	}
	/**
	 * Retrieves the publications
	 * @return array An array of LearningObjectPublication objects
	 */
	function get_publications($from, $count, $column, $direction)
	{
		if(empty($this->publications))
		{
			$tree_id = 'pcattree';
			$category = Request :: get($tree_id)?Request :: get($tree_id):0;

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
			$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'blog');
			$conditions[] = new InCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID, $category);
			
			$access = array();
			$access[] = new InCondition('user', $user_id, $datamanager->get_database()->get_alias('learning_object_publication_user'));
			$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('learning_object_publication_course_group'));
			if (!empty($user_id) || !empty($course_groups))
			{
				$access[] = new AndCondition(array(new EqualityCondition('user', null, $datamanager->get_database()->get_alias('learning_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('learning_object_publication_course_group'))));
			}
			$conditions[] = new OrCondition($access);
			
			$subselect_conditions = array();
			$subselect_conditions[] = new EqualityCondition('type', 'blog_item');
			if($this->get_parent()->get_condition())
			{
				$subselect_conditions[] = $this->get_parent()->get_condition();
			}
			$subselect_condition = new AndCondition($subselect_conditions);
			$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
			$condition = new AndCondition($conditions);
			
			$publications = $datamanager->retrieve_learning_object_publications_new($condition, new ObjectTableOrder(LearningObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
			$visible_publications = array ();
			while ($publication = $publications->next_result())
			{
				// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
				if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
				{
					continue;
				}
				$visible_publications[] = $publication;
			}
			$this->publications = $visible_publications;
		}

		return $this->publications;

	}
	/**
	 * Retrieves the number of published annoucements
	 * @return int
	 */
	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>