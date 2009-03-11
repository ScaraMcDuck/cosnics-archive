<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_manager.class.php';
require_once dirname(__FILE__).'/../reporting_manager_component.class.php';
require_once dirname(__FILE__).'/reporting_template_registration_browser_table/reporting_template_registration_browser_table.class.php';

require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class ReportingManagerReportingTemplateRegistrationBrowserComponent extends ReportingManagerComponent
{
	private $action_bar;
	private $application;
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$application = $this->application = Request :: get('application');
		if (!isset($application))
		{
			$application = $this->application = 'reporting';
		}

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES)), Translation :: get('Reporting')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get(Application :: application_to_class($application)) . '&nbsp;' . Translation :: get('Template')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->action_bar = $this->get_action_bar();
		$output = $this->get_template_html();
		
		$this->display_header($trail);
		echo '<br />' . $this->action_bar->as_html() . '<br />';
		echo $this->get_applications();
		echo $output;
		$this->display_footer();
	}
	
	/**
	 * Gets all the installed applications
	 */
	function get_applications()
	{
		require_once Path :: get_admin_path().'lib/admin_manager/admin_manager.class.php';
		$application = $this->application;
		
		$html = array();
		
		//$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/application.js' .'"></script>';
        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_menu.js' .'"></script>';
        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_menu_interface.js' .'"></script>';
		//$html[] = '<div class="configure">';
		$html[] = '<div class="dock" id="dock">';
        $html[] = '<div class="dock-container"> ';
		$applications = Application :: load_all();
			
		foreach (AdminManager :: get_application_platform_admin_links() as $application_links)
		{
			if (isset($application) && $application == $application_links['application']['class'])
			{
				//$html[] = '<div class="application_current">';
			}
			else
			{
				//$html[] = '<div class="application">';
			}
			$html[] = '<a class="dock-item" href="'. $this->get_url(array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES, ReportingManager :: PARAM_APPLICATION => $application_links['application']['class'])) .'">';
            			$html[] = '<img src="'. Theme :: get_image_path('admin') . 'place_' . $application_links['application']['class'] .'.png" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>';
			$html[] = '<span>'. $application_links['application']['name'].'</span>';
            $html[] = '</a>';
			//$html[] = '</div>';
		}
        
		$html[] = '</div>';
        $html[] = '</div>';
		$html[] = '<div style="clear: both;"></div><br /><br />';

        $html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_dock.js' .'"></script>';

		return implode("\n", $html);
	}
	
	/**
	 * Converts an array of templates for this application to a table
	 */
	function get_template_html()
	{		
		$table = new ReportingTemplateRegistrationBrowserTable($this, array(ReportingManager :: PARAM_ACTION => ReportingManager :: ACTION_BROWSE_TEMPLATES), $this->get_condition());
        $html = array();
		$html[] = '<div style="float: right; width: 100%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';

		return implode($html, "\n");
	}
	
	function get_condition() 
	{
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(ReportingTemplateRegistration :: PROPERTY_NAME, $query);
            $conditions[] = new LikeCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, $query);
            $cond = new OrCondition($conditions);
		}else
        {
            $conditions[] = new EqualityCondition('application',$this->application);
            $conditions[] = new EqualityCondition('platform','1');
            $cond = new AndCondition($conditions);
        }
		return $cond;
	}
	
	function get_reporting_template()
	{
		return (isset($_GET[ReportingManager :: PARAM_TEMPLATE_ID]) ? $_GET[ReportingManager :: PARAM_TEMPLATE_ID] : 0);
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url(array(ReportingManager :: PARAM_TEMPLATE_ID => $this->get_reporting_template())));
		//$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_CREATE_ROLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		//$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('reporting'));
		
		return $action_bar;
	}
}
?>