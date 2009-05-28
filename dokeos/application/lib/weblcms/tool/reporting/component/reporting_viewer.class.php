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
        echo 'bus';
        $classname = 'CourseStudentTrackerReportingTemplate';

        $params = Reporting :: get_params($this);
        $url =array();
        $url[Tool :: PARAM_ACTION] = 'view_reporting_template';
        $url['template_name'] = $classname;
        foreach($params as $key => $param)
        {
            $url[$key] = $param;
        }

        header('location:'.$this->get_url($url));
    }
}
?>