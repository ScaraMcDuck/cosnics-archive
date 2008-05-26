<?php
/**
 * @package application.lib.personal_messenger.publisher
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personalmessagepublishercomponent.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path(). 'lib/repository_utilities.class.php';

/**
 * This class represents a personal message publisher component which can be used
 * to preview a personal message in the personal message publisher.
 */
class PersonalMessageViewer extends PesonalMessagePublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		if ($_GET[PersonalMessagePublisher :: PARAM_LEARNING_OBJECT_ID])
		{
			$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($_GET[PersonalMessagePublisher :: PARAM_LEARNING_OBJECT_ID]);
			$toolbar_data = array();
			$toolbar_data[] = array(
				'href' => $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id())),
				'img' => Theme :: get_common_img_path().'action_publish.png',
				'label' => Translation :: get('Publish'),
				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
//			$toolbar_data[] = array(
//				'href' => $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id(), LearningObjectPublisher :: PARAM_EDIT => 1)),
//				'img' => Theme :: get_common_img_path().'action_editpublish.png',
//				'label' => Translation :: get('EditAndPublish'),
//				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
//			);
			$toolbar = RepositoryUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
			return LearningObjectDisplay :: factory($object)->get_full_html().$toolbar;
		}
	}
}
?>