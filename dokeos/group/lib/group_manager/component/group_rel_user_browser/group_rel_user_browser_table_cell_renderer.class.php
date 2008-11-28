<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/group_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../group_rel_user_table/default_group_rel_user_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../group.class.php';
require_once dirname(__FILE__).'/../../group_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class GroupRelUserBrowserTableCellRenderer extends DefaultGroupRelUserTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function GroupRelUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $groupreluser)
	{
		if ($column === GroupRelUserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($groupreluser);
		}
		
		// Add special features here
		switch ($column->get_object_property())
		{
			// Exceptions that need post-processing go here ...
			case 'User' :
				$user_id = parent :: render_cell($column, $groupreluser);
				$user = UserManager :: retrieve_user($user_id);
//				return '<a href="' . Path :: get(WEB_PATH) . 'index_user.php?go=view&id=' . $user->get_id() .
//					'">' . $user->get_username() . '</a>';
				return $user->get_username();
		}
		return parent :: render_cell($column, $groupreluser);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($groupreluser)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_group_rel_user_unsubscribing_url($groupreluser),
			'label' => Translation :: get('Unsubscribe'),
			'img' => Theme :: get_common_image_path().'action_delete.png'
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>