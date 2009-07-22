<?php
/**
 * @package alexia
 * @subpackage alexia_manager
 * @subpackage component
 * 
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__).'/../alexia_manager.class.php';
require_once dirname(__FILE__).'/../alexia_manager_component.class.php';
require_once dirname(__FILE__) . '/alexia_publication_browser/alexia_publication_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class AlexiaManagerBrowserComponent extends AlexiaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Library')));
		$trail->add_help('alexia general');

		$this->display_header($trail);
		echo '<a name="top"></a>';
		echo $this->get_action_bar_html() . '';
		echo '<div id="action_bar_browser">';
		echo $this->get_publications_html();
		echo '</div>';
		$this->display_footer();
	}
	
    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        
        $table = new AlexiaPublicationBrowserTable($this, null, $parameters, null);
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

	function get_action_bar_html()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_CREATE_PUBLICATION))));
		$action_bar->set_search_url($this->get_url());
//		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path().'tool_calendar_down.png', $this->get_url(array (Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'list'))));
		return $action_bar->as_html();
	}
}
?>