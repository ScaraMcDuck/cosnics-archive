<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_manager.class.php';
require_once Path :: get_reporting_path() . 'lib/reporting.class.php';
require_once dirname(__FILE__).'/../reporting_manager_component.class.php';
//require_once dirname(__FILE__).'/role_browser_table/role_browser_table.class.php';
//require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
//require_once Path :: get_reporting_path() . 'lib/reporting_template_viewer.class.php';

class ReportingManagerReportingTemplateViewComponent extends ReportingManagerComponent
{
	private $action_bar;
	private $template;
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$template = $this->template = Request :: get('template');
		if (!isset($template))
		{
			//$template = $this->template = 'reporting';
			//error
		}

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get(Application :: application_to_class($template)) . '&nbsp;' . Translation :: get('Template')));

        $rpdm = ReportingDataManager :: get_instance();
    	$reporting_template = $rpdm->retrieve_reporting_template_by_name($template);

        $application = $reporting_template->get_application();
        $base_path = (Application :: is_application($application) ? Path :: get_application_path().'lib/' : Path :: get(SYS_PATH));
        $file = $base_path .$application. '/reporting/templates/'.DokeosUtilities :: camelcase_to_underscores($reporting_template->get_classname()).'.class.php';;
        require_once($file);

        $classname = $reporting_template->get_classname();
        $template = new $classname($this);

        //is platform template
        if ($reporting_template->isPlatformTemplate() && !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

		$this->display_header($trail);

        if(isset($_GET['s']))
		{
			$rep_block = ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name($_GET['s']);
            $template->add_reporting_block($rep_block);
		}
        echo $template->to_html();
		$this->display_footer();
	}//run
}
?>