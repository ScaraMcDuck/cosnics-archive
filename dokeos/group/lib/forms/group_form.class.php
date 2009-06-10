<?php
require_once dirname(__FILE__).'/../../../common/global.inc.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once Path :: get_group_path() . 'lib/group.class.php';
require_once Path :: get_group_path() . 'lib/group_data_manager.class.php';
require_once Path :: get_group_path() . 'lib/group_menu.class.php';

class GroupForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'GroupUpdated';
	const RESULT_ERROR = 'GroupUpdateFailed';

	private $parent;
	private $group;
	private $unencryptedpass;
	private $user;

    function GroupForm($form_type, $group, $action, $user) {
    	parent :: __construct('groups_settings', 'post', $action);

    	$this->group = $group;
    	$this->user = $user;
		$this->form_type = $form_type;
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}

		$this->setDefaults();
    }

    function build_basic_form()
    {
		$this->addElement('text', Group :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
		$this->addRule(Group :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->addElement('select', Group :: PROPERTY_PARENT, Translation :: get('Location'), $this->get_groups());
		$this->addRule(Group :: PROPERTY_PARENT, Translation :: get('ThisFieldIsRequired'), 'required');

		$this->add_html_editor(Group :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
		$this->addRule(Group :: PROPERTY_DESCRIPTION, Translation :: get('ThisFieldIsRequired'), 'required');

		// Roles element finder
		$group = $this->group;

		if ($this->form_type == self :: TYPE_EDIT)
		{
			$linked_roles = $group->get_roles();
			$group_roles = RightsUtilities :: roles_for_element_finder($linked_roles);
		}
		else
		{
			$group_roles = array();
		}

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

		$elem = $this->addElement('element_finder', 'roles', null, $url, $locale, $group_roles);
		$elem->setDefaults($defaults);
		$elem->setDefaultCollapsed(count($group_roles) == 0);

		//$this->addElement('submit', 'group_settings', 'OK');
    }

    function build_editing_form()
    {
    	$group = $this->group;
    	$parent = $this->parent;

    	$this->build_basic_form();

    	$this->addElement('hidden', Group :: PROPERTY_ID);

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_group()
    {
    	$group = $this->group;
    	$values = $this->exportValues();

    	$group->set_name($values[Group :: PROPERTY_NAME]);
    	$group->set_description($values[Group :: PROPERTY_DESCRIPTION]);
    	$value = $group->update();

    	$new_parent = $values[Group :: PROPERTY_PARENT];
    	if ($group->get_parent() != $new_parent)
    	{
    		$group->move($new_parent);
    	}

		if (!$group->update_role_links($values['roles']))
		{
			return false;
		}

   		if($value)
   		{
   			Events :: trigger_event('update', 'group', array('target_group_id' => $group->get_id(), 'action_user_id' => $this->user->get_id()));
   		}

   		return $value;
    }

    function create_group()
    {
    	$group = $this->group;
    	$values = $this->exportValues();

    	$group->set_name($values[Group :: PROPERTY_NAME]);
    	$group->set_description($values[Group :: PROPERTY_DESCRIPTION]);
    	$group->set_parent($values[Group :: PROPERTY_PARENT]);

   		$value = $group->create();

		foreach ($values['roles'] as $role_id)
		{
			$group->add_role_link($role_id);
		}

   		if($value)
   		{
   			Events :: trigger_event('create', 'group', array('target_group_id' => $group->get_id(), 'action_user_id' => $this->user->get_id()));
   		}

   		return $value;
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$group = $this->group;
		$defaults[Group :: PROPERTY_ID] = $group->get_id();
		$defaults[Group :: PROPERTY_PARENT] = $group->get_parent();
		$defaults[Group :: PROPERTY_NAME] = $group->get_name();
		$defaults[Group :: PROPERTY_DESCRIPTION] = $group->get_description();
		parent :: setDefaults($defaults);
	}

	function get_group()
	{
		return $this->group;
	}

	function get_groups()
	{
		$group = $this->group;

		$group_menu = new GroupMenu($group->get_id(), null, true, true);
		$renderer = new OptionsMenuRenderer();
		$group_menu->render($renderer, 'sitemap');
		return $renderer->toArray();
	}
}
?>