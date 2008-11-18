<?php
/**
 * $Id$
 * Document tool - browser
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/../../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/document_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/learning_object_publication_details_renderer.class.php';

class DocumentBrowser extends LearningObjectPublicationBrowser
{

	function DocumentBrowser($parent, $types)
	{
		parent :: __construct($parent, 'document');
		$tree_id = 'pcattree';
		$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
		$parent->set_parameter($tree_id, $_GET[$tree_id]);
		if(isset($_GET['pid']))
		{
			$this->set_publication_id($_GET['pid']);
			$renderer = new LearningObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$renderer = new DocumentPublicationListRenderer($this);
		}
		$this->set_publication_list_renderer($renderer);
		$this->set_publication_category_tree($tree);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$dm = WeblcmsDataManager :: get_instance();
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$course_groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$course_groups = $this->get_course_groups();
		}
		
		$conditions[] = new EqualityCondition('type','document');
		if($this->get_parent()->get_condition())
			$conditions[] = $this->get_parent()->get_condition();
		$cond = new AndCondition($conditions);
		
		$pubs = $dm->retrieve_learning_object_publications($this->get_course_id(), $this->get_category(), $user_id, $course_groups, $this->get_condition(), false, array (Document :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC), 0, -1, null, $cond);
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
			$row[] = $renderer->render_icon($publication);
			$row[] = sprintf($wrapper,$renderer->render_title($publication));
			$row[] = sprintf($wrapper,$renderer->render_description($publication));
			$row[] = sprintf($wrapper,$renderer->render_publication_date($publication));
			$row[] = sprintf($wrapper,$renderer->render_publisher($publication));
			$row[] = sprintf($wrapper,$renderer->render_publication_targets($publication));
			if($this->is_allowed(EDIT_RIGHT) || $this->is_allowed(DELETE_RIGHT))
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
		if(is_null($category))
		{
			$category = $this->get_category();
		}
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->count_learning_object_publications($this->get_course_id(), $category, $this->get_user_id(), $this->get_course_groups(), $this->get_condition($category));
	}

	function get_condition($category = null)
	{ 
		if(is_null($category))
		{
			$category = $this->get_publication_category_tree()->get_current_category_id();
		}
		$tool_cond= new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'document');
		$category_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID,$category );
		return new AndCondition($tool_cond, $category_cond);
	}
}
?>