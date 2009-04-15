<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_tool_component.class.php';
require_once Path :: get_reporting_path().'lib/reporting_manager/reporting_manager.class.php';

class ReportingToolViewerComponent extends ReportingToolComponent
{
	function run()
	{
        $params = array();
        $params[ReportingManager :: PARAM_COURSE_ID] = $this->get_course_id();
        $url = ReportingManager :: get_reporting_template_registration_url('CourseLearnerTrackerReportingTemplate',$params);
        header('location: '.$url);
	}
}
?>