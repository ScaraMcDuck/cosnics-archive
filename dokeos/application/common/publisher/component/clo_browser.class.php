<?php
/**
 * @package application.lib.encyclopedia.publisher
 */
require_once dirname(__FILE__).'/../publisher.class.php';
require_once dirname(__FILE__).'/../publisher_component.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repository_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learning_object_display.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/complex_learning_object_item_form.class.php';
require_once dirname(__FILE__).'/../../../../common/dokeos_utilities.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a encyclopedia publisher component which can be used
 * to create a new learning object before publishing it.
 */
class PublisherCLOBrowserComponent extends PublisherComponent
{
	
}
?>