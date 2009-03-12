<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_manager.class.php';
require_once Path :: get_reporting_path() . 'lib/reporting.class.php';
require_once dirname(__FILE__).'/../reporting_manager_component.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class ReportingManagerReportingTemplateRegistrationViewComponent extends ReportingManagerComponent
{
	//private $template;
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		//$template = $this->template = Request :: get('template');
        $template = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
		if (!isset($template))
		{
			//$template = $this->template = 'reporting';
			//error
		}

        //trail = given trail

        //$trail = new BreadcrumbTrail();
		//$trail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
		//$trail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES, ReportingManager :: PARAM_APPLICATION => $application)), Translation :: get(Application :: application_to_class($application)) . '&nbsp;' . Translation :: get('Template')));

		//$trail = new BreadcrumbTrail();
		//$trail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
		//$trail->add(new Breadcrumb($this->get_url(), Translation :: get(Application :: application_to_class($template)) . '&nbsp;' . Translation :: get('Template')));

        $rpdm = ReportingDataManager :: get_instance();
    	if(!$reporting_template_registration = $rpdm->retrieve_reporting_template_registration($template))
        {
            $this->display_header($trail);
			Display :: error_message(Translation :: get("NotFound"));
			$this->display_footer();
			exit;
        }

        //is platform template
        if ($reporting_template_registration->isPlatformTemplate() && !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

        $application = $reporting_template_registration->get_application();
        $base_path = (Application :: is_application($application) ? Path :: get_application_path().'lib/' : Path :: get(SYS_PATH));
        $file = $base_path .$application. '/reporting/templates/'.DokeosUtilities :: camelcase_to_underscores($reporting_template_registration->get_classname()).'.class.php';;
        require_once($file);

        $classname = $reporting_template_registration->get_classname();
        $template = new $classname($this);
        $template->set_registration_id($reporting_template_registration->get_id());

        if(isset($_GET[ReportingManager :: PARAM_TEMPLATE_PARAMETERS]))
        {
            $params = $_GET[ReportingManager :: PARAM_TEMPLATE_PARAMETERS];
            $template->set_reporting_blocks_parameters($params);
        }

		$this->display_header($trail);

        if(isset($_GET['s']))
		{
            $template->show_reporting_block($_GET['s']);
		}
        echo $template->to_html();
		$this->display_footer();
	}//run
}
?>