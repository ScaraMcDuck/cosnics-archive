<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_tool_component.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_viewer.class.php';

class ReportingToolViewerComponent extends ReportingToolComponent
{
    function run()
    {
        $rtv = new ReportingTemplateViewer($this);

        $classname = $_GET[ReportingManager::PARAM_TEMPLATE_NAME];

        $params = $_GET[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS];
        if(!isset($params[ReportingManager::PARAM_COURSE_ID]))
        $params[ReportingManager::PARAM_COURSE_ID] = Request :: get('course');

        $params['url'] = $this->get_url();

        $_SESSION[ReportingManager::PARAM_REPORTING_PARENT] = $this;
        //$params['url'] = ReportingManager::get_reporting_template_registration_url_content

        $trail = $params['trail'];

        if(!isset($trail))
        {
            $trail = new BreadcrumbTrail();
        }

        if(!isset($classname))
        {
            $classname = 'CourseStudentTrackerReportingTemplate';
        }else
        {
            $trail->add(new Breadcrumb(ReportingManager::get_reporting_template_registration_url_content($this,$classname,$params),$classname));
        }

        $params['trail'] = $trail;

        $rtv->show_reporting_template_by_name($classname, $params);
    }
}
?>