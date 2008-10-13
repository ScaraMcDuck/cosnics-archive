<?php
/**
 * @package users.lib.usermanager.component.adminuserbrowser
 */
require_once dirname(__FILE__).'/admin_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../user_table/default_user_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../user.class.php';
require_once dirname(__FILE__).'/../../user_manager.class.php';
/**
 * Cell renderer for the user object browser table
 */
class AdminUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
	/**
	 * The user browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function AdminUserBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $user)
	{
		if ($column === AdminUserBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($user);
		}

		// Add special features here
		switch ($column->get_object_property())
		{
			// Exceptions that need post-processing go here ...
			case User :: PROPERTY_STATUS :
				if ($user->get_status() == '1')
				{
					return Translation :: get('CourseAdmin');
				}
				else
				{
					return Translation :: get('Student');
				}
			case User :: PROPERTY_PLATFORMADMIN :
				if ($user->get_platformadmin() == '1')
				{
					return Translation :: get('PlatformAdmin');
				}
				else
				{
					return '';
				}
		}
		return parent :: render_cell($column, $user);
	}
	/**
	 * Gets the action links to display
	 * @param $user The user for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($user)
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->browser->get_user_editing_url($user),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'action_edit.png'
		);

		$toolbar_data[] = array(
			'href' => $this->browser->get_user_quota_url($user),
			'label' => Translation :: get('VersionQuota'),
			'img' => Theme :: get_common_img_path().'action_statistics.png'
		);

		if($user->get_id() != Session :: get_user_id())
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_user_delete_url($user),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_img_path().'action_delete.png'
			);
		}

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>