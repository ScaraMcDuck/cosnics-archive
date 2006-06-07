<?php
/**
 * Forum tool - browser
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/forumpublicationlistrenderer.class.php';

class ForumBrowser extends LearningObjectPublicationBrowser
{
	function ForumBrowser($parent, $types)
	{
		parent :: __construct($parent, 'forum');
		$renderer = new ForumPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'forum');
		$condition = $tool_condition;
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
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $groups, $condition, false, array (Forum :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC));
		$visible_publications = array ();
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$forum_table_row = array();
			$forum = $publication->get_learning_object();
			$forum_table_row[] = '<img src="'.api_get_path(WEB_CODE_PATH).'img/forum.gif">';
			$forum_url = $this->get_url(array('forum'=>$publication->get_id()));
			$forum_table_row[] = '<a href="'.$forum_url.'">'.$forum->get_title().'</a><br /><small>'.$forum->get_description().'</small>';
			$forum_table_row[] = $forum->get_topic_count();
			$forum_table_row[] = $forum->get_post_count();
			$visible_publications[] = $forum_table_row;
		}
		return $visible_publications;
	}

	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>