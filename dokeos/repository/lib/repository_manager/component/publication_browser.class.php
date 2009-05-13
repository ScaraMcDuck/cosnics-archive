<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/publication_browser/publication_browser_table.class.php';
/**
 * Repository manager component which displays user's publications.
 * 
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RepositoryManagerPublicationBrowserComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail(false);
		
		$output = $this->get_publications_html();
		
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyPublications')));
		$this->display_header($trail);
		echo $output;
		$this->display_footer();
	}
	
	/**
	 * Gets the  table which shows the users's publication
	 */
	private function get_publications_html()
	{
		
		$condition = $this->get_search_condition();
		$parameters = $this->get_parameters(true);
		$types = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE];
		if (is_array($types) && count($types))
		{
			$parameters[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE] = $types;
		}
		
		$table = new PublicationBrowserTable($this, $parameters, $condition);
		return $table->as_html();
	}
}
?>