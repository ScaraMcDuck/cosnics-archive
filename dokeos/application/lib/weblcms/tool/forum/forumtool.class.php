<?php
/**
 * $Id$
 * Forum tool
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/forumbrowser.class.php';
require_once dirname(__FILE__).'/forumtopicbrowser.class.php';
require_once dirname(__FILE__).'/forumpostbrowser.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationcategorymanager.class.php';

/**
 * This tool allows a user to publish forums in his or her course.
 */
class ForumTool extends RepositoryTool
{
	/*
	 * Inherited.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			$this->display_header($trail);
			Display :: display_not_allowed();
			$this->display_footer();
			return;
		}
		if (isset($_GET['admin']))
		{
			$_SESSION['forumadmin'] = $_GET['admin'];
		}
		if ($_SESSION['forumadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.Theme :: get_common_img_path().'browser.gif" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
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
					echo '<a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.Theme :: get_common_img_path().'publish.gif" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a> ';
					echo '<a href="' . $this->get_url(array('category_manager_action' => 1), true) . '"><img src="'.Theme :: get_common_img_path().'category.gif" alt="'.Translation :: get('ManageCategories').'" style="vertical-align:middle;"/> '.Translation :: get('ManageCategories').'</a></p>';
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