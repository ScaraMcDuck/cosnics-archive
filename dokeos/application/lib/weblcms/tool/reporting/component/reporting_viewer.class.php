<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_tool_component.class.php';
require_once Path :: get_reporting_path().'lib/reporting.class.php';

class ReportingToolViewerComponent extends ReportingToolComponent
{
    function run()
    {
        $classname = 'CourseStudentTrackerReportingTemplate';

        $params = Reporting :: get_params($this);

        header('location:'.$this->get_parent()->get_reporting_url($classname,$params));
    }
}
?>