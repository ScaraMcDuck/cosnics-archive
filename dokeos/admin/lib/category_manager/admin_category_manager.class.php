<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path() . 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../admin_data_manager.class.php';
require_once dirname(__FILE__) . '/admin_category.class.php';

class AdminCategoryManager extends CategoryManager
{

    function AdminCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new AdminCategory();
    }

    function count_categories($condition)
    {
        $wdm = AdminDataManager :: get_instance();
        return $wdm->count_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property, $order_direction)
    {
        $wdm = AdminDataManager :: get_instance();
        return $wdm->retrieve_categories($condition, $offset, $count, $order_property, $order_direction);
    }

    function get_next_category_display_order($parent_id)
    {
        $wdm = AdminDataManager :: get_instance();
        return $wdm->select_next_display_order($parent_id);
    }
}
?>