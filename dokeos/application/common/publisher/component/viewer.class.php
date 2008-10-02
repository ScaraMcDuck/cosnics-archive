<?php
/**
 * @package application.lib.encyclopedia.publisher
 */
require_once dirname(__FILE__).'/../publisher_component.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repository_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learning_object_display.class.php';
require_once dirname(__FILE__).'/../../../../common/dokeos_utilities.class.php';

/**
 * This class represents a encyclopedia publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class PublisherViewerComponent extends PublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		if ($_GET[Publisher :: PARAM_ID])
		{
			$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($_GET[Publisher :: PARAM_ID]);
			$toolbar_data = array();
			$toolbar_data[] = array(
				'href' => $this->get_url(array_merge($this->get_extra_parameters(), array (Publisher :: PARAM_ACTION => 'publicationcreator', Publisher :: PARAM_ID => $learning_object->get_id()))),
				'img' => Theme :: get_common_img_path().'action_publish.png',
				'label' => Translation :: get('Publish'),
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
			$toolbar_data[] = array(
				'href' => $this->get_url(array_merge($this->get_extra_parameters(), array (Publisher :: PARAM_ACTION => 'publicationcreator', Publisher :: PARAM_ID => $learning_object->get_id(), ObjectPublisher :: PARAM_EDIT => 1))),
				'img' => Theme :: get_common_img_path().'action_editpublish.png',
				'label' => Translation :: get('EditAndPublish'),
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
			$toolbar = DokeosUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
			return LearningObjectDisplay :: factory($learning_object)->get_full_html().$toolbar;
		}
	}
}
?>