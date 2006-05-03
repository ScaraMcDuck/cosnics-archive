<?php // $Id$
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/browser/repositorybrowsertable.class.php';

class RepositoryManagerBrowserComponent extends RepositoryManagerComponent
{
	function run()
	{
		$this->display_header(array(), true);
		$this->display_learning_objects();
		$this->display_footer();
	}

	private function display_learning_objects()
	{
		$condition = $this->get_search_condition();
		$parameters = array_merge($this->get_parameters(true));
		$table = new RepositoryBrowserTable($this, null, $parameters, $condition);
		echo $table->as_html();
	}
}
?>