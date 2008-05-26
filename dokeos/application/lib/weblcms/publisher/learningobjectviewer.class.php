<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage publisher
 */
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path(). 'lib/repository_utilities.class.php';

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
			$toolbar_data = array();
			$toolbar_data[] = array(
				'href' => $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id())),
				'img' => Theme :: get_common_img_path().'action_publish.png',
				'label' => Translation :: get('Publish'),
				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
			$toolbar_data[] = array(
				'href' => $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id(), LearningObjectPublisher :: PARAM_EDIT => 1)),
				'img' => Theme :: get_common_img_path().'action_editpublish.png',
				'label' => Translation :: get('EditAndPublish'),
				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
			$toolbar = RepositoryUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
			return LearningObjectDisplay :: factory($object)->get_full_html().$toolbar;
		}
	}
}
?>