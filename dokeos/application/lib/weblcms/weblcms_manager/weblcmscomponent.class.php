<?php

abstract class WeblcmsComponent {

	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;
	/**
	 * The weblcms in which this componet is used
	 */
	private $weblcms;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param Weblcms $weblcms The weblcms which
	 * provides this component
	 */
	protected function WeblcmsComponent($weblcms) {
		$this->weblcms = $weblcms;
		$this->id =  ++self :: $component_count;
	}
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}
	
	function count_courses($conditions = null)
	{
		return $this->get_parent()->count_courses($conditions);
	}
	
	function count_course_categories($conditions = null)
	{
		return $this->get_parent()->count_course_categories($conditions);
	}
	
	function count_user_courses($conditions = null)
	{
		return $this->get_parent()->count_user_courses($conditions);
	}
	
	function count_course_user_categories($conditions = null)
	{
		return $this->get_parent()->count_course_user_categories($conditions);
	}
	
	function get_tool_id()
	{
		return $this->get_parent()->get_tool_id();
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function get_course()
	{
		return $this->get_parent()->get_course();
	}
	
	function get_course_id()
	{
		return $this->get_parent()->get_course_id();
	}
	
	function get_groups()
	{
		return $this->get_parent()->get_groups();
	}
	
	function get_categories($list = false)
	{
		return $this->get_parent()->get_categories($list = false);
	}
	
	function get_category($id)
	{
		return $this->get_parent()->get_category($id);
	}

	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}
	
	function set_tool_class($class)
	{
		return $this->get_parent()->set_tool_class($class);
	}
	
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
	}
	
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		return $this->get_parent()->display_header($breadcrumbs, $display_search);
	}
	
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}
	
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}
	
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}
	
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}
	
	function get_registered_tools()
	{
		return $this->get_parent()->get_registered_tools();
	}
	
	function load_course()
	{
		return $this->get_parent()->load_course();
	}
	
	function load_tools()
	{
		return $this->get_parent()->load_tools();
	}

	static function is_tool_name($name)
	{
		return $this->get_parent()->is_tool_name($name);
	}
	
	function retrieve_max_sort_value($table, $column, $condition = null)
	{
		return $this->get_parent()->retrieve_max_sort_value($table, $column, $condition);
	}
	
	function learning_object_is_published($object_id)
	{
		return $this->get_parent()->learning_object_is_published($object_id);
	}

	function any_learning_object_is_published($object_ids)
	{
		return $this->get_parent()->any_learning_object_is_published($object_ids);
	}
	
	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->get_learning_object_publication_attributes($object_id, $type, $offset, $count, $order_property, $order_direction);
	}
	
	function get_learning_object_publication_attribute($publication_id)
	{
		return $this->get_parent()->get_learning_object_publication_attribute($publication_id);
	}
	
	function delete_learning_object_publications($object_id)
	{
		return $this->get_parent()->delete_learning_object_publications($object_id);
	}
	
	function update_learning_object_publication_id($publication_attr)
	{
		return $this->get_parent()->update_learning_object_publication_id($publication_attr);
	}
	
	function count_publication_attributes($type = null, $condition = null)
	{
		return $this->get_parent()->count_publication_attributes($type, $condition);
	}
	
	function retrieve_course_categories($conditions = null, $offset = null, $count = null, $orderBy = null, $orderDir = null)
	{
		return $this->get_parent()->retrieve_course_categories($conditions, $offset, $count, $orderBy, $orderDir);
	}
	
	function retrieve_course_user_categories ($offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_course_user_categories($offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_course_user_category ($course_user_category_id)
	{
		return $this->get_parent()->retrieve_course_user_category($course_user_category_id);
	}
	
	function retrieve_course_user_category_at_sort($user_id, $sort, $direction)
	{
		return $this->get_parent()->retrieve_course_user_category_at_sort($user_id, $sort, $direction);
	}
	
	function retrieve_course($course_code)
	{
		return $this->get_parent()->retrieve_course($course_code);
	}
	
	function retrieve_course_category($course_category_code)
	{
		return $this->get_parent()->retrieve_course_category($course_category_code);
	}
	
	function retrieve_course_user_relation($course_code, $user_id)
	{
		return $this->get_parent()->retrieve_course_user_relation($course_code, $user_id);
	}
	
	function retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction)
	{
		return $this->get_parent()->retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction);
	}
	
	function retrieve_course_user_relations($user_id, $course_user_category)
	{
		return $this->get_parent()->retrieve_course_user_relations($user_id, $course_user_category);
	}
	
	function retrieve_courses($user = null, $category = null, $condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_courses($user, $category, $condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_user_courses($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_user_courses($condition, $offset, $count, $order_property, $order_direction);
	}

	function get_last_visit_date($tool = null,$category_id = null)
	{
		return $this->get_parent()->get_last_visit_date($tool,$category_id);
	}

	function tool_has_new_publications($tool)
	{
		return $this->get_parent()->tool_has_new_publications($tool);
	}
	
	function get_parent()
	{
		return $this->weblcms;
	}
	
	function get_course_viewing_url($course)
	{
		return $this->get_parent()->get_course_viewing_url($course);
	}
	
	function get_course_editing_url($course)
	{
		return $this->get_parent()->get_course_editing_url($course);
	}
	
	function get_course_maintenance_url($course)
	{
		return $this->get_parent()->get_course_maintenance_url($course);
	}
	
	function get_course_subscription_url($course)
	{
		return $this->get_parent()->get_course_subscription_url($course);
	}
	
	function get_course_unsubscription_url($course)
	{
		return $this->get_parent()->get_course_unsubscription_url($course);
	}
	
	function get_course_user_category_edit_url($course_user_category)
	{
		return $this->get_parent()->get_course_user_category_edit_url($course_user_category);
	}
	
	function get_course_user_category_move_url($course_user_category, $direction)
	{
		return $this->get_parent()->get_course_user_category_move_url($course_user_category, $direction);
	}
	
	function get_course_user_edit_url($course_user)
	{
		return $this->get_parent()->get_course_user_edit_url($course_user);
	}
	
	function get_course_user_move_url($course_user, $direction)
	{
		return $this->get_parent()->get_course_user_move_url($course_user, $direction);
	}
	
	function get_course_user_category_add_url()
	{
		return $this->get_parent()->get_course_user_category_add_url();
	}
	
	function get_course_user_category_delete_url($course_user_category)
	{
		return $this->get_parent()->get_course_user_category_delete_url($course_user_category);
	}

	function get_course_category_edit_url($coursecategory)
	{
		return $this->get_parent()->get_course_category_edit_url($coursecategory);
	}
	
	function get_course_category_add_url()
	{
		return $this->get_parent()->get_course_category_add_url();
	}
	
	function get_course_category_delete_url($coursecategory)
	{
		return $this->get_parent()->get_course_category_delete_url($coursecategory);
	}

	function is_subscribed($course)
	{
		return $this->get_parent()->is_subscribed($course);
	}
	
	function get_web_code_path()
	{
		return $this->get_parent()->get_web_code_path();
	}
	
	function subscribe_user_to_course($course, $status, $tutor_id, $user_id)
	{
		return $this->get_parent()->subscribe_user_to_course($course, $status, $tutor_id, $user_id);
	}
	
	function unsubscribe_user_from_course($course)
	{
		return $this->get_parent()->unsubscribe_user_from_course($course);
	}
	
	/**
	 * @see Weblcms::get_search_condition()
	 */
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	/**
	 * @see Weblcms::get_search_validate()
	 */
	function get_search_validate()
	{
		return $this->get_parent()->get_search_validate();
	}
	
	/**
	 * @see Weblcms::get_search_parameter()
	 */
	function get_search_parameter($name)
	{
		return $this->get_parent()->get_search_parameter($name);
	}
	
	/**
	 * Create a new weblcms component
	 * @param string $type The type of the component to create.
	 * @param Weblcms $weblcms The weblcms in
	 * which the created component will be used
	 */
	static function factory($type, $weblcms)
	{
		$filename = dirname(__FILE__).'/component/'.strtolower($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'Weblcms'.$type.'Component';
		require_once $filename;
		return new $class($weblcms);
	}
}
?>