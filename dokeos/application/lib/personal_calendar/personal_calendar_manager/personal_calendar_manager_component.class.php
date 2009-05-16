<?php
/**
 * @package application.lib.calendar.calendar_manager
 */
abstract class PersonalCalendarManagerComponent {

	/**
	 * The number of components allready instantiated
	 */
	private static $component_count = 0;
	/**
	 * The calendar in which this componet is used
	 */
	private $pm;
	/**
	 * The id of this component
	 */
	private $id;
	/**
	 * Constructor
	 * @param Calendar $pm The calendar which
	 * provides this component
	 */
	protected function PersonalCalendarManagerComponent($pm) {
		$this->pm = $pm;
		$this->id =  ++self :: $component_count;
	}
	
	/**
	 * @see CalendarManager :: redirect()
	 */
	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
	}
	
	function is_allowed($right, $locations = array())
	{
		return $this->get_parent()->is_allowed($right, $locations);
	}

	/**
	 * @see CalendarManager :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->get_parent()->get_parameter($name);
	}
	
	/**
	 * @see CalendarManager :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}
	
	/**
	 * @see CalendarManager :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		return $this->get_parent()->set_parameter($name, $value);
	}
	
	/**
	 * @see CalendarManager :: get_url()
	 */
	function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
	{
		return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
	}
	
	function get_link($parameters = array (), $encode = false)
	{
		return $this->get_parent()->get_link($parameters, $encode);
	}
	
	/**
	 * @see CalendarManager :: display_header()
	 */
	function display_header($breadcrumbs = array (), $display_search = false)
	{
		return $this->get_parent()->display_header($breadcrumbs, $display_search);
	}
	
	/**
	 * @see CalendarManager :: display_message()
	 */
	function display_message($message)
	{
		return $this->get_parent()->display_message($message);
	}
	
	/**
	 * @see CalendarManager :: display_error_message()
	 */
	function display_error_message($message)
	{
		return $this->get_parent()->display_error_message($message);
	}
	
	/**
	 * @see CalendarManager :: display_warning_message()
	 */
	function display_warning_message($message)
	{
		return $this->get_parent()->display_warning_message($message);
	}
	
	/**
	 * @see CalendarManager :: display_footer()
	 */
	function display_footer()
	{
		return $this->get_parent()->display_footer();
	}
	
	/**
	 * @see CalendarManager :: display_error_page()
	 */
	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}
	
	/**
	 * @see CalendarManager :: display_warning_page()
	 */
	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}
	
	/**
	 * @see CalendarManager :: display_popup_form
	 */
	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}
	
	/**
	 * @see CalendarManager :: get_parent
	 */
	function get_parent()
	{
		return $this->pm;
	}
	
	/**
	 * @see CalendarManager :: get_web_code_path
	 */
	function get_path($path_type)
	{
		return $this->get_parent()->get_path($path_type);
	}
	
	function retrieve_calendar_event_publication($publication_id)
	{
		return $this->get_parent()->retrieve_calendar_event_publication($publication_id);
	}
	
	function get_events($from_date,$to_date)
	{
		return $this->get_parent()->get_events($from_date,$to_date);
	}
	
	/**
	 * @see CalendarManager :: get_user()
	 */
	function get_user()
	{
		return $this->get_parent()->get_user();
	}
	
	/**
	 * @see CalendarManager :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	/**
	 * @see CalendarManager :: get_search_condition()
	 */
	function get_search_condition()
	{
		return $this->get_parent()->get_search_condition();
	}
	
	/**
	 * @see CalendarManager :: get_publication_deleting_url() 
	 */
	function get_publication_deleting_url($publication)
	{
		return $this->get_parent()->get_publication_deleting_url($publication);
	}
	
	/**
	 * @see CalendarManager :: get_publication_editing_url() 
	 */
	function get_publication_editing_url($publication)
	{
		return $this->get_parent()->get_publication_editing_url($publication);
	}
	
	/**
	 * @see CalendarManager :: get_publication_viewing_url()
	 */
	function get_publication_viewing_url($calendar)
	{
		return $this->get_parent()->get_publication_viewing_url($calendar);
	}
	
	function get_publication_viewing_link($calendar)
	{
		return $this->get_parent()->get_publication_viewing_link($calendar);
	}
	
	/**
	 * @see CalendarManager :: get_calendar_creation_url()
	 */
	function get_calendar_creation_url()
	{
		return $this->get_parent()->get_calendar_creation_url();
	}
	
	/**
	 * @see CalendarManager :: get_publication_reply_url()
	 */
	function get_publication_reply_url($calendar)
	{
		return $this->get_parent()->get_publication_reply_url($calendar);
	}
	
	function force_menu_url($url)
	{
		return $this->get_parent()->force_menu_url($url);
	}
	
	/**
	 * Create a new calendar component
	 * @param string $type The type of the component to create.
	 * @param Calendar $pm The pm in
	 * which the created component will be used
	 */
	static function factory($type, $pm)
	{
		$filename = dirname(__FILE__).'/component/'.DokeosUtilities :: camelcase_to_underscores($type).'.class.php'; 
		if (!file_exists($filename) || !is_file($filename)) 
		{
			die('Failed to load "'.$type.'" component');
		}
		$class = 'PersonalCalendarManager'.$type.'Component';
		require_once $filename;
		return new $class($pm);
	}
	
	function display_light_header($object = null)
	{
		return $this->get_parent()->display_light_header($object);
	}
	
	function display_light_footer()
	{
		return $this->get_parent()->display_light_footer();
	}
	
	function get_publication_attachment_viewing_url($calendar)
	{
		return $this->get_parent()->get_publication_attachment_viewing_url($calendar);
	}
}
?>