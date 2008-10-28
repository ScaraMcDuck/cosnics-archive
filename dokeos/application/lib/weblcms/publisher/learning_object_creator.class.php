<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_application_library_path() . 'publisher/component/creator.class.php';
/**
 * This class represents a profile publisher component which can be used
 * to create a new learning object before publishing it.
 */
class LearningObjectPublisherCreatorComponent extends PublisherCreatorComponent
{
	function LearningObjectPublisherCreatorComponent($parent)
	{
		parent :: __construct($parent);
	}
}

?>