<?php
/**
 * @package application.webconferencing.webconferencing.component
 */

require_once dirname(__FILE__).'/../webconferencing_manager.class.php';
require_once dirname(__FILE__).'/../webconferencing_manager_component.class.php';
require_once dirname(__FILE__).'/webconference_browser/webconference_browser_table.class.php';

/**
 * webconferencing component which allows the user to browse his webconferences
 * @author Stefaan Vanbillemont
 */
class WebconferencingManagerWebconferencesBrowserComponent extends WebconferencingManagerComponent
{
	
	private $action_bar;

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Webconferencing')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseWebconferences')));
		$trail->add_help('webconferencing general');
		$this->action_bar = $this->get_action_bar();
		
		$toolbar = $this->get_action_bar();

		$this->display_header($trail);
		echo $toolbar->as_html();
		echo '<div id="action_bar_browser">';
		echo $this->get_table();
		echo '</div>';
		$this->display_footer();
	}

	function get_table()
	{
		$table = new WebconferenceBrowserTable($this, array(Application :: PARAM_APPLICATION => 'webconferencing', Application :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES), null);
		return $table->as_html();
	}
	
	function add_actionbar_item($item)
	{
		$this->action_bar->add_tool_action($item);
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateWebconference'), Theme :: get_common_image_path().'action_publish.png', $this->get_create_webconference_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		return $action_bar;
	}
	
	function get_condition()
	{
		$condition = null;
		$user = $this->get_user();

		if (!$user->is_platform_admin())
		{
		}

		return $condition;
	}

}
?>