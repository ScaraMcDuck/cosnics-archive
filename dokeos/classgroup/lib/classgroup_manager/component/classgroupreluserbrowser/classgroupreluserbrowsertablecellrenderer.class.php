<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/classgroupreluserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../classgroup_rel_user_table/defaultclassgrouprelusertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../classgroup.class.php';
require_once dirname(__FILE__).'/../../classgroupmanager.class.php';
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
		switch ($column->get_classgroup_rel_user_property())
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
			'img' => Theme :: get_common_img_path().'delete.gif'
		);
//		
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_classgroup_roles_url($classgroupreluser),
//			'label' => Translation :: get('UserRoles'),
//			'img' => Theme :: get_common_img_path().'img/config.png'
//		);
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>