<?php
/**
 * $Id: wikitool.class.php 9206 2006-09-05 10:12:59Z bmol $
 * Chat tool
 * @package application.weblcms.tool
 * @subpackage chat
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/chatbrowser.class.php';
/**
 * This tool allows a user to publish chatboxes in his or her course.
 */
class ChatTool extends RepositoryTool
{
	// Inherited.
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
			$_SESSION['chatadmin'] = $_GET['admin'];
		}
		if ($_SESSION['chatadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'chatbox');
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.$this->get_parent()->get_path(WEB_IMG_PATH).'browser.gif" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
			$this->display_header($trail);
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header($trail);
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.$this->get_parent()->get_path(WEB_IMG_PATH).'publish.gif" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			$browser = new ChatBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>