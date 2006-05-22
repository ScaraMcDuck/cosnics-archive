<?php
/**
 * Document tool - list renderer
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
/**
 * This tool allows a user to publish documents in his or her course.
 */
class DocumentTool extends RepositoryTool
{
	/*
	 * Inherited.
	 */
	function run()
	{
		if (isset($_GET['documenttoolmode']))
		{
			$_SESSION['documenttoolmode'] = $_GET['documenttoolmode'];
		}
		if( isset($_GET['admin']) && $_GET['admin'] == 0)
		{
			$_SESSION['documenttoolmode'] = 0;
		}
		$html[] = '<ul style="list-style: none; padding: 0; margin: 0 0 1em 0">';
		$i = 0;
		foreach (array('Browser Mode', 'Publisher Mode', 'Category Manager Mode') as $title)
		{
			$current = ($_SESSION['documenttoolmode'] == $i);
			$html[] =  '<li style="display: inline; margin: 0 1ex 0 0; padding: 0">';
			if (!$current)
			{
				$html[] = '<a href="' . $this->get_url(array('documenttoolmode' => $i), true) . '">';
			}
			$html[] =  '[' . $title . ']';
			if (!$current)
			{
				$html[] =  '</a>';
			}
			$html[] =  '</li>';
			$i++;
		}
		$html[] =  '</ul>';
		$html[] = $this->perform_requested_actions();
		switch ($_SESSION['documenttoolmode'])
		{
			case 2:
				require_once dirname(__FILE__).'/../../learningobjectpublicationcategorymanager.class.php';
				$catman = new LearningObjectPublicationCategoryManager($this, 'document');
				$html[] =  $catman->as_html();
				break;
			case 1:
				require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
				$pub = new LearningObjectPublisher($this, 'document');
				$html[] =  $pub->as_html();
				break;
			default:
				require_once dirname(__FILE__).'/documentbrowser.class.php';
				$browser = new DocumentBrowser($this);
				$html[] =  $browser->as_html();
		}
		$this->display_header();
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>