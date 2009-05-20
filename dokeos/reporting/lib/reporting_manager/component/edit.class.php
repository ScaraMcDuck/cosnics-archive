<?php
/**
 * @author Michael
 */
require_once dirname(__FILE__).'/../reporting_manager.class.php';
require_once dirname(__FILE__).'/../reporting_manager_component.class.php';
require_once dirname(__FILE__).'/reporting_template_registration_browser_table/reporting_template_registration_browser_table.class.php';
require_once Path :: get_reporting_path() . 'lib/forms/reporting_template_registration_form.class.php';

class ReportingManagerEditComponent extends ReportingManagerComponent {
    	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
        $trail = new BreadcrumbTrail();
        $admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager::ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));

		$id = $_GET[ReportingManager :: PARAM_TEMPLATE_ID];

		if ($id)
		{
			$reporting_template_registration = $this->retrieve_reporting_template_registration($id);

            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ReportingManager::ACTION_VIEW_TEMPLATE, ReportingManager::PARAM_TEMPLATE_ID => $id)), Translation :: get($reporting_template_registration->get_title())));

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail, false, 'reporting general');
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$form = new ReportingTemplateRegistrationForm(ReportingTemplateRegistrationForm :: TYPE_EDIT, $reporting_template_registration, $this->get_url(array(ReportingManager :: PARAM_TEMPLATE_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_reporting_template_registration();
				$this->redirect(Translation :: get($success ? 'ReportingTemplateRegistrationUpdated' : 'ReportingTemplateRegistrationNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES));
			}
			else
			{
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Edit')));
				$this->display_header($trail, false, 'reporting general');
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoReportingTemplateRegistrationSelected')));
		}
	}
}
?>
