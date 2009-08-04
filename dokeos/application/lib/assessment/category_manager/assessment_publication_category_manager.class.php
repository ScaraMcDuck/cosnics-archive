<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path() . 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../assessment_data_manager.class.php';
require_once dirname(__FILE__) . '/assessment_publication_category.class.php';

class AssessmentPublicationCategoryManager extends CategoryManager
{

    function AssessmentPublicationCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
    }

    function get_category()
    {
        return new AssessmentPublicationCategory();
    }

    function count_categories($condition)
    {
        $adm = AssessmentDataManager :: get_instance();
        return $adm->count_assessment_publication_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property, $order_direction)
    {
        $adm = AssessmentDataManager :: get_instance();
        return $adm->retrieve_assessment_publication_categories($condition, $offset, $count, $order_property, $order_direction);
    }

    function get_next_category_display_order($parent_id)
    {
        $adm = AssessmentDataManager :: get_instance();
        return $adm->select_next_assessment_publication_category_display_order($parent_id);
    }
}
?>