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
		$this->display_header();
		if (isset($_GET['documenttoolmode']))
		{
			$_SESSION['documenttoolmode'] = $_GET['documenttoolmode'];
		}
		echo '<ul style="list-style: none; padding: 0; margin: 0 0 1em 0">';
		$i = 0;
		foreach (array('Browser Mode', 'Publisher Mode', 'Category Manager Mode') as $title)
		{
			$current = ($_SESSION['documenttoolmode'] == $i);
			echo '<li style="display: inline; margin: 0 1ex 0 0; padding: 0">';
			if (!$current) echo '<a href="' . $this->get_url(array('documenttoolmode' => $i), true) . '">';
			echo '[' . $title . ']';
			if (!$current) echo '</a>';
			echo '</li>';
			$i++;
		}
		echo '</ul>';
		$this->perform_requested_actions();
		switch ($_SESSION['documenttoolmode'])
		{
			case 2:
				require_once dirname(__FILE__).'/../../learningobjectpublicationcategorymanager.class.php';
				$catman = new LearningObjectPublicationCategoryManager($this, 'document');
				echo $catman->as_html();
				break;
			case 1:
				require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
				$pub = new LearningObjectPublisher($this, 'document');
				echo $pub->as_html();
				break;
			default:
				require_once dirname(__FILE__).'/documentbrowser.class.php';
				$browser = new DocumentBrowser($this);
				echo $browser->as_html();
		}
		$this->display_footer();
	}
}
?>