<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../user_data_manager.class.php';

class UserRoleManagerForm extends FormValidator
{
	private $parent;
	private $user;
	private $form_user;

	/**
	 * Creates a new UserForm
	 * Used by the admin to create/update a user
	 */
    function UserRoleManagerForm($user, $form_user, $action)
    {
    	parent :: __construct('user_role_manager_form', 'post', $action);

    	$this->user = $user;
    	$this->form_user = $form_user;

		$this->build_basic_form();
    }

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
		// Roles element finder
		$user = $this->user;

		$linked_roles = $user->get_roles();
		$user_roles = RightsUtilities :: roles_for_element_finder($linked_roles);

		$roles = RightsDataManager :: get_instance()->retrieve_roles();
		while($role = $roles->next_result())
		{
			$defaults[$role->get_id()] = array('title' => $role->get_name(), 'description', $role->get_description(), 'class' => 'role');
		}

		$url = Path :: get(WEB_PATH).'rights/xml_feeds/xml_role_feed.php';
		$locale = array ();
		$locale['Display'] = Translation :: get('AddRoles');
		$locale['Searching'] = Translation :: get('Searching');
		$locale['NoResults'] = Translation :: get('NoResults');
		$locale['Error'] = Translation :: get('Error');
		$hidden = true;

		$elem = $this->addElement('element_finder', 'roles', null, $url, $locale, $user_roles);
		$elem->setDefaults($defaults);

		// Submit button
		//$this->addElement('submit', 'user_settings', 'OK');
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();
    }

    function update_user_roles()
    {
    	$user = $this->user;
		$values = $this->exportValues();
		return $user->update_role_links($values['roles']);
    }

}
?>