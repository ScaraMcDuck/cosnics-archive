<?php
/**
 * @author Michael
 */
require_once Path :: get_reporting_path().'lib/reporting_template_viewer.class.php';
require_once Path :: get_reporting_path().'lib/reporting.class.php';
class UserManagerReportingComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
        $rtv = new ReportingTemplateViewer($this);

        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserList')));
        $trail->add(new Breadcrumb($this->get_url(), 'user'));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Reporting')));

        $classname = $_GET[ReportingManager::PARAM_TEMPLATE_NAME];

        $params = Reporting :: get_params($this);

        $this->display_header($trail);

        $rtv->show_reporting_template_by_name($classname, $params);

        $this->display_footer();
	}
}
?>