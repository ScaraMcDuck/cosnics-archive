<?php

require_once dirname(__FILE__) . '/complex_builder_component.class.php';
require_once dirname(__FILE__) . '/../repository_manager/component/complex_browser/complex_browser_table.class.php';

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
	const PARAM_TYPE = 'type';
	
	const ACTION_DELETE_CLOI = 'delete_cloi';
	const ACTION_UPDATE_CLOI = 'update_cloi';
	const ACTION_CREATE_CLOI = 'create_cloi';
	const ACTION_MOVE_CLOI = 'move_cloi';
	const ACTION_BROWSE_CLO = 'browse';
	
	function ComplexBuilder($parent)
	{
		$this->parent = $parent;
		$action = Request :: get(self :: PARAM_BUILDER_ACTION);
		
		if(!$action)
			$action = self :: ACTION_BROWSE_CLO;

		$this->set_action($action);
	}
	
	//Singleton
	private static $instance;
	
	static function factory($parent)
	{
		if(is_null(self :: $instance))
		{
			$root_lo = Request :: get(self :: PARAM_ROOT_LO);
			if($root_lo)
			{
				$type = RepositoryDataManager :: get_instance()->determine_learning_object_type($root_lo);
				$small_type = DokeosUtilities :: camelcase_to_underscores($type); 
				$file = dirname(__FILE__) . '/' . $small_type . '/' . $small_type . '_builder.class.php'; 
				require_once $file;
				$class = $type . 'Builder';
				self :: $instance = new $class($parent);
			}
		}
		
		return self :: $instance;
	}
	
	// This run method handles the basic functionality like editing of lo's, deleting of lo wrappers, organising lo wrappers..
	function run()
	{
		$action = $this->get_action();
		switch($action)
		{
			case self :: ACTION_CREATE_CLOI :
				$component = ComplexBuilderComponent :: factory(null, 'Creator', $this);
				break;
			case self :: ACTION_DELETE_CLOI :
				$component = ComplexBuilderComponent :: factory(null, 'Deleter', $this);
				break;
			case self :: ACTION_UPDATE_CLOI :
				$component = ComplexBuilderComponent :: factory(null, 'Updater', $this);
				break;
			case self :: ACTION_MOVE_CLOI :
				$component = ComplexBuilderComponent :: factory(null, 'Mover', $this);
				break;
			case self :: ACTION_BROWSE_CLO :
				$component = ComplexBuilderComponent :: factory(null, 'Browser', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_CLO);
				$component = ComplexBuilderComponent :: factory(null, 'Browser', $this);
		} 
		
		$component->run();
	}
	
	private $parent;
	
	function get_action()
	{
		return $this->get_parameter(self :: PARAM_BUILDER_ACTION);
	}
	
	function set_action($action)
	{
		$this->set_parameter(self :: PARAM_BUILDER_ACTION, $action);
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
	
	function get_parameter($parameter)
	{
		return $this->get_parent()->get_parameter($parameter);
	}
	
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	function display_header($breadcrumbtrail)
	{
		$this->get_parent()->display_header($breadcrumbtrail, false, false);
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
	
	function redirect($action, $message = null, $error_message = false, $extra_params = null)
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
	
	/**
	 * Common functionality
	 */
	
	function get_clo_table_html($show_subitems_column = true)
	{
		$table = new ComplexBrowserTable($this, $this->get_parameters(), $this->get_clo_table_condition(), $show_subitems_column);
		return $table->as_html();
	}
	
	private function get_clo_table_condition()
	{
		$cloi_id = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
		if(isset($cloi_id))
		{
			$cloi = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($cloi_id);
			return new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $cloi->get_ref());
		}
		$root_lo = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
		return new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $root_lo);
	}
	
	function get_clo_tree_structure()
	{
		
	}
}

?>