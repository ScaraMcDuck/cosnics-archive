<?php
/**
 * @package alexia
 * @subpackage alexia_manager
 * @subpackage component
 * 
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/../alexia_manager.class.php';
require_once dirname(__FILE__) . '/../alexia_manager_component.class.php';
require_once Path :: get_application_library_path() . 'repo_viewer/repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../publisher/alexia_publisher.class.php';

class AlexiaManagerPublisherComponent extends AlexiaManagerComponent
{

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
		$trail->add_help('alexia general');

		$object = Request :: get('object');
		$pub = new RepoViewer($this, 'link', true);

		if(!isset($object))
		{
			$html[] =  $pub->as_html();
		}
		else
		{
			$publisher = new AlexiaPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}

		$this->display_header($trail);
		echo implode("\n", $html);
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
}
?>