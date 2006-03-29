<?php
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobjectdisplay.class.php';

class LearningObjectViewer extends LearningObjectPublisherComponent
{
	function as_html()
	{
		if ($_GET['object'])
		{
			$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($_GET['object']);
			return LearningObjectDisplay :: factory($object)->get_full_html().'<p><a href="'.$this->get_url(array ('publish_action' => 'publicationcreator', 'object' => $object->get_id()), true).'"><img src="'.api_get_path(WEB_CODE_PATH).'img/publish.gif" alt="'.get_lang('Publish').'" style="vertical-align: middle"/> '.get_lang('PublishThisObject').'</a></p>';
		}
	}
}
?>