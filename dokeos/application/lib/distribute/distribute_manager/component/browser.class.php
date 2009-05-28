<?php
/**
 * @package application.distribute.distribute.component
 */
require_once dirname(__FILE__).'/../distribute_manager.class.php';
require_once dirname(__FILE__).'/../distribute_manager_component.class.php';

/**
 * Distribute component which allows the user to browse the distribute application
 * @author Hans De Bisschop
 */
class DistributeManagerBrowserComponent extends DistributeManagerComponent
{

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseDistribute')));

		$this->display_header($trail);
		echo '<a name="top"></a>';
		echo $this->get_action_bar_html() . '';
		echo '<div id="action_bar_browser">';
		echo $this->get_browser_html();
		echo '</div>';
		$this->display_footer();
	}

	function get_action_bar_html()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Send'), Theme :: get_common_image_path().'action_mail.png', $this->get_url(array(Application :: PARAM_ACTION => DistributeManager :: ACTION_DISTRIBUTE_ANNOUNCEMENT)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar->as_html();
	}

	function get_browser_html()
	{
	    return 'Content goes here ...';
	}
}
?>