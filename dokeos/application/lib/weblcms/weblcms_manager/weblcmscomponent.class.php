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
	
	function redirect($action = null, $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
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
	
	function get_url($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_url($parameters, $encode);
	}
	
	function display_header($breadcrumbs = array ())
	{
		return $this->get_parent()->display_header($breadcrumbs);
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
	
	function load_tools()
	{
		return $this->get_parent()->load_tools();
	}

	static function is_tool_name($name)
	{
		return $this->get_parent()->is_tool_name($name);
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
	
	function retrieve_course_categories($parent = null)
	{
		return $this->get_parent()->retrieve_course_categories($parent);
	}
	
	function retrieve_courses($user = null, $category = null)
	{
		return $this->get_parent()->retrieve_courses($user, $category);
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