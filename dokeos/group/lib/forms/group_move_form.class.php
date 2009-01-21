<?php
/**
 * @package groups.lib.groupmanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../group.class.php';
require_once dirname(__FILE__).'/../group_data_manager.class.php';

class GroupMoveForm extends FormValidator 
{
    private $group;
    private $locations = array();
    private $level = 1;
    private $gdm;
    
    function GroupMoveForm($group, $action, $user) 
    {
    	parent :: __construct('group_move', 'post', $action);
    	$this->group = $group;
    	
    	$this->gdm = GroupDataManager :: get_instance();
    	
    	if($group->get_parent() != 0)
    		$this->locations[0] = Translation :: get('Groups');
    	$this->get_locations(0);
    	
		$this->build_form();
    }

    function build_form()
    {
    	$this->addElement('select', 'location', Translation :: get('NewLocation'),$this->locations);
		//$this->addElement('submit', 'group_export', Translation :: get('Ok'));
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Ok'), array('class' => 'positive'));
		//$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    function get_locations($parent)
    {
    	$conditions[] = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $this->group->get_id()));
    	$conditions[] = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $this->group->get_parent()));
    	$conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $parent);
    	$condition = new AndCondition($conditions);	
    	
    	$groups = $this->gdm->retrieve_groups($condition);
    	while($group = $groups->next_result())
    	{
    		$this->locations[$group->get_id()] = str_repeat('--', $this->level) . ' ' . $group->get_name();
    		$this->level++;
    		$this->get_locations($group->get_id());
    		$this->level--;
    	}
    }
    
    function move_group()
    {
    	$new_parent = $this->exportValue('location');
    	$this->group->set_parent($new_parent);
    	return $this->group->update();
    }
    
    function get_new_parent()
    {
    	return $this->exportValue('location');
    }
}
?>