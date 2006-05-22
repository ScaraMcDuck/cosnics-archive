<?php
/**
 * Link tool
 * @package application.weblcms.tool
 * @subpackage link
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';

class LinkTool extends RepositoryTool
{
	function run()
	{
		if (isset($_GET['linktoolmode']))
		{
			$_SESSION['linktoolmode'] = $_GET['linktoolmode'];
		}
		if( isset($_GET['admin']) && $_GET['admin'] == 0)
		{
			$_SESSION['linktoolmode'] = 0;
		}
		$html[] =  '<ul style="list-style: none; padding: 0; margin: 0 0 1em 0">';
		$i = 0;
		foreach (array('Browser Mode', 'Publisher Mode', 'Category Manager Mode') as $title)
		{
			$current = ($_SESSION['linktoolmode'] == $i);
			$html[] =   '<li style="display: inline; margin: 0 1ex 0 0; padding: 0">';
			if (!$current)
			{
				$html[] =   '<a href="' . $this->get_url(array('linktoolmode' => $i), true) . '">';
			}
			$html[] =   '[' . $title . ']';
			if (!$current)
			{
				$html[] =   '</a>';
			}
			$html[] =   '</li>';
			$i++;
		}
		$html[] =   '</ul>';
		$html[] = $this->perform_requested_actions();
		switch ($_SESSION['linktoolmode'])
		{
			case 2:
				require_once dirname(__FILE__).'/../../learningobjectpublicationcategorymanager.class.php';
				$catman = new LearningObjectPublicationCategoryManager($this, 'link');
				$html[] = $catman->as_html();
				break;
			case 1:
				require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
				$pub = new LearningObjectPublisher($this, 'link');
				$html[] =  $pub->as_html();
				break;
			default:
				require_once dirname(__FILE__).'/linkbrowser.class.php';
				$browser = new LinkBrowser($this);
				$html[] =  $browser->as_html();
		}
		$this->display_header();
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>