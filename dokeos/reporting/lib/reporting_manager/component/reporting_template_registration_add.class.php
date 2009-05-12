<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../reporting_manager.class.php';
require_once dirname(__FILE__).'/../reporting_manager_component.class.php';
//require_once dirname(__FILE__).'/role_browser_table/role_browser_table.class.php';
//require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
/**
 * 
 */
class ReportingManagerReportingTemplateRegistrationAddComponent extends ReportingManagerComponent
{
	private $action_bar;
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Translation :: get('Reporting')))));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddTemplate')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->action_bar = $this->get_action_bar();
		$output = $this->get_user_html();
		
		$this->display_header($trail, false);
		echo '<br />' . $this->action_bar->as_html() . '<br />';
		echo $output;
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		//$table = new RoleBrowserTable($this, array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_BROWSE_ROLES), $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 100%;">';
		//$html[] = $table->as_html();	
		$html[] = 'bla';	 
		$html[] = '</div>';
		
		return implode($html, "\n");
	}

	function get_condition()
	{	
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$condition = new LikeCondition(HelpItem :: PROPERTY_NAME, $query);
		}
		
		return $condition;
	}
	
	function get_template()
	{
		return (isset($_GET[ReportingManager :: PARAM_TEMPLATE_ID]) ? $_GET[ReportingManager :: PARAM_TEMPLATE_ID] : 0);
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url(array(ReportingManager :: PARAM_TEMPLATE_ID => $this->get_template())));
		//$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_CREATE_ROLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->set_help_action(HelpManager :: get_tool_bar_help_item('reporting'));
		
		return $action_bar;
	}
}
?>