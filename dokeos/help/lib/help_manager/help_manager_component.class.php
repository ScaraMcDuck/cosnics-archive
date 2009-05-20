<?php
/**
 * @package user.helpsmanager
 */
require_once Path :: get_library_path() . 'core_application_component.class.php';
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */
abstract class HelpManagerComponent extends CoreApplicationComponent
{
	/**
	 * Constructor
	 * @param HelpsManager $helps_manager The help manager which
	 * provides this component
	 */
    function HelpManagerComponent($help_manager)
    {
        parent :: __construct($help_manager);
    }

	public function count_help_items($condition)
	{
		return $this->get_parent()->count_help_items($condition);
	}

	public function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_help_items($condition, $offset , $count, $order_property, $order_direction);
	}

	public function retrieve_help_item($name, $language)
	{
		return $this->get_parent()->retrieve_help_item($name, $language);
	}
}
?>