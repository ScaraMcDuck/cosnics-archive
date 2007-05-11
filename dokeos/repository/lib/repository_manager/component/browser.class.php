<?php
/**
 * $Id$
 * @package repository.repositorymanager
 * 
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/browser/repositorybrowsertable.class.php';
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerBrowserComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$output = $this->get_learning_objects_html();
		$this->display_header(array(), true);
		echo $output;
		$this->display_footer();
	}
	/**
	 * Gets the  table which shows the learning objects in the currently active
	 * category
	 */
	private function get_learning_objects_html()
	{
		$condition = $this->get_search_condition();
		$parameters = $this->get_parameters(true);
		$types = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE];
		if (is_array($types) && count($types))
		{
			$parameters[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE] = $types;
		}
		$table = new RepositoryBrowserTable($this, null, $parameters, $condition);
		return $table->as_html();
	}
}
?>