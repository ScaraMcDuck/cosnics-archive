<?php
/**
 * @author Michael Kyndt
 */
class ReportingRights {

    function ReportingRights() {
    }

    public static function getUsersPerRole($params)
    {
        $rdm = RightsDataManager::get_instance();
        $udm = UserDataManager::get_instance();

        $list = $rdm->retrieve_roles();

        while ($role = $list->next_result()) {
            $arr[$role->get_id()][0] = 0;
        }

        $list = $udm->retrieve_user_roles();

        while ($bla = $list->next_result()) {
            $arr[$bla->get_role_id()][0]++;
        }

        foreach ($arr as $key => $value) {
            $arr[Translation :: get($rdm->retrieve_role($key)->get_name())] = $arr[$key];
            unset($arr[$key]);
        }

        return Reporting :: getSerieArray($arr);
    }//getUsersPerRole

    public static function getGroupsPerRole($params)
    {
        $rdm = RightsDataManager::get_instance();
        $gdm = GroupDataManager::get_instance();

        $list = $rdm->retrieve_roles();

        while($role = $list->next_result())
        {
            $arr[$role->get_id()][0] = 0;
        }

        $list = $gdm->retrieve_group_roles();

        while($group = $list->next_result())
        {
            $arr[$group->get_role_id()][0]++;
        }

        $group = $gdm->retrieve_group(0);

        foreach ($arr as $key => $value) {
            $arr[Translation :: get($rdm->retrieve_role($key)->get_name())] = $arr[$key];
            unset($arr[$key]);
        }

        return Reporting::getSerieArray($arr);
    }//getgroupsperrole

    public static function getNoOfRoles($params)
    {
        $rdm = RightsDataManager::get_instance();

        $list = $rdm->retrieve_roles();

        while ($role = $list->next_result()) {
            $arr[Translation :: get('Roles')][0]++;
        }

        return Reporting::getSerieArray($arr);
    }//getnoofroles

    public static function getRoles($params)
    {
        $rdm = RightsDataManager::get_instance();

        $list = $rdm->retrieve_roles();

        while ($role = $list->next_result()) {
            $arr[Translation :: get('Roles')][] = $role->get_name();
        }

        return Reporting::getSerieArray($arr);
    }//getroles
}
?>