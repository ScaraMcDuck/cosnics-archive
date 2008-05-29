<?php
/**
 * $Id$
 * Forum tool - post browser
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../learning_object_publication_browser.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_form.class.php';
require_once dirname(__FILE__).'/forum_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/forum_topic_list_renderer.class.php';
require_once dirname(__FILE__).'/forum_post_list_renderer.class.php';
/**
 * Browser to show the forum posts to the end user
 */
class ForumPostBrowser extends LearningObjectPublicationBrowser
{
	private $forum_publication;
	private $topic;
	private $forum;
	
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

	/**
	 * Send a notification email to all the users who suscribed to this course
	 *
	 * @param boolean $error_notify   - display warning messages for emails that weren't sent properly 
	 * @param boolean $success_notify - display messages for sucessfully sent emails
	 * @return html code
	 */
	function send_notification_emails($success_notify = FALSE, $error_notify = TRUE)
	{
		$html = "";
		
		// Get the current course
		$course = parent::get_parent()->get_course();
		// Get all the users who subscribed to the current course
		$users_relations = $course->get_subscribed_users();
		// Get the current logged user and their email
		$logged_user = $this->get_user_info($this->get_user_id());
		$logged_user_email = $logged_user->get_email();
		// Get the webmaster email
		$adm = AdminDataManager :: get_instance();
		$settings = $adm->retrieve_setting_from_variable_name('administrator_email', 'admin');
		$webmaster_email = $settings->get_value();
		// Retrieve a bunch of required stuff
		$topic = $this->topic->get_title();
		$topic_url = 'http://' . $_SERVER['SERVER_NAME'] . $this->get_url();
		$subject = Translation :: get('ForumNotifyMsgHeader') . "\"$topic\"";
		// Grab the list of users who must be notified
		$notification_emails = $this->topic->get_notification_emails();
		foreach ($notification_emails as $email)
		{
			if ($email === $logged_user_email)
			{
				continue;
			}
			$inp = array('#TOPIC#', '#FORUM#', '#COURSE#');
			$outp = array($topic, $this->forum->get_title(), $course->get_name());
			$content = str_replace($inp, $outp, Translation :: get('ForumNotifyMsgContent'));
			$content .= $topic_url;
			// Prepare the email
			$mail = Mail :: factory($subject, $content, $email, $webmaster_email);
			// Check whether it was sent successfully
			if ($mail->send() === FALSE) {
				if ($error_notify) {
					$msg = Translation :: get('ForumNotifyError') . $email;
					$html .= Display::display_warning_message($msg, true);
				}
			}
			else {
				if ($success_notify) {
					$msg = Translation :: get('ForumNotifySuccess') . $email;
					$html .= Display::display_normal_message($msg, true);
				}
			}
		}
		
		return $html;
	}
	
	function as_html()
	{		
		$first_post = $this->get_publications(0,1);
		$forum = $this->forum_publication->get_learning_object();
		$show_posts = true;
		
		// Create a new post		
		if($_GET['forum_action'] == 'newpost')
		{
			$new_post =  new AbstractLearningObject('forum_post', $this->get_user_id());
			if (isset($_GET['parent_post']))
			{
				$parent_post = $forum->get_forum_post($_GET['parent_post']);
				$new_post->set_description('<blockquote style="border-left:1px solid gray;padding: 5px;">'.$parent_post->get_description().'</blockquote><br />');
				$title = $parent_post->get_title();
				$new_post->set_title('re: '.$title);
			}
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $new_post,
						'create', 'post', $this->get_url(array('forum_action'=>'newpost',
						ForumPost :: PROPERTY_PARENT_POST => $_GET[ForumPost :: PROPERTY_PARENT_POST])));
			if (!$form->validate())
			{
				$html .=  $form->toHTML();
				$show_posts = false;
			}
			else
			{
				// The new post was validated, create it
				$post = $form->create_learning_object();
				$post->set_parent_id($this->topic->get_id());
				// The PROPERTY_PARENT_POST url parameter is not defined!
				$post->set_parent_post_id($_GET[ForumPost :: PROPERTY_PARENT_POST]);
				$post->update();
				$html .= Display::display_normal_message(Translation :: get('PostAdded'),true);
				$show_posts = true;

				// Check whether the user must be added to the notification
				// list for the given topic.
				$notify = $form->exportValue(ForumPost :: PROPERTY_NOTIFICATION);
				switch ($notify) {
					case ForumPost :: NOTIFY_NONE:
						break;
					case ForumPost :: NOTIFY_TOPIC:
						// Get email of the current user
						$email = $this->get_user_info($this->get_user_id())->get_email();
						$this->topic->add_notification_email($email);
						$this->topic->update();
						break;
				}
				
				// Send the notificaion emails
				$html .= $this->send_notification_emails();
			}
		}
		if ($show_posts)
		{
			$toolbar_data = array ();
			$toolbar_data[] = array ('href' => $this->get_url(array('forum_action'=>'newpost')), 'img' => Theme :: get_common_img_path().'learning_object/forum.png', 'label' => Translation :: get('NewPost'), 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$html .=  '<div style="margin-bottom: 1em;">'.DokeosUtilities :: build_toolbar($toolbar_data).'</div>';
			$html .= '<b><a href="'.$this->get_url(array('topic'=>null)).'">'.$forum->get_title().'</a> : '.$this->topic->get_title().'</b>';
			$html .= $this->listRenderer->as_html();
		}
		return $html;
	}
}
?>