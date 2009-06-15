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

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseWebconferences')));

		$this->display_header($trail);

		echo '<a href="' . $this->get_create_webconference_url() . '">' . Translation :: get('CreateWebconference') . '</a>';
		echo '<br /><br />';
		echo $this->get_table();
		$this->display_footer();
	}

	function get_table()
	{
		$table = new WebconferenceBrowserTable($this, array(Application :: PARAM_APPLICATION => 'tester', Application :: PARAM_ACTION => WebconferencingManager :: ACTION_BROWSE_WEBCONFERENCES), null);
		return $table->as_html();
	}

}
?>