<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/tables/registration_table/default_registration_table_cell_renderer.class.php';
require_once Path :: get_admin_path() . 'lib/registration.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RegistrationBrowserTableCellRenderer extends DefaultRegistrationTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function RegistrationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $registration)
	{
		if ($column === RegistrationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($registration);
		}

		// Add special features here
		switch ($column->get_object_property())
		{
		}

		switch($column->get_title())
		{
		}

		return parent :: render_cell($column, $registration);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($registration)
	{
		$toolbar_data = array();

//		$toolbar_data[] = array(
//			'href' => $this->browser->get_group_editing_url($group),
//			'label' => Translation :: get('Edit'),
//			'img' => Theme :: get_common_image_path().'action_edit.png'
//		);
//
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_group_suscribe_user_browser_url($group),
//			'label' => Translation :: get('AddUsers'),
//			'img' => Theme :: get_common_image_path().'action_subscribe.png',
//		);
//
//		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
//		$users = $this->browser->retrieve_group_rel_users($condition);
//		$visible = ($users->size() > 0);
//
//		if($visible)
//		{
//			$toolbar_data[] = array(
//				'href' => $this->browser->get_group_emptying_url($group),
//				'label' => Translation :: get('Truncate'),
//				'img' => Theme :: get_common_image_path().'action_recycle_bin.png',
//			);
//		}
//		else
//		{
//			$toolbar_data[] = array(
//				'label' => Translation :: get('TruncateNA'),
//				'img' => Theme :: get_common_image_path().'action_recycle_bin_na.png',
//			);
//		}
//
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_group_delete_url($group),
//			'label' => Translation :: get('Delete'),
//			'img' => Theme :: get_common_image_path().'action_delete.png'
//		);
//
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_move_group_url($group),
//			'label' => Translation :: get('Move'),
//			'img' => Theme :: get_common_image_path().'action_move.png'
//		);
//
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_manage_roles_url($group),
//			'label' => Translation :: get('ManageRoles'),
//			'img' => Theme :: get_common_image_path().'action_rights.png'
//		);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>