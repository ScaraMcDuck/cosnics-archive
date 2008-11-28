<?php
/**
 * $Id$
 * Wiki tool
 * @package application.weblcms.tool
 * @subpackage wiki
 */
//require_once dirname(__FILE__).'/../repository_tool.class.php';
require_once dirname(__FILE__).'/wiki_browser.class.php';
/**
 * This tool allows a user to publish wikis in his or her course.
 */
class WikiTool extends Tool
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
			$_SESSION['wikiadmin'] = $_GET['admin'];
		}
		if ($_SESSION['wikiadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';
			$pub = new LearningObjectPublisher($this, 'wiki');
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
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
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.Theme :: get_common_image_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			$browser = new WikiBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>