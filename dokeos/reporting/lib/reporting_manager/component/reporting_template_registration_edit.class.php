<?php
/**
 * @author Michael
 */
require_once dirname(__FILE__).'/../reporting_manager.class.php';
require_once dirname(__FILE__).'/../reporting_manager_component.class.php';
require_once dirname(__FILE__).'/reporting_template_registration_browser_table/reporting_template_registration_browser_table.class.php';
require_once Path :: get_reporting_path() . 'lib/forms/reporting_template_registration_form.class.php';

class ReportingManagerReportingTemplateRegistrationEditComponent extends ReportingManagerComponent {
    	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
        //trail from browser ?

		$id = $_GET[ReportingManager :: PARAM_TEMPLATE_ID];

		if ($id)
		{
			$reporting_template_registration = $this->retrieve_reporting_template_registration($id);

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$form = new ReportingTemplateRegistrationForm(ReportingTemplateRegistrationForm :: TYPE_EDIT, $reporting_template_registration, $this->get_url(array(ReportingManager :: PARAM_TEMPLATE_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_reporting_template_registration();
				$this->redirect('url', Translation :: get($success ? 'ReportingTemplateRegistrationUpdated' : 'ReportingTemplateRegistrationNotUpdated'), ($success ? false : true), array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES));
			}
			else
			{
				$this->display_header($trail);
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
