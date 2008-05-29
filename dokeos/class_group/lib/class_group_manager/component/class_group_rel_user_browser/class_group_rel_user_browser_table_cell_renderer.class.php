<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/class_group_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../class_group_rel_user_table/default_class_group_rel_user_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../class_group.class.php';
require_once dirname(__FILE__).'/../../class_group_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ClassGroupRelUserBrowserTableCellRenderer extends DefaultClassGroupRelUserTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ClassGroupRelUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $classgroupreluser)
	{
		if ($column === ClassGroupRelUserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($classgroupreluser);
		}
		
		// Add special features here
		switch ($column->get_object_property())
		{
			// Exceptions that need post-processing go here ...
			case 'User' :
				$user_id = parent :: render_cell($column, $classgroupreluser);
				$user = UserManager :: retrieve_user($user_id);
				return $user->get_username();
		}
		return parent :: render_cell($column, $classgroupreluser);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($classgroupreluser)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_classgroup_rel_user_unsubscribing_url($classgroupreluser),
			'label' => Translation :: get('Unsubscribe'),
			'img' => Theme :: get_common_img_path().'action_delete.png'
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>