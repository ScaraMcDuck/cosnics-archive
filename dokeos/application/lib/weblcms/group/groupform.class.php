<?php
/**
 * @package application.lib.weblcms.group
 * @author Bart Mollet
 */
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/user.class.php';
require_once dirname(__FILE__).'/group.class.php';

class GroupForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ObjectUpdated';
	const RESULT_ERROR = 'ObjectUpdateFailed';

	private $parent;
	private $group;
	private $form_type;

    function GroupForm($form_type, $group, $action) {
    	parent :: __construct('course_settings', 'post', $action);
		$this->form_type = $form_type;
		$this->group = $group;
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
		$this->addElement('text', Group :: PROPERTY_NAME, get_lang('Title'));
		$this->addRule(Group :: PROPERTY_NAME, get_lang('ThisFieldIsRequired'), 'required');
		$this->addElement('submit', 'group_settings', get_lang('Ok'));
    }

    function build_editing_form()
    {
    	$parent = $this->parent;

    	$this->build_basic_form();

    	$this->addElement('hidden', Group :: PROPERTY_ID);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();
    }

    function update_group()
    {
    	$group = $this->group;
    	$values = $this->exportValues();
    	$group->set_name($values[Group :: PROPERTY_NAME]);
    	return $group->update();
    }

    function create_group()
    {
    	$group = $this->group;
    	$values = $this->exportValues();

    	$group->set_name($values[Group :: PROPERTY_NAME]);

    	if ($group->create())
    	{
   			return true;
    	}
    	else
    	{
    		return false;
    	}
    }

	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$group = $this->group;
		$defaults[Group :: PROPERTY_NAME] = $group->get_name();
		parent :: setDefaults($defaults);
	}
}
?>