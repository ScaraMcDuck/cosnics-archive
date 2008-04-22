<?php
/**
 * $Id$
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
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('RecycleBin')));
		
		$this->display_header($trail);
		if ($_GET[RepositoryManager :: PARAM_EMPTY_RECYCLE_BIN])
		{
			$this->empty_recycle_bin();
			$this->display_message(htmlentities(Translation :: get('RecycleBinEmptied')));
		}
		$count = $this->display_learning_objects();
		if ($count)
		{
			$toolbar_data = array();
			$toolbar_data[] = array(
				'href' => $this->get_url(array(RepositoryManager :: PARAM_EMPTY_RECYCLE_BIN => 1)),
				'img' => Theme :: get_common_img_path().'treemenu/trash.png',
				'label' => Translation :: get('EmptyRecycleBin'),
				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL,
				'confirm' => true
			);
			echo '<div style="text-align: right; margin-top: 1em;">'.RepositoryUtilities :: build_toolbar($toolbar_data).'</div>';
		}
		$this->display_footer();
	}
	/**
	 * Display the learning objects in the recycle bin.
	 * @return int The number of learning objects currently in the recycle bin.
	 */
	private function display_learning_objects()
	{
		$condition = $this->get_search_condition();
		$parameters = $this->get_parameters(true);
		$table = new RepositoryRecycleBinBrowserTable($this, null, $parameters, $condition);
		echo $table->as_html();
		return $table->get_learning_object_count();
	}
	/**
	 * Empty the recycle bin.
	 * This function will permanently delete all objects from the recycle bin.
	 * Only objects from current user will be deleted.
	 */
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