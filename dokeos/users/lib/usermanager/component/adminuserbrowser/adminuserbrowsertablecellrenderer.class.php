<?php
/**
 * @package users.lib.usermanager.component.adminuserbrowser
 */
require_once dirname(__FILE__).'/adminuserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../user_table/defaultusertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../user.class.php';
require_once dirname(__FILE__).'/../../usermanager.class.php';
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
		switch ($column->get_user_property())
		{
			// Exceptions that need post-processing go here ...
			case User :: PROPERTY_STATUS :
				if ($user->get_status() == '1')
				{
					return Translation :: get_lang('CourseAdmin');
				}
				else
				{
					return Translation :: get_lang('Student');
				}
			case User :: PROPERTY_PLATFORMADMIN :
				if ($user->get_platformadmin() == '1')
				{
					return Translation :: get_lang('PlatformAdmin');
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
			'label' => Translation :: get_lang('Edit'),
			'img' => $this->browser->get_path(WEB_IMG_PATH).'edit.gif'
		);

		$toolbar_data[] = array(
			'href' => $this->browser->get_user_quota_url($user),
			'label' => Translation :: get_lang('VersionQuota'),
			'img' => $this->browser->get_path(WEB_IMG_PATH).'versions.gif'
		);

		if($user->get_user_id() != api_get_user_id())
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_user_delete_url($user),
				'label' => Translation :: get_lang('Delete'),
				'img' => $this->browser->get_path(WEB_IMG_PATH).'delete.gif'
			);
		}

		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>