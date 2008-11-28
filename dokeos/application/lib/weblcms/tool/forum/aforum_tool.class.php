<?php
/**
 * $Id$
 * Forum tool
 * @package application.weblcms.tool
 * @subpackage forum
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/forum_browser.class.php';
require_once dirname(__FILE__).'/forum_topic_browser.class.php';
require_once dirname(__FILE__).'/forum_post_browser.class.php';
require_once dirname(__FILE__).'/../../category_manager/learning_object_publication_category_manager.class.php';

/**
 * This tool allows a user to publish forums in his or her course.
 */
class ForumTool extends Tool
{
	/*
	 * Inherited.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		if (isset($_GET['admin']))
		{
			$_SESSION['forumadmin'] = $_GET['admin'];
		}
		if ($_SESSION['forumadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';
			$pub = new LearningObjectPublisher($this, 'forum');
			$html[] =  $pub->as_html();
			$this->display_header($trail);
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header($trail);
			if(isset($_GET['category_manager_action']))
			{
				echo '<a href="'.$this->get_url().'">Back</a>';
				$catman = new LearningObjectPublicationCategoryManager($this, 'forum');
				echo $catman->as_html();
				$this->display_footer();
				return;
			}
			elseif(isset($_GET['topic']))
			{
				$this->set_parameter('forum',$_GET['forum']);
				$this->set_parameter('topic',$_GET['topic']);
				$browser = new ForumPostBrowser($this);
			}
			elseif(isset($_GET['forum']))
			{
				$this->set_parameter('forum',$_GET['forum']);
				$browser = new ForumTopicBrowser($this);
				$browser->perform_requested_actions();
			}
			else
			{
				if($this->is_allowed(ADD_RIGHT))
				{
					$toolbar_data = array();
					$toolbar_data[] = array ('href' => $this->get_url(array('admin' => 1)), 'label' => Translation :: get('Publish'), 'img' => Theme :: get_common_image_path().'action_publish.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
					$toolbar_data[] = array ('href' => $this->get_url(array('category_manager_action' => 1)), 'label' => Translation :: get('ManageCategories'), 'img' => Theme :: get_common_image_path().'action_category.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
					
					echo DokeosUtilities :: build_toolbar($toolbar_data, array (), 'margin-top: 1em; margin-bottom: 1em;');
				}
				echo $this->perform_requested_actions();
				$browser = new ForumBrowser($this);
			}
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>