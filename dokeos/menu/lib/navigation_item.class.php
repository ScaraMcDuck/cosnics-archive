<?php
require_once dirname(__FILE__) . '/menu_data_manager.class.php';

class NavigationItem
{

    const CLASS_NAME = __CLASS__;

    const PROPERTY_ID = 'id';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_SECTION = 'section';
    const PROPERTY_EXTRA = 'extra';
    const PROPERTY_URL = 'url';

    private $defaultProperties;

    function NavigationItem($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_CATEGORY, self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_APPLICATION, self :: PROPERTY_SECTION, self :: PROPERTY_EXTRA, self :: PROPERTY_URL);
    }

    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    function set_sort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }

    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    function get_section()
    {
        return $this->get_default_property(self :: PROPERTY_SECTION);
    }

    function set_section($section)
    {
        $this->set_default_property(self :: PROPERTY_SECTION, $section);
    }

    function get_extra()
    {
        return $this->get_default_property(self :: PROPERTY_EXTRA);
    }

    function set_extra($extra)
    {
        $this->set_default_property(self :: PROPERTY_EXTRA, $extra);
    }

    function update()
    {
        $wdm = MenuDataManager :: get_instance();
        $success = $wdm->update_navigation_item($this);
        if (! $success)
        {
            return false;
        }

        return true;
    }

    function create()
    {
        $mdm = MenuDataManager :: get_instance();
        $id = $mdm->get_next_navigation_item_id();
        $this->set_id($id);
        $success = $mdm->create_navigation_item($this);
        if (! $success)
        {
            return false;
        }

        return true;
    }

    function delete()
    {
        $wdm = MenuDataManager :: get_instance();
        $success = $wdm->delete_navigation_item($this);
        if (! $success)
        {
            return false;
        }

        return true;
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
}
?>