<?php
/**
 * @package application.lib.portfolio.publisher
 */
require_once dirname(__FILE__).'/../portfolio_publisher_component.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class PortfolioViewer extends PortfolioPublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		if (Request :: get(PortfolioPublisher :: PARAM_LEARNING_OBJECT_ID))
		{
			$object = RepositoryDataManager :: get_instance()->retrieve_learning_object(Request :: get(PortfolioPublisher :: PARAM_LEARNING_OBJECT_ID));
			$toolbar_data = array();
			$toolbar_data[] = array(
				'href' => $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id())),
				'img' => Theme :: get_common_img_path().'action_publish.png',
				'label' => Translation :: get('Publish'),
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);

			$toolbar = DokeosUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
			return LearningObjectDisplay :: factory($object)->get_full_html().$toolbar;
		}
	}
}
?>
