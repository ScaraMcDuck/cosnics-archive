<?php
require_once dirname(__FILE__) . '/../repositorytool.class.php';
require_once dirname(__FILE__) . '/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__) . '/../../learningobjectpublisher.class.php';

class LinkTool extends RepositoryTool
{
    function run()
	{
		$pub = new LearningObjectPublisher(api_get_user_id(), 'link');
		$pub->display();
	}
}
?>