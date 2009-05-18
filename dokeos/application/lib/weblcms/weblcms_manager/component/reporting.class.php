<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path().'lib/reporting.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_viewer.class.php';

class WeblcmsManagerReportingComponent extends WeblcmsManagerComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $rtv = new ReportingTemplateViewer($this);

        $classname = $_GET[ReportingManager::PARAM_TEMPLATE_NAME];

        $params = Reporting :: get_params($this);

        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'].'?application=weblcms', Translation :: get('MyCourses')));
        $trail->add(new Breadcrumb('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'].'?go=courseviewer&application=weblcms&course='.$params['course_id'], WebLcmsDataManager ::get_instance()->retrieve_course($params['course_id'])->get_name()));
        $trail->add(new Breadcrumb($this->get_parent()->get_reporting_url($classname, $params), Translation :: get('Reporting')));

        $this->display_header($trail);
        $rtv->show_reporting_template_by_name($classname, $params);
        $this->display_footer();
    }
}
?>