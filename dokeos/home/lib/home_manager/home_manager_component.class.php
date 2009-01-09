<?php
/**
 * @package user.usermanager
 */
/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class HomeManagerComponent {
	/**
	 * The number of components already instantiated
	 */
	private static $component_count = 0;
	/**
	 * The homemanager in which this component is used
	 */
	private $homemanager;
	/**
	 * The id of this component
	 */
	private $id;
	
	/**
	 * Constructor
	 * @param MenuManager $menumanager The menumanager which
	 * provides this component
	 */
    function HomeManagerComponent($homemanager) {
    	$this->homemanager = $homemanager;
		$this->id =  ++self :: $component_count;
    }
    
	function display_header($breadcrumbtrail = null, $display_search = false)
	{
		$this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}
	
	function display_portal_header()
	{
		$this->get_parent()->display_portal_header();
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
	
	function display_warning_message($message)
	{
		$this->get_parent()->display_warning_message($message);
	}
	
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	function count_home_rows($conditions = null)
	{
		return $this->get_parent()->count_home_rows($conditions);
	}
	
	function count_home_columns($conditions = null)
	{
		return $this->get_parent()->count_home_columns($conditions);
	}
	
	function count_home_blocks($conditions = null)
	{
		return $this->get_parent()->count_home_blocks($conditions);
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
		return $this->homemanager;
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
	
	function retrieve_home_tabs($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_home_tabs($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_home_rows($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_home_rows($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_home_columns($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_home_columns($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_home_blocks($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_home_blocks($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function retrieve_home_block($id)
	{
		return $this->get_parent()->retrieve_home_block($id);
	}
	
	function retrieve_home_column($id)
	{
		return $this->get_parent()->retrieve_home_column($id);
	}
	
	function retrieve_home_row($id)
	{
		return $this->get_parent()->retrieve_home_row($id);
	}
	
	function retrieve_home_tab($id)
	{
		return $this->get_parent()->retrieve_home_tab($id);
	}
	
	function retrieve_home_block_config($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_home_block_config($condition, $offset, $count, $order_property, $order_direction);
	}
	
	function truncate_home($user_id)
	{
		return $this->get_parent()->truncate_home($user_id);
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
	static function factory($type, $home_manager)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'HomeManager'.$type.'Component';
		require_once $filename;
		return new $class($home_manager);
	}
	
	function get_home_row_editing_url($home_row)
	{
		return $this->get_parent()->get_home_row_editing_url($home_row);
	}
	
	function get_home_column_editing_url($home_column)
	{
		return $this->get_parent()->get_home_column_editing_url($home_column);
	}
	
	function get_home_block_editing_url($home_block)
	{
		return $this->get_parent()->get_home_block_editing_url($home_block);
	}
	
	function get_home_block_configuring_url($home_block)
	{
		return $this->get_parent()->get_home_block_configuring_url($home_block);
	}
	
	function get_home_row_creation_url()
	{
		return $this->get_parent()->get_home_row_creation_url();
	}
	
	function get_home_column_creation_url()
	{
		return $this->get_parent()->get_home_column_creation_url();
	}
	
	function get_home_block_creation_url()
	{
		return $this->get_parent()->get_home_block_creation_url();
	}
	
	function get_home_row_deleting_url($home_row)
	{
		return $this->get_parent()->get_home_row_deleting_url($home_row);
	}
	
	function get_home_column_deleting_url($home_column)
	{
		return $this->get_parent()->get_home_column_deleting_url($home_column);
	}
	
	function get_home_block_deleting_url($home_block)
	{
		return $this->get_parent()->get_home_block_deleting_url($home_block);
	}
	
	function get_home_row_moving_url($home_row, $index)
	{
		return $this->get_parent()->get_home_row_moving_url($home_row, $index);
	}
	
	function get_home_tab_viewing_url($home_tab)
	{
		return $this->get_parent()->get_home_tab_viewing_url($home_tab);
	}
	
	function get_home_block_moving_url($home_block, $index)
	{
		return $this->get_parent()->get_home_block_moving_url($home_block, $index);
	}
	
	function get_home_column_moving_url($home_column, $index)
	{
		return $this->get_parent()->get_home_column_moving_url($home_column, $index);
	}
	
	function retrieve_home_block_at_sort($parent, $sort, $direction)
	{
		return $this->get_parent()->retrieve_home_block_at_sort($parent, $sort, $direction);
	}
	
	function retrieve_home_column_at_sort($parent, $sort, $direction)
	{
		return $this->get_parent()->retrieve_home_column_at_sort($parent, $sort, $direction);
	}
	
	function retrieve_home_row_at_sort($sort, $direction)
	{
		return $this->get_parent()->retrieve_home_row_at_sort($sort, $direction);
	}
	
	function retrieve_home_tab_at_sort($sort, $direction)
	{
		return $this->get_parent()->retrieve_home_tab_at_sort($sort, $direction);
	}
	
	function get_platform_setting($variable, $application = UserManager :: APPLICATION_NAME)
	{
		return $this->get_parent()->get_platform_setting($variable, $application);
	}
}
?>