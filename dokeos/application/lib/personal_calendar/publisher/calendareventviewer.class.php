<?php
/**
 * @package application.lib.calendareventr.publisher
 */
require_once dirname(__FILE__).'/../calendareventpublishercomponent.class.php';
require_once Path :: get_repository_path(). 'lib/repositorydatamanager.class.php';
require_once Path :: get_repository_path(). 'lib/learningobjectdisplay.class.php';
require_once Path :: get_repository_path(). 'lib/repositoryutilities.class.php';

/**
 * This class represents a calendarevent publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class CalendarEventViewer extends PesonalMessagePublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		if ($_GET[CalendarEventPublisher :: PARAM_LEARNING_OBJECT_ID])
		{
			$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($_GET[CalendarEventPublisher :: PARAM_LEARNING_OBJECT_ID]);
			$toolbar_data = array();
			$toolbar_data[] = array(
				'href' => $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id())),
				'img' => Theme :: get_common_img_path().'publish.png',
				'label' => Translation :: get('Publish'),
				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
//			$toolbar_data[] = array(
//				'href' => $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id(), LearningObjectPublisher :: PARAM_EDIT => 1)),
//				'img' => Theme :: get_common_img_path().'action-editpublish.png',
//				'label' => Translation :: get('EditAndPublish'),
//				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
//			);
			$toolbar = RepositoryUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
			return LearningObjectDisplay :: factory($object)->get_full_html().$toolbar;
		}
	}
}
?>