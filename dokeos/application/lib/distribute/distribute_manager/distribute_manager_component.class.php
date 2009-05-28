<?php
/**
 * @package application.lib.distribute.distribute_manager
 * Basic functionality of a component to talk with the distribute application
 * @author Hans De Bisschop
 */
abstract class DistributeManagerComponent
{
	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;

	/**
	 * The distribute in which this componet is used
	 */
	private $distribute;

	/**
	 * The id of this component
	 */
	private $id;

	/**
	 * Constructor
	 * @param Distribute $distribute The distribute which
	 * provides this component
	 */
	protected function DistributeManagerComponent($distribute)
	{
		$this->pm = $distribute;
		$this->id =  ++self :: $component_count;
	}

	/**
	 * @see DistributeManager :: redirect()
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}

	/**
	 * @see DistributeManager :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}

	/**
	 * @see DistributeManager :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}

	/**
	 * @see DistributeManager :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}

	/**
	 * @see DistributeManager :: get_url()
	 */
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
	}
	/**
	 * @see DistributeManager :: display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}

	/**
	 * @see DistributeManager :: display_message()
	 */
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}

	/**
	 * @see DistributeManager :: display_error_message()
	 */
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}

	/**
	 * @see DistributeManager :: display_warning_message()
	 */
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}

	/**
	 * @see DistributeManager :: display_footer()
	 */
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}

	/**
	 * @see DistributeManager :: display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}

	/**
	 * @see DistributeManager :: display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}

	/**
	 * @see DistributeManager :: display_popup_form
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}

	/**
	 * @see DistributeManager :: get_parent
	 */
	function get_parent()
	{
		return $this->pm;
	}

	/**
	 * @see DistributeManager :: get_web_code_path
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}

	/**
	 * @see DistributeManager :: get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * @see DistributeManager :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	//Data Retrieval

	function count_distribute_publications($condition)
	{
		return $this->get_parent()->count_distribute_publications($condition);
	}

	function retrieve_distribute_publications($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_distribute_publications($condition, $offset, $count, $order_property, $order_direction);
	}

 	function retrieve_distribute_publication($id)
	{
		return $this->get_parent()->retrieve_distribute_publication($id);
	}

	// Url Creation

	function get_create_distribute_publication_url()
	{
		return $this->get_parent()->get_create_distribute_publication_url();
	}

	function get_update_distribute_publication_url($distribute_publication)
	{
		return $this->get_parent()->get_update_distribute_publication_url($distribute_publication);
	}

 	function get_delete_distribute_publication_url($distribute_publication)
	{
		return $this->get_parent()->get_delete_distribute_publication_url($distribute_publication);
	}

	function get_browse_distribute_publications_url()
	{
		return $this->get_parent()->get_browse_distribute_publications_url();
	}


	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

	/**
	 * Create a new profile component
	 * @param string $type The type of the component to create.
	 * @param Profile $distribute The pm in
	 * which the created component will be used
	 */
	static function factory($type, $distribute)
	{
		$filename = dirname(__FILE__).'/component/' . DokeosUtilities :: camelcase_to_underscores($type) . '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'DistributeManager'.$type.'Component';
		require_once $filename;
		return new $class($distribute);
	}
}
?>