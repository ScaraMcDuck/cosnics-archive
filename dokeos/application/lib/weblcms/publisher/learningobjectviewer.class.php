<?php
/**
 * @package application.weblcms.tool
 */
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobjectdisplay.class.php';
/**
 * This class represents a learning object publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class LearningObjectViewer extends LearningObjectPublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		if ($_GET[LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID])
		{
			$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($_GET[LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID]);
			return LearningObjectDisplay :: factory($object)->get_full_html()
				.'<p>'
				.'<a href="'.$this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id()), true).'"><img src="'.api_get_path(WEB_CODE_PATH).'img/publish.gif" alt="'.get_lang('Publish').'" style="vertical-align: middle"/> '.get_lang('PublishThisObject').'</a>'
				. ' '
				.'<a href="'.$this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id(), LearningObjectPublisher :: PARAM_EDIT => 1), true).'"><img src="'.api_get_path(WEB_CODE_PATH).'img/editpublish.gif" alt="'.get_lang('EditAndPublish').'" style="vertical-align: middle"/> '.get_lang('EditAndPublishThisObject').'</a>'
				.'</p>';
		}
	}
}
?>