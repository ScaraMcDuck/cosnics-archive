<?php
/**
 * $Id$
 * Forum tool - post browser
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once Path :: get_repository_path(). 'lib/learningobjectform.class.php';
require_once dirname(__FILE__).'/forumpublicationlistrenderer.class.php';
require_once dirname(__FILE__).'/forumtopiclistrenderer.class.php';
require_once dirname(__FILE__).'/forumpostlistrenderer.class.php';
/**
 * Browser to show the forum posts to the end user
 */
class ForumPostBrowser extends LearningObjectPublicationBrowser
{
	private $forum_publication;
	private $topic;
	/**
	 * Constructor
	 */
	function ForumPostBrowser($parent, $types)
	{
		parent :: __construct($parent, 'forum_post');
		$renderer = new ForumPostListRenderer($this);
		$this->set_publication_list_renderer($renderer);
		$datamanager = WeblcmsDataManager :: get_instance();
		$forum_id = $this->get_parameter('forum');
		$this->forum_publication = $datamanager->retrieve_learning_object_publication($forum_id);
		$topic_id = $this->get_parameter('topic');
		$forum = $this->forum_publication->get_learning_object();
		$this->topic = $forum->get_forum_topic($topic_id);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$posts = $this->topic->get_forum_posts();
		$index = 0;
		while ($post = $posts->next_result())
		{
			$visible_publications[] = $post;
		}
		return $visible_publications;
	}

	function get_publication_count()
	{
		return $this->topic->get_forum_posts()->size();
	}
	function as_html()
	{
		$first_post = $this->get_publications(0,1);
		$forum = $this->forum_publication->get_learning_object();
		$show_posts = true;
		if($_GET['forum_action'] == 'newpost')
		{
			$new_post =  new AbstractLearningObject('forum_post', $this->get_user_id());
			if(isset($_GET['parent_post']))
			{
				$parent_post = $forum->get_forum_post($_GET['parent_post']);
				$new_post->set_description('<blockquote style="border-left:1px solid gray;padding: 5px;">'.$parent_post->get_description().'</blockquote><br />');
			}
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE,$new_post, 'create', 'post', $this->get_url(array('forum_action'=>'newpost',ForumPost :: PROPERTY_PARENT_POST => $_GET[ForumPost :: PROPERTY_PARENT_POST])));
			if (!$form->validate())
			{
				$html .=  $form->toHTML();
				$show_posts = false;
			}
			else
			{
				$post = $form->create_learning_object();
				$post->set_parent_id($this->topic->get_id());
				$post->set_parent_post_id($_GET[ForumPost :: PROPERTY_PARENT_POST]);
				$post->update();
				$html .= Display::display_normal_message(Translation :: get_lang('PostAdded'),true);
				$show_posts = true;
			}
		}
		if($show_posts)
		{
			$toolbar_data = array ();
			$toolbar_data[] = array ('href' => $this->get_url(array('forum_action'=>'newpost')), 'img' => $this->get_path(WEB_IMG_PATH).'forum.gif', 'label' => Translation :: get_lang('NewPost'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$html .=  '<div style="margin-bottom: 1em;">'.RepositoryUtilities :: build_toolbar($toolbar_data).'</div>';
			$html .= '<b><a href="'.$this->get_url(array('topic'=>null)).'">'.$forum->get_title().'</a> : '.$this->topic->get_title().'</b>';
			$html .= $this->listRenderer->as_html();
		}
		return $html;
	}
}
?>