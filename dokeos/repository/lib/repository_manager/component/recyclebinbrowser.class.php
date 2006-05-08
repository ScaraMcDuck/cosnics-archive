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
		if ($_GET[RepositoryManager :: PARAM_EMPTY_RECYCLE_BIN])
		{
			$this->empty_recycle_bin();
			$this->display_message(get_lang('RecycleBinEmptied'));
		}
		$count = $this->display_learning_objects();
		if ($count)
		{
			echo '<div class="empty_recycle_bin" style="margin-top: 1em; text-align: right;"><a href="'.htmlentities($this->get_url(array(RepositoryManager :: PARAM_EMPTY_RECYCLE_BIN => 1))).'" style="background: url('.$this->get_web_code_path().'img/recycle.gif) no-repeat 0 50%; padding: 10px 0 10px 31px;">'.get_lang('EmptyRecycleBin').'</a></div>';
		}
		$this->display_footer();
	}
	private function display_learning_objects()
	{
		$condition = $this->get_search_condition();
		$parameters = $this->get_parameters(true);
		$table = new RepositoryRecycleBinBrowserTable($this, null, $parameters, $condition);
		echo $table->as_html();
		return $table->get_learning_object_count();
	}
	private function empty_recycle_bin()
	{
		$trashed_objects = $this->retrieve_learning_objects(null, new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $this->get_user_id()), array(), array(), 0, -1, LearningObject :: STATE_RECYCLED);
		$count = 0;
		while ($object = $trashed_objects->next_result())
		{
			$object->delete();
			$count++;
		}
		return $count;
	}
}
?>