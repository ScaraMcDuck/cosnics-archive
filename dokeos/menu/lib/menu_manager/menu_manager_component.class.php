<?php
/**
 * @package user.usermanager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class MenuManagerComponent {
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The menumanager in which this component is used
	 */
	private $menumanager;
	/**
	 * The id of this component
	 */
	private $id;
	
	/**
	 * Constructor
	 * @param MenuManager $menumanager The menumanager which
	 * provides this component
	 */
    function MenuManagerComponent($menumanager) {
    	$this->menumanager = $menumanager;
		$this->id =  ++self :: $component_count;
    }
    
	function display_header($breadcrumbtrail = null, $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbtrail, $display_search);
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
	
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	function count_menu_categories($conditions = null)
	{
		return $this->get_parent()->count_menu_categories($conditions);
	}
	
	function count_menu_items($conditions = null)
	{
		return $this->get_parent()->count_menu_items($conditions);
	}
	
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	function get_parent()
	{
		return $this->menumanager;
	}
	
	function get_component_id()
	{
		return $this->id;
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function retrieve_menu_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_menu_categories($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_menu_items($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_menu_items($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_menu_item($id)
	{
		return $this->get_parent()->retrieve_menu_item($id);
	}
	
	function retrieve_menu_item_at_sort($parent, $sort, $direction)
	{
		return $this->get_parent()->retrieve_menu_item_at_sort($parent, $sort, $direction);
	}
	
	function get_parameters($include_search = false)
	{
		return $this->get_parent()->get_parameters($include_search);
	}
	
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	function set_parameter($name, $value)
	{
		$this->get_parent()->set_parameter($name, $value);
	}
	
	function get_url($additional_parameters = array(), $include_search = false, $encode_entities = false)
	{
		return $this->get_parent()->get_url($additional_parameters, $include_search, $encode_entities);
	}
	
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}
	
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		return $this->get_parent()->redirect($type, $message, $error_message, $extra_params);
	}
	
	function is_allowed($right, $locations = array())
	{
		return $this->get_parent()->is_allowed($right, $locations);
	}

	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}

	function not_allowed()
	{
		$this->get_parent()->not_allowed();
	}
	/**
	 * Create a new menumanager component
	 * @param string $type The type of the component to create.
	 * @param MenuManager $menumanager The menu manager in
	 * which the created component will be used
	 */
	static function factory($type, $menu_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'MenuManager'.$type.'Component';
		require_once $filename;
		return new $class($menu_manager);
	}
	
	function get_menu_item_creation_url()
	{
		return $this->get_parent()->get_menu_item_creation_url();
	}
	
	function get_menu_item_editing_url($menu_item)
	{
		return $this->get_parent()->get_menu_item_editing_url($menu_item);
	}
	
	function get_menu_item_deleting_url($menu_item)
	{
		return $this->get_parent()->get_menu_item_deleting_url($menu_item);
	}
	
	function get_menu_item_moving_url($menu_item, $direction)
	{
		return $this->get_parent()->get_menu_item_moving_url($menu_item, $direction);
	}
	
	function get_action()
	{
		return $this->get_parent()->get_action();
	}
}
?>