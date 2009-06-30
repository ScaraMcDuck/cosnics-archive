<?php
require_once Path :: get_common_path() . 'sub_manager_component.class.php';

class PackageManagerComponent extends SubManagerComponent
{
    function retrieve_registrations($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
    {
        return $this->get_parent()->retrieve_registrations($condition, $orderBy, $orderDir, $offset, $maxObjects);
    }

    function count_registrations($condition = null)
    {
        return $this->get_parent()->count_registrations($condition);
    }
}
?>