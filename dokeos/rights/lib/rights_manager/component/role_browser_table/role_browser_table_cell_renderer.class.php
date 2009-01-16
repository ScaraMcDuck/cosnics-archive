<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/role_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../role_table/default_role_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../role.class.php';
require_once dirname(__FILE__).'/../../rights_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RoleBrowserTableCellRenderer extends DefaultRoleTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function RoleBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $role)
	{
		if ($column === RoleBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($role);
		}
		
		return parent :: render_cell($column, $role);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($role)
	{
		$toolbar_data = array();
		
		$editing_url = $this->browser->get_role_editing_url($role);
		$toolbar_data[] = array(
			'href' => $editing_url,
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png',
		);
		
		$deleting_url = $this->browser->get_role_deleting_url($role);
		$toolbar_data[] = array(
			'href' => $deleting_url,
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>