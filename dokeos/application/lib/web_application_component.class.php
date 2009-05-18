<?php

class WebApplicationComponent
{
	/**
	 * The application manager in which this component is used
	 */
	private $manager;

	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;

	/**
	 * The id of the component
	 */
	private $id;

	/**
	 * The WebApplicationComponent constructor
	 */
    function WebApplicationComponent($manager)
    {
    	$this->manager = $manager;
    	$this->id =  ++self :: $component_count;
    }

	/**
	 * @return WebApplication $manager The web application
	 */
	function get_parent()
	{
		return $this->manager;
	}

	/**
	 * @see WebApplication :: simple_redirect()
	 */
	function simple_redirect($parameters = array (), $filter = array(), $encode_entities = false, $type = Redirect :: TYPE_URL)
	{
		return $this->get_parent()->simple_redirect($parameters, $filter, $encode_entities, $type);
	}

	/**
	 * @see WebApplication :: redirect()
	 */
	function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $type = Redirect :: TYPE_URL)
	{
		return $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities, $type);
	}

	/**
	 * @see WebApplication :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}

	/**
	 * @see WebApplication :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}

	/**
	 * @see WebApplication :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}

	/**
	 * @see WebApplication :: get_url()
	 */
	function get_url($parameters = array (), $filter = array(), $encode_entities = false)
	{
		return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
	}

	/**
	 * @see WebApplication :: get_link()
	 */
	function get_link($parameters = array (), $filter = array(), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $filter, $encode);
	}

	/**
	 * @see WebApplication :: display_header()
	 */
	function display_header($breadcrumbs = array ())
	{
		return $this->get_parent()->display_header($breadcrumbs);
	}

	/**
	 * @see WebApplication :: display_message()
	 */
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}

	/**
	 * @see WebApplication :: display_error_message()
	 */
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}

	/**
	 * @see WebApplication :: display_warning_message()
	 */
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}

	/**
	 * @see WebApplication :: display_footer()
	 */
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}

	/**
	 * @see WebApplication :: display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}

	/**
	 * @see WebApplication :: display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}

	/**
	 * @see WebApplication :: display_popup_form
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}

	/**
	 * @see WebApplication :: get_path
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}

	/**
	 * @see WebApplication :: get_platform_setting
	 */
	function get_platform_setting($variable)
	{
		return $this->get_parent()->get_platform_setting($variable);
	}

	/**
	 * @see WebApplication :: get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * @see WebApplication :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	/**
	 * Create a new calendar component
	 * @param string $type The type of the component to create.
	 * @param Calendar $pm The pm in
	 * which the created component will be used
	 */
	static function factory($type, $manager)
	{
		$application_name = $manager->get_application_name();
		$application_class = Application :: application_to_class($application_name);

		$file_path = Path :: get_application_path() . '/lib/' . $application_name . '/' . $application_name . '_manager/component/';
		$file_name = DokeosUtilities :: camelcase_to_underscores($type) . '.class.php';
		$full_path = $file_path . $file_name;

		if (!file_exists($full_path) || !is_file($full_path))
		{
			$message = Translation :: get('ComponentFailedToLoad'). ': ';
			$message .= Translation :: get($application_class) . ' ==> ';
			$message .= Translation :: get($type);
			Display :: error_message($message);
		}

		$class = $application_class . 'Manager' . $type . 'Component';
		require_once $full_path;
		return new $class($manager);
	}
}
?>