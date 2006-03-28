<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';

class LinkTool extends RepositoryTool
{
	function run()
	{
		if (isset($_GET['linktoolmode']))
		{
			$_SESSION['linktoolmode'] = $_GET['linktoolmode'];
		}
		echo '<ul style="list-style: none; padding: 0; margin: 0 0 1em 0">';
		$i = 0;
		foreach (array('Browser Mode', 'Publisher Mode', 'Category Manager Mode') as $title)
		{
			$current = ($_SESSION['linktoolmode'] == $i);
			echo '<li style="display: inline; margin: 0 1ex 0 0; padding: 0">';
			if (!$current) echo '<a href="' . $this->get_url(array('linktoolmode' => $i)) . '">';
			echo '[' . $title . ']';
			if (!$current) echo '</a>';
			echo '</li>';
			$i++;
		}
		echo '</ul>';
		switch ($_SESSION['linktoolmode'])
		{
			case 2:
				require_once dirname(__FILE__).'/../../learningobjectpublicationcategorymanager.class.php';
				$catman = new LearningObjectPublicationCategoryManager($this, 'link');
				echo $catman->as_html();
				break;
			case 1:
				require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
				$pub = new LearningObjectPublisher($this, 'link');
				echo $pub->as_html();
				break;
			default:
				require_once dirname(__FILE__).'/linkbrowser.class.php';
				$browser = new LinkBrowser($this);
				echo $browser->as_html();
		}
	}
}
?>