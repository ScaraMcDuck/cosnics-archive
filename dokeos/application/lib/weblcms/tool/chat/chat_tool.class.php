<?php
/**
 * $Id: wikitool.class.php 9206 2006-09-05 10:12:59Z bmol $
 * Chat tool
 * @package application.weblcms.tool
 * @subpackage chat
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/chat_browser.class.php';
/**
 * This tool allows a user to publish chatboxes in his or her course.
 */
class ChatTool extends Tool
{
	// Inherited.
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
			$_SESSION['chatadmin'] = $_GET['admin'];
		}
		if ($_SESSION['chatadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';
			$pub = new LearningObjectPublisher($this, 'chatbox');
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
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
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.Theme :: get_common_img_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			$browser = new ChatBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>