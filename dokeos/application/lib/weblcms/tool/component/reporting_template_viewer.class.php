<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_viewer.class.php';

class ToolReportingTemplateViewerComponent extends ToolComponent
{
    function run()
    {
        $rtv = new ReportingTemplateViewer($this);

        if(isset($_GET[ReportingManager::PARAM_TEMPLATE_NAME]))
            $classname = $_GET[ReportingManager::PARAM_TEMPLATE_NAME];
        else
            $classname = 'PublicationDetailReportingTemplate';

        $params = $_GET[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS];
        if(!isset($params[ReportingManager::PARAM_COURSE_ID]))
        $params[ReportingManager::PARAM_COURSE_ID] = Request :: get('course');

        $params['parent'] = $this;

        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(ReportingManager::get_reporting_template_registration_url_content($this,$classname,$params),$classname));

        $params['trail'] = $trail;
        if(isset($_GET['pid']))
            $params['pid'] = $_GET['pid'];

        $rtv->show_reporting_template_by_name($classname, $params);
    }
}
?>