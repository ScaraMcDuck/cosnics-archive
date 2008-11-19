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
abstract class PublisherMultipublisherComponent extends PublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		return $this->get_publications_form();
	}
	
	abstract function get_publications_form();
}
?>