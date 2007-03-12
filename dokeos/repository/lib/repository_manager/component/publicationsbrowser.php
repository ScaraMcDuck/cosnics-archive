<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/publicationbrowser/publicationbrowsertable.class.php';
/**
 * Repository manager component which displays the quota to the user.
 *
 * This component displays two progress-bars. The first one displays the used
 * disk space and the second one the number of learning objects in the users
 * repository.
 */
class RepositoryManagerPublicationBrowserComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$output = $this->get_publications_html();
		$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('MyPublications')));
		$this->display_header($breadcrumbs);
		echo $output;
		$this->display_footer();
	}
	
	/**
	 * Gets the  table which shows the learning objects in the currently active
	 * category
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
		
		$table = new PublicationBrowserTable($this, null, $parameters, $condition);
		return $table->as_html();
	}
	
	
	
}
?>