<?php
/**
 * $Id:$
 * @package application.lib.weblcms.course_group
 * @author Bart Mollet
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
require_once dirname(__FILE__).'/course_group.class.php';

class CourseGroupSubscriptionsForm extends FormValidator 
{

	private $parent;
	private $course_group;
	private $form_type;

    function CourseGroupSubscriptionsForm($course_group, $action, $parent) 
    {
    	parent :: __construct('course_settings', 'post', $action);
		$this->course_group = $course_group;
		$this->parent = $parent;
		$this->wdm = WeblcmsDataManager :: get_instance();
		
		$this->build_basic_form();
    }

    function build_basic_form()
    {
		$subscribed_users = $this->wdm->retrieve_course_group_users($this->course_group);
		$all_users = $this->wdm->retrieve_course_users($this->parent->get_course());

		$udm = UserDataManager :: get_instance();
		
		while($user = $all_users->next_result())
		{
			$id = $user->get_user();
			$name = $udm->retrieve_user($id)->get_fullname();
			$all[$id] = $name;
		}
	
		while($sub = $subscribed_users->next_result())
		{
			$id = $sub->get_id();
			$subs[$id] = $id;
		}
		$this->subs = $subs;
		
		$this->addElement('advmultiselect', 'users', Translation :: get('SelectGroupUsers'), 
								  $all, array('style' => 'width:300px; height: 250px;'));
		$this->setDefaults(array('users' => $subs));
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();
    }

    function update_course_group_subscriptions()
    {
    	$values = $this->exportValues();
    	$subs = $this->subs;
    	
    	foreach($values['users'] as $value)
    	{
    		if(!array_key_exists($value, $subs))
    		{
    			$creation[] = $value;
    		}
    		else
    		{
    			unset($subs[$value]);
    		}
    	}
    	
    	if(count($values['users']) > $this->course_group->get_max_number_of_members() )
    		return false;
    		
    	if(count($subs) > 0)
    		$succes = $this->course_group->unsubscribe_users($subs);
    		
    	if(count($creation) > 0)
    		$succes &= $this->course_group->subscribe_users($creation);
    	
    	return $succes;
    }
}
?>