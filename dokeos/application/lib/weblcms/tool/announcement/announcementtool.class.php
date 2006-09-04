<?php
/**
 * $Id$
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/announcementbrowser.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class AnnouncementTool extends RepositoryTool
{
	/*
	 * Inherited.
	 */
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			$this->display_header();
			api_not_allowed();
			$this->display_footer();
			return;
		}
		if (isset($_GET['admin']))
		{
			$_SESSION['announcementadmin'] = $_GET['admin'];
		}
		if ($_SESSION['announcementadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'announcement');
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.api_get_path(WEB_CODE_PATH).'/img/browser.gif" alt="'.get_lang('BrowserTitle').'" style="vertical-align:middle;"/> '.get_lang('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
			$this->display_header();
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header();
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.api_get_path(WEB_CODE_PATH).'/img/publish.gif" alt="'.get_lang('Publish').'" style="vertical-align:middle;"/> '.get_lang('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			$browser = new AnnouncementBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>