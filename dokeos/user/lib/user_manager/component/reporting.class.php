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

        $classname = $_GET[ReportingManager::PARAM_TEMPLATE_NAME];

        $params = Reporting :: get_params($this);

        $trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION =>  UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));

        $user = $this->retrieve_user($params[ReportingManager::PARAM_USER_ID]);
        $trail->add(new Breadcrumb($this->get_url(array(ReportingManager::PARAM_TEMPLATE_NAME => $classname, ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS => $params)), $user->get_fullname()));
        $trail->add(new Breadcrumb($this->get_url(array(ReportingManager::PARAM_TEMPLATE_NAME => $classname, ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS => $params)), Translation :: get('Report')));


        $this->display_header($trail, false, 'user general');

        $rtv->show_reporting_template_by_name($classname, $params);

        $this->display_footer();
	}
}
?>