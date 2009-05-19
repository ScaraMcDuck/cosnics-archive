<?php
require_once Path :: get_library_path() . 'application.class.php';

abstract class CoreApplication extends Application
{
    /**
     *
     * @see Application::is_active()
     */
    function is_active($application)
    {
        return true;
    }

	/**
	 * Gets a link to the personal calendar application
	 * @param array $parameters
	 * @param boolean $encode
	 */
	public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_CORE)
	{
	    return parent :: get_link($parameters, $filter, $encode_entities, $application_type);
	}

	/**
	 * @see Application :: simple_redirect
	 */
	function simple_redirect($parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_CORE)
	{
	    return parent :: simple_redirect($parameters, $filter, $encode_entities, $redirect_type, $application_type);
	}

	/**
	 * @see Application :: redirect
	 */
	function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_CORE)
	{
		return parent :: redirect($message, $error_message, $parameters, $filter, $encode_entities, $redirect_type, $application_type);
	}

    public function get_application_path($application_name)
    {
        return Path :: get(SYS_PATH) . $application_name . '/';
    }

    public function get_application_component_path()
    {
        $application_name = $this->get_application_name();
        return $this->get_application_path($application_name) . 'lib/' . $application_name . '_manager/component/';
    }

	function factory($application, $user = null)
	{
	    $manager_path = self :: get_application_path($application) . 'lib/' . $application . '_manager/' . $application . '_manager.class.php';
	    require_once $manager_path;
	    return parent :: factory($application, $user);
	}
}

?>