<?php
/**
 * @package application.lib.portfolio.portfolio_manager
 */
abstract class PortfolioComponent {

	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;
	/**
	 * The portfolio in which this componet is used
	 */
	private $pm;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param Portfolio $pm The portfolio which
	 * provides this component
	 */
	protected function PortfolioComponent($pm) {
		$this->pm = $pm;
		$this->id =  ++self :: $component_count;
	}
	
	/**
	 * @see PortfolioManager :: redirect()
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}

	/**
	 * @see PortfolioManager :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see PortfolioManager :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	/**
	 * @see PortfolioManager :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}
	
	/**
	 * @see PortfolioManager :: get_url()
	 */
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
	}
	/**
	 * @see PortfolioManager :: display_header()
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
	}
	
	/**
	 * @see PortfolioManager :: display_message()
	 */
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}
	
	/**
	 * @see PortfolioManager :: display_error_message()
	 */
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see PortfolioManager :: display_warning_message()
	 */
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}
	
	/**
	 * @see PortfolioManager :: display_footer()
	 */
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}
	
	/**
	 * @see PortfolioManager :: display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see PortfolioManager :: display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	/**
	 * @see PortfolioManager :: display_popup_form
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	/**
	 * @see PortfolioManager :: get_parent
	 */
	function get_parent()
	{
		return $this->pm;
	}
	
	/**
	 * @see PortfolioManager :: get_web_code_path
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	/**
	 * @see PortfolioManager :: count_portfolio_publications
	 */
	function count_portfolio_publications($condition = null)
	{
		return $this->get_parent()->count_portfolio_publications($condition);
	}
	
	/**
	 * @see PortfolioManager :: retrieve_portfolio_publication()
	 */
	function retrieve_portfolio_publication($id)
	{
		return $this->get_parent()->retrieve_portfolio_publication($id);
	}
	
	/**
	 * @see PortfolioManager :: retrieve_portfolio_publication_from_item()
	 */
	function retrieve_portfolio_publication_from_item($item)
	{
		return $this->get_parent()->retrieve_portfolio_publication_from_item($item);
	}

	/**
	 * @see PortfolioManager :: retrieve_portfolio_publications()
	 */
	function retrieve_portfolio_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		return $this->get_parent()->retrieve_portfolio_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
	}
	
	/**
	 * @see PortfolioManager :: get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	/**
	 * @see PortfolioManager :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	/**
	 * @see PortfolioManager :: get_search_condition()
	 */
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	/**
	 * @see PortfolioManager :: get_publication_deleting_url() 
	 */
	function get_publication_deleting_url($portfolio)
	{
		return $this->get_parent()->get_publication_deleting_url($portfolio);
	}
	
	/**
	 * @see PortfolioManager :: get_publication_viewing_url()
	 */
	function get_publication_viewing_url($portfolio)
	{
		return $this->get_parent()->get_publication_viewing_url($portfolio);
	}
	
	/**
	 * @see PortfolioManager :: get_portfolio_creation_url()
	 */
	function get_portfolio_creation_url()
	{
		return $this->get_parent()->get_portfolio_creation_url();
	}
	
	/**
	 * @see PortfolioManager :: get_publication_reply_url()
	 */
	function get_publication_reply_url($portfolio)
	{
		return $this->get_parent()->get_publication_reply_url($portfolio);
	}
	/*
	* Wrapper for Display :: display_not_allowed();
	 */
	function not_allowed()
	{
		Display :: display_not_allowed();
	}
	
	/**
	 * Create a new portfolio component
	 * @param string $type The type of the component to create.
	 * @param Portfolio $pm The pm in
	 * which the created component will be used
	 */
	static function factory($type, $pm)
	{
		$filename = dirname(__FILE__).'/component/'.strtolower($type).'.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'Portfolio'.$type.'Component';
		require_once $filename;
		return new $class($pm);
	}
}
?>