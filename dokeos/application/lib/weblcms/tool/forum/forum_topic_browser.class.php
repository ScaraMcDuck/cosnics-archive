<?php
/**
 * $Id$
 * Forum tool - topic browser
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../learning_object_publication_browser.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_form.class.php';
require_once dirname(__FILE__).'/forum_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/forum_topic_list_renderer.class.php';

class ForumTopicBrowser extends LearningObjectPublicationBrowser
{
	private $forum_publication;
	function ForumTopicBrowser($parent, $types)
	{
		parent :: __construct($parent, 'forum_topic');
		$renderer = new ForumTopicListRenderer($this);
		$this->set_publication_list_renderer($renderer);
		$datamanager = WeblcmsDataManager :: get_instance();
		$forum_id = $this->get_parameter('forum');
		$this->forum_publication = $datamanager->retrieve_learning_object_publication($forum_id);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$forum = $this->forum_publication->get_learning_object();
		$topics = $forum->get_forum_topics();
		$index = 0;
		$renderer = new ForumTopicListRenderer($this);
		while ($topic = $topics->next_result())
		{
			$first = ($index == 0);
			$last = ($index == $topics->size() - 1);
			$forum_table_row = array();
			if($this->is_allowed(EDIT_RIGHT) || $this->is_allowed(DELETE_RIGHT))
			{
				$forum_table_row[] = $topic->get_id();
			}
			$forum_url = $this->get_url(array('topic'=>$topic->get_id()));
			$forum_table_row[] = '<a href="'.$forum_url.'">'.$topic->get_title().'</a>';
			$forum_table_row[] = ''.$topic->get_reply_count();
			$author = $this->get_user_info($topic->get_owner_id());
			$forum_table_row[] = $author->get_firstname().' '.$author->get_lastname();
			$last_post = $topic->get_last_post();
			$last_post_author = $this->get_user_info($last_post->get_owner_id());
			$forum_table_row[] = date('r',$last_post->get_creation_date()).' '.Translation :: get('By').' '.$last_post_author->get_firstname().' '.$last_post_author->get_lastname();
			if($this->is_allowed(EDIT_RIGHT) || $this->is_allowed(DELETE_RIGHT))
			{
				$forum_table_row[] = $renderer->render_publication_actions($topic, $first, $last);
			}
			$visible_publications[] = $forum_table_row;
			$index++;
		}
		return $visible_publications;
	}
	function get_publication_count()
	{
		$forum = $this->forum_publication->get_learning_object();
		return $forum->get_topic_count();
	}
	function as_html()
	{
		$forum = $this->forum_publication->get_learning_object();
		$display_topics = true;
		if($_GET['forum_action'] == 'newtopic')
		{
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, new AbstractLearningObject('forum_topic', $this->get_user_id()), 'create', 'post', $this->get_url(array('forum_action'=>'newtopic')));
			if (!$form->validate())
			{
				$html .=  $form->toHTML();
				$display_topics = false;
			}
			else
			{
				$topic = $form->create_learning_object();
				$topic->set_parent_id($forum->get_id());
				$topic->update();
				$course = $this->get_course_id();
				$html .= Display::display_normal_message(Translation :: get('TopicAdded'),true);
				$display_topics = true;
			}
		}
		if($display_topics)
		{
			$toolbar_data = array ();
			$toolbar_data[] = array ('href' => $this->get_url(array('forum_action'=>'newtopic')), 'img' => Theme :: get_common_image_path().'action_create.png', 'label' => Translation :: get('NewTopic'), 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$html .=  '<div style="margin-bottom: 1em;">'.DokeosUtilities :: build_toolbar($toolbar_data).'</div>';
			$html .= '<b>'.$forum->get_title().'</b>';
			$html .= $this->listRenderer->as_html();
		}
		return $html;
	}
	function perform_requested_actions()
	{
		if(isset($_GET['action']))
		{
			switch($_GET['action'])
			{
				case 'lock':
				{
					$forum = $this->forum_publication->get_learning_object();
					$topic = $forum->get_forum_topic($_GET['topic_id']);
					$topic->set_locked(true);
					$topic->update();
					break;
				}
				case 'unlock':
				{
					$forum = $this->forum_publication->get_learning_object();
					$topic = $forum->get_forum_topic($_GET['topic_id']);
					$topic->set_locked(false);
					$topic->update();
					break;
				}
			}
		}
	}
}
?>