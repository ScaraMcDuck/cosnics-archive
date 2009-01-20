<?php
/**
 * @package groups.lib.groupmanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../group.class.php';
require_once dirname(__FILE__).'/../group_data_manager.class.php';

class GroupRoleManagerForm extends FormValidator 
{
	private $parent;
	private $group;
	private $form_group;

	/**
	 * Creates a new UserForm
	 * Used by the admin to create/update a group
	 */
    function GroupRoleManagerForm($group, $form_group, $action) 
    {
    	parent :: __construct('group_role_manager_form', 'post', $action);

    	$this->group = $group;
    	$this->form_group = $form_group;

		$this->build_basic_form();
    }

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
		// Roles element finder
		$group = $this->group;

		$linked_roles = $group->get_roles();
		$group_roles = RightsUtilities :: roles_for_element_finder($linked_roles);

		$roles = RightsDataManager :: get_instance()->retrieve_roles();
		while($role = $roles->next_result())
		{
			$defaults[$role->get_id()] = array('title' => $role->get_name(), 'description', $role->get_description(), 'class' => 'role');
		}
		
		$url = Path :: get(WEB_PATH).'rights/xml_role_feed.php';
		$locale = array ();
		$locale['Display'] = Translation :: get('AddRoles');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$hidden = true;
		
		$elem = $this->addElement('element_finder', 'roles', null, $url, $locale, $group_roles);
		$elem->setDefaults($defaults);
		
		// Submit button
		$this->addElement('submit', 'group_settings', 'OK');
    }

    function build_creation_form()
    {
    	$this->build_basic_form();
    }

    function update_group_roles()
    {
    	$group = $this->group;
		$values = $this->exportValues();
		return $group->update_role_links($values['roles']);
    }

}
?>