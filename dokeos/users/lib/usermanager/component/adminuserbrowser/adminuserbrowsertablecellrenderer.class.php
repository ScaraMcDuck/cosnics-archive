<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/adminuserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../user_table/defaultusertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../user.class.php';
require_once dirname(__FILE__).'/../../usermanager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class AdminUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
	/**
	 * The repository browser component
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
					return get_lang('CourseAdmin');
				}
				else
				{
					return get_lang('Student');
				}
			case User :: PROPERTY_PLATFORMADMIN :
				if ($user->get_platformadmin() == '1')
				{
					return get_lang('PlatformAdmin');
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
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($user)
	{
		$toolbar_data = array();
		
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_user_viewing_url($user),
//			'label' => get_lang('View'),
//			'img' => $this->browser->get_web_code_path().'img/home_small.gif'
//		);
//		
		$toolbar_data[] = array(
			'href' => $this->browser->get_user_editing_url($user),
			'label' => get_lang('Edit'),
			'img' => $this->browser->get_web_code_path().'img/edit.gif'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_user_quota_url($user),
			'label' => get_lang('VersionQuota'),
			'img' => $this->browser->get_web_code_path().'img/versions.gif'
		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>