<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';

class LinkTool extends RepositoryTool
{
	function run()
	{
		// Temporary testing code.
		require_once dirname(__FILE__).'/linkbrowser.class.php';
		$browser = new LinkBrowser();
		$browser->display();

		require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
		$pub = new LearningObjectPublisher('link', api_get_course_id(), api_get_user_id());
		$pub->display();
	}
}
?>