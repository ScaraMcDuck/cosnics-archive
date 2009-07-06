<?php
/**
 * @author Michael Kyndt
 */
class ReportingAdmin
{

    function ReportingAdmin()
    {
    }

    public static function getNoOfApplications()
    {
        require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
        $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
        $adm = new AdminManager($user);
        $arr[Translation :: get('NumberOfApplications')][0] = 0;
        foreach ($adm->get_application_platform_admin_links() as $application_links)
        {
            $arr[Translation :: get('NumberOfApplications')][0] ++;
        }
        
        return Reporting :: getSerieArray($arr);
    }

    public static function getMostUsedApplications()
    {
    
    }
}
?>