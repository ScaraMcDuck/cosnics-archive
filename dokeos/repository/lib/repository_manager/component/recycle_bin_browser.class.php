<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/recycle_bin_browser/recycle_bin_browser_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
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
		$trail = new BreadcrumbTrail(false);
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('RecycleBin')));
		$trail->add_help('repository recyclebin');

		$this->display_header($trail, false, true);
		
		if (Request :: get(RepositoryManager :: PARAM_EMPTY_RECYCLE_BIN))
		{
			$this->empty_recycle_bin();
			$this->display_message(htmlentities(Translation :: get('RecycleBinEmptied')));
		}
		
		echo $this->get_action_bar()->as_html();
		$this->display_content_objects();
		$this->display_footer();
	}
	/**
	 * Display the learning objects in the recycle bin.
	 * @return int The number of learning objects currently in the recycle bin.
	 */
	private function display_content_objects()
	{
		$condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
		$parameters = $this->get_parameters(true);
		$table = new RecycleBinBrowserTable($this, $parameters, $condition);
		echo $table->as_html();
		return $table->get_object_count();
	}
	/**
	 * Empty the recycle bin.
	 * This function will permanently delete all objects from the recycle bin.
	 * Only objects from current user will be deleted.
	 */
	private function empty_recycle_bin()
	{
		$trashed_objects = $this->retrieve_content_objects(null, new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id()), array(), array(), 0, -1, ContentObject :: STATE_RECYCLED);
		$count = 0;
		while ($object = $trashed_objects->next_result())
		{
			$object->delete();
			$count++;
		}
		return $count;
	}
	
	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('EmptyRecycleBin'), Theme :: get_common_image_path().'treemenu/trash.png', $this->get_url(array(RepositoryManager :: PARAM_EMPTY_RECYCLE_BIN => 1)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>