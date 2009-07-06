<?php
require_once Path :: get_admin_path() . 'lib/package_remover/package_remover.class.php';

class PackageApplicationRemover extends PackageRemover
{
    private $registration;

    function run()
    {
        $adm = AdminDataManager :: get_instance();
        $registration_id = Request :: get(PackageManager :: PARAM_PACKAGE);
        $registration = $adm->retrieve_registration($registration_id);
        $this->registration = $registration;

        // Deactivate the application, thus making it inaccesible
        $this->add_message(Translation :: get('DeactivatingApplication'));
        $registration->toggle_status();
        if (! $registration->update())
        {
            return $this->installation_failed('initilization', Translation :: get('ApplicationDeactivationFailed'));
        }
        else
        {
            $mdm = MenuDataManager :: get_instance();
            $this->add_message(Translation :: get('RemovingMenuItems'));

            $condition = new EqualityCondition(MenuItem :: PROPERTY_APPLICATION, $registration->get_name());
            if ($mdm->delete_menu_items($condition))
            {
                $this->installation_successful('initilization', Translation :: get('ApplicationSuccessfullyDeactivated'));
            }
            else
            {
                return $this->installation_failed('initilization', Translation :: get('ApplicationDeactivationFailed'));
            }
        }

        // Remove webservices
        if (! $this->remove_webservices())
        {
            return $this->installation_failed('webservice', Translation :: get('WebservicesDeletionFailed'));
        }
        else
        {
            $this->installation_successful('webservice', Translation :: get('WebservicesSuccessfullyDeleted'));
        }

        // Remove reporting
        if (! $this->remove_reporting())
        {
            return $this->installation_failed('reporting', Translation :: get('ReportingDeletionFailed'));
        }
        else
        {
            $this->installation_successful('reporting', Translation :: get('ReportingSuccessfullyDeleted'));
        }

        // Remove tracking
        if (! $this->remove_tracking())
        {
            return $this->installation_failed('tracking', Translation :: get('TrackingDeletionFailed'));
        }
        else
        {
            $this->installation_successful('tracking', Translation :: get('TrackingSuccessfullyDeleted'));
        }

        // Remove roles and rights
        if (! $this->remove_rights())
        {
            return $this->installation_failed('rights', Translation :: get('RightsDeletionFailed'));
        }
        else
        {
            $this->installation_successful('rights', Translation :: get('RightsSuccessfullyDeleted'));
        }

        // Remove storage units
        if (! $this->remove_storage_units())
        {
            return $this->installation_failed('database', Translation :: get('StorageUnitsDeletionFailed'));
        }
        else
        {
            $this->installation_successful('database', Translation :: get('StorageUnitsSuccessfullyDeleted'));
        }

        // Remove application
        if (! $this->remove_settings() || ! $this->remove_application())
        {
            return $this->installation_failed('failed', Translation :: get('ApplicationDeletionFailed'));
        }
        else
        {
            $this->installation_successful('finished', Translation :: get('ApplicationSuccessfullyDeleted'));
        }
    }

    function remove_webservices()
    {
        $registration = $this->registration;

        $wdm = WebserviceDataManager :: get_instance();
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_APPLICATION, $registration->get_name());
        $webservices = $wdm->retrieve_webservices($condition);

        while ($webservice = $webservices->next_result())
        {
            $message = Translation :: get('RemovingWebserviceRegistration') . ': ' . $webservice->get_name();
            $this->add_message($message);
            if (! $webservice->delete())
            {
                return false;
            }
        }

        // TODO: Delete categories added  by the application


        return true;
    }

    function remove_reporting()
    {
        $registration = $this->registration;

        $rdm = ReportingDataManager :: get_instance();

        $this->add_message(Translation :: get('RemovingReportingblocks'));
        $condition = new EqualityCondition(ReportingBlock :: PROPERTY_APPLICATION, $registration->get_name());
        if (! $rdm->delete_reporting_blocks($condition))
        {
            return false;
        }

        $this->add_message(Translation :: get('RemovingReportingTemplates'));
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, $registration->get_name());
        if (! $rdm->delete_reporting_template_registrations($condition))
        {
            return false;
        }

        if (! $rdm->delete_orphaned_block_template_relations())
        {
            $this->add_message(Translation :: get('DeletingOrphanedBlockTemplateRelationsFailed'), self :: TYPE_WARNING);
        }
        else
        {
            $this->add_message(Translation :: get('DeletingOrphanedBlockTemplateRelations'));
        }

        return true;
    }

    function remove_tracking()
    {
        // TODO: Remove the tracking data / objects


        return true;
    }

    function remove_rights()
    {
        $registration = $this->registration;
        $rdm = RightsDataManager :: get_instance();
        $condition = new EqualityCondition(Location :: PROPERTY_APPLICATION, $registration->get_name());
        $this->add_message(Translation :: get('DeletingApplicationLocations'));
        if (! $rdm->delete_locations($condition))
        {
            return false;
        }
        else
        {
            if (! $rdm->delete_orphaned_role_right_locations())
            {
                $this->add_message(Translation :: get('DeletingOrphanedRoleRightLocationsFailed'), self :: TYPE_WARNING);
            }

            return true;
        }
    }

    function remove_settings()
    {
        $registration = $this->registration;
        $adm = AdminDataManager :: get_instance();
        $condition = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $registration->get_name());

        $this->add_message(Translation :: get('DeletingApplicationSettings'));
        return $adm->delete_settings($condition);
    }

    function remove_storage_units()
    {
        $registration = $this->registration;
        $database = new Database();
        $database->set_prefix($registration->get_name() . '_');

        $path = Path :: get_application_path() . 'lib/' . $registration->get_name() . '/install/';
        $files = FileSystem :: get_directory_content($path, FileSystem :: LIST_FILES);

        foreach ($files as $file)
        {
            if ((substr($file, - 3) == 'xml'))
            {
                $doc = new DOMDocument();
                $doc->load($file);
                $object = $doc->getElementsByTagname('object')->item(0);
                $name = $object->getAttribute('name');

                $this->add_message(Translation :: get('DroppingStorageUnit') . ': <em>' . $name . '</em>');

                if (! $database->drop_storage_unit($name))
                {
                    return false;
                }
            }
        }

        return true;
    }

    function remove_application()
    {
        $registration = $this->registration;

        $this->add_message(Translation :: get('DeletingApplicationRegistration'));
        if (! $registration->delete())
        {
            return false;
        }

        $this->add_message(Translation :: get('DeletingApplication'));
        return true;
    }
}
?>