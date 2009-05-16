<?php
/**
 * @package application.weblcms.weblcms_component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
 
abstract class WeblcmsManagerComponent
{
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
	protected function WeblcmsManagerComponent($weblcms) {
		$this->weblcms = $weblcms;
		$this->id =  ++self :: $component_count;
	}
	
	/**
	 * @see WeblcmsManager :: redirect()
	 */	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}
	
	/**
	 * @see WeblcmsManager :: count_courses()
	 */	
	function count_courses($conditions = null)
	{
		return $this->get_parent()->count_courses($conditions);
	}
	
	/**
	 * @see WeblcmsManager :: count_course_categories()
	 */	
	function count_course_categories($conditions = null)
	{
		return $this->get_parent()->count_course_categories($conditions);
	}
	
	/**
	 * @see WeblcmsManager :: count_user_courses()
	 */	
	function count_user_courses($conditions = null)
	{
		return $this->get_parent()->count_user_courses($conditions);
	}
	
	/**
	 * @see WeblcmsManager :: count_course_user_categories()
	 */	
	function count_course_user_categories($conditions = null)
	{
		return $this->get_parent()->count_course_user_categories($conditions);
	}
	
	/**
	 * @see WeblcmsManager :: get_tool_id()
	 */	
	function get_tool_id()
	{
		return $this->get_parent()->get_tool_id();
	}
	
	/**
	 * @see WeblcmsManager :: get_user_id()
	 */	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	/**
	 * @see WeblcmsManager ::  get_user_info()
	 */	
	function get_user_info($user_id)
	{
		return $this->get_parent()->get_user_info($user_id);
	}
	
	/**
	 * @see WeblcmsManager :: get_user()
	 */	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	/**
	 * @see WeblcmsManager :: get_course()
	 */	
	function get_course()
	{
		return $this->get_parent()->get_course();
	}
	
	/**
	 * @see WeblcmsManager :: get_course_id()
	 */	
	function get_course_id()
	{
		return $this->get_parent()->get_course_id();
	}
	
	/**
	 * @see WeblcmsManager :: get_course_groups()
	 */	
	function get_course_groups()
	{
		return $this->get_parent()->get_course_groups();
	}
	
	/**
	 * @see WeblcmsManager :: get_categories()
	 */	
	function get_categories($list = false)
	{
		return $this->get_parent()->get_categories($list);
	}
	
	/**
	 * @see WeblcmsManager :: get_category()
	 */	
	function get_category($id)
	{
		return $this->get_parent()->get_category($id);
	}
	
	/**
	 * @see WeblcmsManager :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see WeblcmsManager :: get_parameters()
	 */	
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	/**
	 * @see WeblcmsManager :: set_parameter()
	 */	
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}
	
	/**
	 * @see WeblcmsManager :: set_tool_class()
	 */	
	function set_tool_class($class)
	{
		return $this->get_parent()->set_tool_class($class);
	}
	
	/**
	 * @see WeblcmsManager :: get_url()
	 */	
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
	}
	
	/**
	 * @see WeblcmsManager :: display_header()
	 */	
	function display_header($breadcrumbtrail, $display_search = false)
	{
		return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}
	
	/**
	 * @see WeblcmsManager :: display_message()
	 */	
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}
	
	/**
	 * @see WeblcmsManager :: display_error_message()
	 */	
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see WeblcmsManager :: display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see WeblcmsManager :: display_warning_message()
	 */	
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}
	
	/**
	 * @see WeblcmsManager :: display_footer()
	 */	
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}
	
	/**
	 * @see WeblcmsManager :: get_registered_tools()
	 */	
	function get_registered_tools()
	{
		return $this->get_parent()->get_registered_tools();
	}
	
	/**
	 * @see WeblcmsManager :: get_registered_sections()
	 */	
	function get_registered_sections()
	{
		return $this->get_parent()->get_registered_sections();
	}
	
	/**
	 * @see WeblcmsManager :: get_registered_tools()
	 */	
	function get_tool_properties($module)
	{
		return $this->get_parent()->get_tool_properties($module);
	}
	
	/**
	 * @see WeblcmsManager :: load_course()
	 */	
	function load_course()
	{
		return $this->get_parent()->load_course();
	}
	
	/**
	 * @see WeblcmsManager :: load_tools()
	 */	
	function load_tools()
	{
		return $this->get_parent()->load_tools();
	}
	
	/**
	 * @see WeblcmsManager :: is_tool_name()
	 */
	static function is_tool_name($name)
	{
		return $this->get_parent()->is_tool_name($name);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_max_sort_value()
	 */	
	function retrieve_max_sort_value($table, $column, $condition = null)
	{
		return $this->get_parent()->retrieve_max_sort_value($table, $column, $condition);
	}
	
	/**
	 * @see WeblcmsManager :: learning_object_is_published()
	 */	
	function learning_object_is_published($object_id)
	{
		return $this->get_parent()->learning_object_is_published($object_id);
	}
	
	/**
	 * @see WeblcmsManager :: any_learning_object_is_published()
	 */
	function any_learning_object_is_published($object_ids)
	{
		return $this->get_parent()->any_learning_object_is_published($object_ids);
	}
	
	/**
	 * @see WeblcmsManager :: get_learning_object_publication_attributes()
	 */	
	function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->get_learning_object_publication_attributes($object_id, $type, $offset, $count, $order_property, $order_direction);
	}
	
	/**
	 * @see WeblcmsManager :: get_learning_object_publication_attribute()
	 */	
	function get_learning_object_publication_attribute($publication_id)
	{
		return $this->get_parent()->get_learning_object_publication_attribute($publication_id);
	}
	
	/**
	 * @see WeblcmsManager :: delete_learning_object_publications()
	 */	
	function delete_learning_object_publications($object_id)
	{
		return $this->get_parent()->delete_learning_object_publications($object_id);
	}
	
	/**
	 * @see WeblcmsManager :: update_learning_object_publication_id()
	 */	
	function update_learning_object_publication_id($publication_attr)
	{
		return $this->get_parent()->update_learning_object_publication_id($publication_attr);
	}
	
	/**
	 * @see WeblcmsManager :: count_publication_attributes()
	 */	
	function count_publication_attributes($type = null, $condition = null)
	{
		return $this->get_parent()->count_publication_attributes($type, $condition);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_categories()
	 */	
	function retrieve_course_categories($conditions = null, $offset = null, $count = null, $orderBy = null, $orderDir = null)
	{
		return $this->get_parent()->retrieve_course_categories($conditions, $offset, $count, $orderBy, $orderDir);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_user_categories()
	 */	
	function retrieve_course_user_categories ($conditions = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_course_user_categories($conditions, $offset, $count, $order_property, $order_direction);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_user_category()
	 */	
	function retrieve_course_user_category ($course_user_category_id, $user_id = null)
	{
		return $this->get_parent()->retrieve_course_user_category($course_user_category_id, $user_id);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_user_category_at_sort()
	 */	
	function retrieve_course_user_category_at_sort($user_id, $sort, $direction)
	{
		return $this->get_parent()->retrieve_course_user_category_at_sort($user_id, $sort, $direction);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course()
	 */	
	function retrieve_course($course_code)
	{
		return $this->get_parent()->retrieve_course($course_code);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_category()
	 */	
	function retrieve_course_category($course_category)
	{
		return $this->get_parent()->retrieve_course_category($course_category);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_user_relation()
	 */	
	function retrieve_course_user_relation($course_code, $user_id)
	{
		return $this->get_parent()->retrieve_course_user_relation($course_code, $user_id);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_user_relation_at_sort()
	 */	
	function retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction)
	{
		return $this->get_parent()->retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_user_relations()
	 */	
	function retrieve_course_user_relations($user_id, $course_user_category)
	{
		return $this->get_parent()->retrieve_course_user_relations($user_id, $course_user_category);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_course_users()
	 */	
	function retrieve_course_users($course)
	{
		return $this->get_parent()->retrieve_course_users($course);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_courses()
	 */	
	function retrieve_courses($user = null, $condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_courses($user, $condition, $offset, $count, $order_property, $order_direction);
	}
	
	/**
	 * @see WeblcmsManager :: retrieve_user_courses()
	 */	
	function retrieve_user_courses($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_user_courses($condition, $offset, $count, $order_property, $order_direction);
	}
	
	/**
	 * @see WeblcmsManager :: get_last_visit_date()
	 */
	function get_last_visit_date($tool = null,$category_id = null)
	{
		return $this->get_parent()->get_last_visit_date($tool,$category_id);
	}
	
	/**
	 * @see WeblcmsManager :: tool_has_new_publications()
	 */
	function tool_has_new_publications($tool)
	{
		return $this->get_parent()->tool_has_new_publications($tool);
	}
	
	/**
	 * @see WeblcmsManager :: get_parent()
	 */	
	function get_parent()
	{
		return $this->weblcms;
	}
	
	/**
	 * @see WeblcmsManager ::  get_course_viewing_url()
	 */	
	function get_course_viewing_url($course)
	{
		return $this->get_parent()->get_course_viewing_url($course);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_editing_url()
	 */	
	function get_course_editing_url($course)
	{
		return $this->get_parent()->get_course_editing_url($course);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_maintenance_url()
	 */	
	function get_course_maintenance_url($course)
	{
		return $this->get_parent()->get_course_maintenance_url($course);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_subscription_url()
	 */	
	function get_course_subscription_url($course)
	{
		return $this->get_parent()->get_course_subscription_url($course);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_unsubscription_url()
	 */	
	function get_course_unsubscription_url($course)
	{
		return $this->get_parent()->get_course_unsubscription_url($course);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_user_category_edit_url()
	 */	
	function get_course_user_category_edit_url($course_user_category)
	{
		return $this->get_parent()->get_course_user_category_edit_url($course_user_category);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_user_category_move_url()
	 */	
	function get_course_user_category_move_url($course_user_category, $direction)
	{
		return $this->get_parent()->get_course_user_category_move_url($course_user_category, $direction);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_user_edit_url()
	 */	
	function get_course_user_edit_url($course_user)
	{
		return $this->get_parent()->get_course_user_edit_url($course_user);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_user_move_url()
	 */	
	function get_course_user_move_url($course_user, $direction)
	{
		return $this->get_parent()->get_course_user_move_url($course_user, $direction);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_user_category_add_url()
	 */	
	function get_course_user_category_add_url()
	{
		return $this->get_parent()->get_course_user_category_add_url();
	}
	
	/**
	 * @see WeblcmsManager :: get_course_user_category_delete_url()
	 */	
	function get_course_user_category_delete_url($course_user_category)
	{
		return $this->get_parent()->get_course_user_category_delete_url($course_user_category);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_category_edit_url()
	 */
	function get_course_category_edit_url($coursecategory)
	{
		return $this->get_parent()->get_course_category_edit_url($coursecategory);
	}
	
	/**
	 * @see WeblcmsManager :: get_course_category_add_url()
	 */	
	function get_course_category_add_url()
	{
		return $this->get_parent()->get_course_category_add_url();
	}
	
	/**
	 * @see WeblcmsManager :: get_course_category_delete_url()
	 */	
	function get_course_category_delete_url($coursecategory)
	{
		return $this->get_parent()->get_course_category_delete_url($coursecategory);
	}
	
	/**
	 * @see WeblcmsManager :: is_subscribed()
	 */
	function is_subscribed($course, $user_id)
	{
		return $this->get_parent()->is_subscribed($course, $user_id);
	}
	
	/**
	 * @see WeblcmsManager :: get_path()
	 */	
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	/**
	 * @see WeblcmsManager :: subscribe_user_to_course()
	 */	
	function subscribe_user_to_course($course, $status, $tutor_id, $user_id)
	{
		return $this->get_parent()->subscribe_user_to_course($course, $status, $tutor_id, $user_id);
	}
	
	/**
	 * @see WeblcmsManager :: unsubscribe_user_from_course()
	 */
	function unsubscribe_user_from_course($course, $user_id)
	{
		return $this->get_parent()->unsubscribe_user_from_course($course, $user_id);
	}
	
	/**
	 * @see WeblcmsManager :: get_search_condition()
	 */
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	/**
	 * @see WeblcmsManager :: get_search_validate()
	 */
	function get_search_validate()
	{
		return $this->get_parent()->get_search_validate();
	}
	
	/**
	 * @see WeblcmsManager :: get_search_parameter()
	 */
	function get_search_parameter($name)
	{
		return $this->get_parent()->get_search_parameter($name);
	}
	
	function get_platform_setting($variable, $application = WeblcmsManager :: APPLICATION_NAME)
	{
		return $this->get_parent()->get_platform_setting($variable, $application);
	}
	
	/**
	 * Create a new weblcms component
	 * @param string $type The type of the component to create.
	 * @param Weblcms $weblcms The weblcms in
	 * which the created component will be used
	 */
	static function factory($type, $weblcms)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'WeblcmsManager'.$type.'Component';
		require_once $filename;
		return new $class($weblcms);
	}

    function get_reporting_url($classname, $params)
    {
        return $this->get_parent()->get_reporting_url($classname, $params);
    }
}
?>