<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/recycle_bin_browser/repositoryrecyclebinbrowsertable.class.php';
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerRecycleBinBrowserComponent extends RepositoryManagerComponent
{
	function run()
	{
		$this->display_header(array(array('url' => $this->get_url(), 'name' => get_lang('RecycleBin'))));
		$this->display_learning_objects();
		$this->display_footer();
	}
	private function display_learning_objects()
	{
		$condition = $this->get_search_condition();
		$parameters = $this->get_parameters(true);
		$table = new RepositoryRecycleBinBrowserTable($this, null, $parameters, $condition);
		echo $table->as_html();
	}
}
?>