<?php
require_once Path :: get_application_path() . 'lib/web_application_component.class.php';

abstract class LaikaManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param Laika $laika The laika which
     * provides this component
     */
    protected function LaikaManagerComponent($laika_manager)
    {
        parent :: __construct($laika_manager);
    }

    function get_user_info($user_id)
    {
        return $this->get_parent()->get_user_info($user_id);
    }

    function retrieve_laika_question($id)
    {
        return $this->get_parent()->retrieve_laika_question($id);
    }

    function retrieve_laika_questions($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_laika_questions($condition, $offset, $count, $order_property, $order_direction);
    }

    function has_taken_laika($user)
    {
        return $this->get_parent()->has_taken_laika($user);
    }

    function retrieve_laika_scale($id)
    {
        return $this->get_parent()->retrieve_laika_scale($id);
    }

    function retrieve_laika_scales($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_laika_scales($condition, $offset, $count, $order_property, $order_direction);
    }

    function retrieve_laika_cluster($id)
    {
        return $this->get_parent()->retrieve_laika_cluster($id);
    }

    function retrieve_laika_clusters($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_laika_clusters($condition, $offset, $count, $order_property, $order_direction);
    }

    function retrieve_laika_result($id)
    {
        return $this->get_parent()->retrieve_laika_result($id);
    }

    function retrieve_laika_results($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_laika_results($condition, $offset, $count, $order_property, $order_direction);
    }

    function retrieve_laika_calculated_result($id)
    {
        return $this->get_parent()->retrieve_laika_calculated_result($id);
    }

    function retrieve_laika_calculated_results($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_laika_calculated_results($condition, $offset, $count, $order_property, $order_direction);
    }

    function retrieve_laika_table_calculated_results($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
    {
        return $this->get_parent()->retrieve_laika_table_calculated_results($condition, $offset, $max_objects, $order_by, $order_dir);
    }

    function retrieve_laika_answer($id)
    {
        return $this->get_parent()->retrieve_laika_answer($id);
    }

    function retrieve_laika_answers($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_laika_answers($condition, $offset, $count, $order_property, $order_direction);
    }

    function retrieve_laika_attempt($id)
    {
        return $this->get_parent()->retrieve_laika_attempt($id);
    }

    function retrieve_laika_attempts($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_laika_attempts($condition, $offset, $count, $order_property, $order_direction);
    }

    function count_laika_attempts($condition = null)
    {
        return $this->get_parent()->count_laika_attempts($condition);
    }

    function count_laika_calculated_results($condition = null)
    {
        return $this->get_parent()->count_laika_calculated_results($condition);
    }

    function count_laika_table_calculated_results($condition = null)
    {
        return $this->get_parent()->count_laika_table_calculated_results($condition);
    }

    function get_laika_attempt_viewing_url($laika_attempt)
    {
        return $this->get_parent()->get_laika_attempt_viewing_url($laika_attempt);
    }

    function get_laika_calculated_result_attempt_viewing_url($laika_calculated_result)
    {
        return $this->get_parent()->get_laika_calculated_result_attempt_viewing_url($laika_calculated_result);
    }

    function get_laika_user_viewing_url($user)
    {
        return $this->get_parent()->get_laika_user_viewing_url($user);
    }

    function get_group_statistics_viewing_url($group)
    {
        return $this->get_parent()->get_group_statistics_viewing_url($group);
    }

    function retrieve_laika_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
    {
        return $this->get_parent()->retrieve_laika_users($condition, $offset, $count, $order_property, $order_direction);
    }

    function count_laika_users($condition = null)
    {
        return $this->get_parent()->count_laika_users($condition);
    }
}
?>