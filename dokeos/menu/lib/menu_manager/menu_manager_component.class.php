<?php
/**
 * @package user.usermanager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */
require_once Path :: get_library_path() . 'core_application_component.class.php';

abstract class MenuManagerComponent extends CoreApplicationComponent
{

    /**
     * Constructor
     * @param MenuManager $menumanager The menumanager which
     * provides this component
     */
    function MenuManagerComponent($menu_manager)
    {
        parent :: __construct($menu_manager);
    }

    function count_menu_categories($conditions = null)
    {
        return $this->get_parent()->count_menu_categories($conditions);
    }

    function count_navigation_items($conditions = null)
    {
        return $this->get_parent()->count_navigation_items($conditions);
    }

    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    function retrieve_menu_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_menu_categories($condition, $offset, $count, $order_property, $order_direction);
    }

    function retrieve_navigation_items($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_navigation_items($condition, $offset, $count, $order_property, $order_direction);
    }

    function retrieve_navigation_item($id)
    {
        return $this->get_parent()->retrieve_navigation_item($id);
    }

    function retrieve_navigation_item_at_sort($parent, $sort, $direction)
    {
        return $this->get_parent()->retrieve_navigation_item_at_sort($parent, $sort, $direction);
    }

    function is_allowed($right, $locations = array())
    {
        return $this->get_parent()->is_allowed($right, $locations);
    }

    function get_navigation_item_creation_url()
    {
        return $this->get_parent()->get_navigation_item_creation_url();
    }

    function get_navigation_item_editing_url($navigation_item)
    {
        return $this->get_parent()->get_navigation_item_editing_url($navigation_item);
    }

    function get_navigation_item_deleting_url($navigation_item)
    {
        return $this->get_parent()->get_navigation_item_deleting_url($navigation_item);
    }

    function get_navigation_item_moving_url($navigation_item, $direction)
    {
        return $this->get_parent()->get_navigation_item_moving_url($navigation_item, $direction);
    }
}
?>