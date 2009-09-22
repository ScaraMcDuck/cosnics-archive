<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../personal_calendar_manager.class.php';
require_once dirname(__FILE__).'/../personal_calendar_manager_component.class.php';
require_once Path :: get_repository_path() . 'lib/export/learning_object_export.class.php';

class PersonalCalendarManagerIcalExporterComponent extends PersonalCalendarManagerComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(PersonalCalendarManager :: PARAM_CALENDAR_EVENT_ID);

        if ($id)
        {
            $calendar_event_publication = $this->retrieve_calendar_event_publication($id);
            $learning_object = $calendar_event_publication->get_publication_object();
            
            $exporter = LearningObjectExport :: factory('ical', $learning_object);
			$path = $exporter->export_learning_object();
			
			Filesystem :: file_send_for_download($path, true, basename($path));
			Filesystem :: remove($path);
        }
    	else
    	{
    		$this->display_header();
    		$this->display_error_message(Translation :: get('NoObjectSelected'));
    		$this->dipslay_footer();
    	}
    }
    
}
?>