<?php
/**
 * $Id$
 * Link tool - browser
 * @package application.weblcms.tool
 * @subpackage link
 */
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/linkpublicationlistrenderer.class.php';
require_once dirname(__FILE__).'/../../browser/list_renderer/learningobjectpublicationdetailsrenderer.class.php';

class LinkBrowser extends LearningObjectPublicationBrowser
{
	function LinkBrowser($parent, $types)
	{
		parent :: __construct($parent, 'link');
		// TODO: Assign a dynamic tree name.
		$tree_id = 'pcattree';
		$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
		
		if(isset($_GET['pid']))
		{
			$this->set_publication_id($_GET['pid']);
			$renderer = new LearningObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$renderer = new LinkPublicationListRenderer($this);
		}
		$this->set_publication_list_renderer($renderer);
		$this->set_publication_category_tree($tree);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$dm = WeblcmsDataManager :: get_instance();
		$tool_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'link');
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$groups = $this->get_groups();
		}
		$publications = $dm->retrieve_learning_object_publications($this->get_course_id(), $this->get_publication_category_tree()->get_current_category_id(), $user_id, $groups,$tool_cond);
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$visible_publications[] = $publication;
		}
		return $visible_publications;
	}

	function get_publication_count($category = null)
	{
		if(is_null($category))
		{
			$category = $this->get_publication_category_tree()->get_current_category_id();
		}
		$dm = WeblcmsDataManager :: get_instance();
		$tool_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'link');
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$groups = $this->get_groups();
		}
		return $dm->count_learning_object_publications($this->get_course_id(), $category, $user_id, $groups, $tool_cond);
	}
}
?>