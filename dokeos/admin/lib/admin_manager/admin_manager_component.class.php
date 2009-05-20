<?php
/**
 * @package admin.lib.admin_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path() . 'core_application_component.class.php';

abstract class AdminManagerComponent extends CoreApplicationComponent
{

    protected function AdminManagerComponent($admin_manager)
    {
        parent :: __construct($admin_manager);
    }

    /**
     * @see AdminManager :: retrieve_system_announcement_publication()
     */
    function retrieve_system_announcement_publication($id)
    {
        return $this->get_parent()->retrieve_system_announcement_publication($id);
    }

    /**
     * @see AdminManager :: retrieve_system_announcement_publications()
     */
    function retrieve_system_announcement_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
    {
        return $this->get_parent()->retrieve_system_announcement_publications($condition, $orderBy, $orderDir, $offset, $maxObjects);
    }

    /**
     * @see AdminManager :: count_system_announcement_publications()
     */
    function count_system_announcement_publications($condition = null)
    {
        return $this->get_parent()->count_system_announcement_publications($condition);
    }

    function get_system_announcement_publication_deleting_url($system_announcement_publication)
    {
        return $this->get_parent()->get_system_announcement_publication_deleting_url($system_announcement_publication);
    }

    function get_system_announcement_publication_visibility_url($system_announcement_publication)
    {
        return $this->get_parent()->get_system_announcement_publication_visibility_url($system_announcement_publication);
    }

    function get_system_announcement_publication_viewing_url($system_announcement_publication)
    {
        return $this->get_parent()->get_system_announcement_publication_viewing_url($system_announcement_publication);
    }

    function get_system_announcement_publication_editing_url($system_announcement_publication)
    {
        return $this->get_parent()->get_system_announcement_publication_editing_url($system_announcement_publication);
    }

    function get_system_announcement_publication_creating_url()
    {
        return $this->get_parent()->get_system_announcement_publication_creating_url();
    }
}
?>