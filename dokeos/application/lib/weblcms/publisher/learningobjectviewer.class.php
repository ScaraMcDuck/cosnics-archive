<?php
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject_display.class.php';

class LearningObjectViewer extends LearningObjectPublisherComponent
{
	function display()
	{
		if ($_GET['object']) {
			$object = RepositoryDataManager::get_instance()->retrieve_learning_object($_GET['object']);
			echo LearningObjectDisplay::factory($object)->get_full_html();
			echo '<p>[ USE ]</p>';
		}		
	}
}
?>