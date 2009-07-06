<?php
require_once Path :: get_admin_path() . 'lib/package_remover/package_remover.class.php';

class PackageLearningObjectRemover extends PackageRemover
{
    private $registration;

    function run()
    {
        $adm = AdminDataManager :: get_instance();
        $registration_id = Request :: get(PackageManager :: PARAM_PACKAGE);
        $registration = $adm->retrieve_registration($registration_id);
        $this->registration = $registration;
    }
}
?>