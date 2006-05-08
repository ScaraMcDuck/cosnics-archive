<?php
/**
 * @package repository.repositorymanager
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
	function run()
	{
		$this->display_header(array(), true);
		$this->display_learning_objects();
		$this->display_footer();
	}
	/**
	 * Displays the table which shows the learning objects in the currently
	 * active category
	 */
	private function display_learning_objects()
	{
		$condition = $this->get_search_condition();
		$parameters = $this->get_parameters(true);
		$types = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE];
		if (is_array($types) && count($types))
		{
			$parameters[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE] = $types;
		}
		$table = new RepositoryBrowserTable($this, null, $parameters, $condition);
		echo $table->as_html();
	}
}
?>