<?php
/**
 * $Id$
 * Forum tool - browser
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/forum_publication_list_renderer.class.php';

class ForumBrowser extends LearningObjectPublicationBrowser
{
	private $current_category;
	function ForumBrowser($parent, $types)
	{
		parent :: __construct($parent, 'forum');
		$renderer = new ForumPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
		$this->current_category = 0;
	}

	function as_html()
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$categories = $datamanager->retrieve_learning_object_publication_categories($this->get_course_id(),'forum');
		$html = array();
		$html[] =  parent::as_html();
		foreach($categories as $index => $category_info)
		{
			$category = $category_info['obj'];
			$this->current_category = $category->get_id();
			$html[] = '<h2>'.$category->get_title().'</h2>';
			$renderer = new ForumPublicationListRenderer($this);
			$this->set_publication_list_renderer($renderer);
			$html[] =  parent::as_html();
		}
		return implode("\n",$html);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'forum');
		$condition = $tool_condition;
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
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), $this->current_category, $user_id, $course_groups, $condition, false, array (Forum :: PROPERTY_DISPLAY_ORDER_INDEX));
		$visible_publications = array ();
		$renderer = $this->get_publication_list_renderer();
		$index = 0;
		$last_visit_date = $this->get_last_visit_date();
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$first = ($index == 0);
			$last = ($index == $publications->size() - 1);
			$forum_table_row = array();
			$forum = $publication->get_learning_object();
			if($this->is_allowed(EDIT_RIGHT) || $this->is_allowed(DELETE_RIGHT))
			{
				$forum_table_row[] = $publication->get_id();
			}
			$new = '';
			if( $publication->get_publication_date() >= $last_visit_date)
			{
				$new = '_new';
			}
			$forum_table_row[] = '<img src="'.Theme :: get_common_img_path().'learning_object/forum'.$new.'.png">';
			$forum_url = $this->get_url(array('forum'=>$publication->get_id()));
			$forum_table_row[] = '<a href="'.$forum_url.'">'.$forum->get_title().'</a><br /><small>'.$forum->get_description().'</small>';
			$forum_table_row[] = ''.$forum->get_topic_count();
			$forum_table_row[] = ''.$forum->get_post_count();
			$last_post = $forum->get_last_post();
			if(!is_null($last_post))
			{
				$last_post_author = $this->get_user_info($last_post->get_owner_id());
				$forum_table_row[] = date('r',$last_post->get_creation_date()).' '.Translation :: get('By').' '.$last_post_author->get_firstname().' '.$last_post_author->get_lastname();
			}
			else
			{
				$forum_table_row[] = '-';
			}
			if($this->is_allowed(EDIT_RIGHT) || $this->is_allowed(DELETE_RIGHT))
			{
				$forum_table_row[] = $renderer->render_publication_actions($publication, $first, $last);
			}
			$visible_publications[] = $forum_table_row;
			$index++;
		}
		return $visible_publications;
	}
	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>