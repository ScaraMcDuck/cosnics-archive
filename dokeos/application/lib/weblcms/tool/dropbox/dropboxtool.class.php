<?php
/**
 * $Id$
 * Dropbox tool
 * @package application.weblcms.tool
 * @subpackage dropbox
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/dropboxbrowser.class.php';
/**
 * Dropbox tool.
 */
class DropboxTool extends RepositoryTool
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
			$_SESSION['dropboxadmin'] = $_GET['admin'];
		}
		if ($_SESSION['dropboxadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'document');
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
			$browser = new DropboxBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>