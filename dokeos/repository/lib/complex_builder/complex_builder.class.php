<?php

/**
 * This class represents a basic complex builder structure. 
 * When a builder is needed for a certain type of complex learning object an extension should be written.
 * We will make use of the repoviewer for selection, creation of learning objects
 * 
 * @author vanpouckesven
 *
 */
abstract class ComplexBuilder
{
	const PARAM_BUILDER_ACTION = 'builder_action';
	const PARAM_ROOT_LO = 'root_lo';
	const PARAM_CURRENT_LO = 'current_lo';
	const PARAM_CLOI_ID = 'cloi';
	const PARAM_DELETE_SELECTED_CLOI = 'delete_selected_cloi';
	const PARAM_MOVE_SELECTED_CLOI = 'move_selected_cloi';
	
	const ACTION_DELETE_CLOI = 'delete_cloi';
	const ACTION_UPDATE_CLOI = 'update_cloi';
	const ACTION_CREATE_CLOI = 'create_cloi';
	const ACTION_MOVE_CLOI = 'move_cloi';
	const ACTION_BROWSE_CLO = 'browse';
	
	//Singleton
	private static $instance;
	
	static function get_instance($type)
	{
		if(is_null(self :: $instance))
		{
			$small_type = DokeosUtilities :: camelcase_to_underscores($type); 
			$file = dirname(__FILE__) . '/' . $small_type . '_builder/' . $small_type . '_builder.class.php'; 
			require_once $file;
			$class = $type . 'Builder';
			self :: $instance = new $class;
		}
		
		return self :: $instance;
	}
	
	// This run method handles the basic functionality like editing of lo's, deleting of lo wrappers, organising lo wrappers..
	function run()
	{
		$action = $this->get_parameters(self :: PARAM_BUILDER_ACTION);
		switch($action)
		{
			//case ACTION_CREATE_CLOI : 
		}
	}
	
	private $parent;
	
	function ComplexBuilder($parent)
	{
		$this->parent = $parent;
	}
	
	function get_parent()
	{
		return $this->parent;
	} 
	
	function set_parent($parent)
	{
		$this->parent = $parent;
	}
	
	function set_parameter($parameter, $value)
	{
		$this->get_parent()->set_parameter($parameter, $value);
	}
	
	function get_parameter($parammeter)
	{
		return $this->get_parent()->get_parameter($parameter);
	}
	
	function display_header($breadcrumbtrail)
	{
		$this->get_parent()->display_header($breadcrumbtrail);
	}
	
	function display_footer()
	{
		$this->get_parent()->display_footer();
	}
	
	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}

	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}
	
	function display_warning_message($message)
	{
		$this->get_parent()->display_warning_message($message);
	}

	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}

	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}

	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	function redirect($action = self :: ACTION_BROWSE_LEARNING_OBJECTS, $message = null, $error_message = false, $extra_params = null)
	{
		$this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}
	
	function get_url($additional_parameters = array ())
	{
		return $this->get_parent()->get_url($additional_parameters);
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
}

?>