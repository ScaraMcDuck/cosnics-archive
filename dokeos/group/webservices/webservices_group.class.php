<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_group.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once Path :: get_webservice_path() . '/security/webservice_security_manager.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';
require_once Path :: get_library_path() . 'validator/validator.class.php';


$handler = new WebServicesGroup();
$handler->run();

class WebServicesGroup
{
	private $webservice;
	private $functions;
    private $validator;
	
	function WebServicesGroup()
	{
		$this->webservice = Webservice :: factory($this);
        $this->wsm = WebserviceSecurityManager :: get_instance($this);
        $this->validator = Validator :: get_validator('group');
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['get_group'] = array(
			'input' => new Group(),
			'output' => new Group(),
            'require_hash' => true
		);
		
		$functions['create_group'] = array(
			'input' => new Group(),
			'require_hash' => true
		);

        $functions['update_group'] = array(
			'input' => new Group(),
			'require_hash' => true
		);

		$functions['delete_group'] = array(
			'input' => new Group(),
			'require_hash' => true
		);
		
		$functions['subscribe_user'] = array(
			'input' => new GroupRelUser(),
			'require_hash' => true
		);
		
		$functions['unsubscribe_user'] = array(
			'input' => new GroupRelUser(),
			'require_hash' => true
		);
		
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function get_group(&$input_group)
	{
        if($this->webservice->can_execute($input_group, 'get group'))
		{
            $gdm = DatabaseGroupDataManager :: get_instance();
            if($this->validator->validate_retrieve($input_group))
            {
                $group = $gdm->retrieve_group_by_name($input_group[name]);
                if(count($group->get_default_properties())>0)
                {
                    return $group->get_default_properties();
                }
                else
                {
                    return $this->webservice->raise_error('Group '.$input_group[name].' not found.');
                }
            }
            else
            {
                return $this->webservice->raise_error("Could not retrieve group. Please check the data you've provided.");
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
	function create_group(&$input_group)
	{
        if($this->webservice->can_execute($input_group, 'create group'))
		{
            unset($input_group[hash]);
            if($this->validator->validate_create($input_group))
            {
                $g = new Group(0,$input_group);
                return $this->webservice->raise_message($g->create());
            }
            else
            {
                return $this->webservice->raise_error("Could not create group. Please check the data you've provided.");
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
	}

    function update_group($input_group)
	{
		if($this->webservice->can_execute($input_group, 'update group'))
		{
            unset($input_group[hash]);
            if($this->validator->validate_update($input_group))
            {
                $g = new Group(0,$input_group);
                return $this->webservice->raise_message($g->update());
            }
            else
            {
                return $this->webservice->raise_error("Could not update group. Please check the data you've provided.");
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
	function delete_group(&$input_group)
	{
		if($this->webservice->can_execute($input_group, 'delete group'))
		{
            unset($input_group[hash]);
            if($this->validator->validate_delete($input_group))
            {
                $g = new Group(0,$input_group);
                return $this->webservice->raise_message($g->delete());
            }
            else
            {
                return $this->webservice->raise_error("Could not delete group. Please check the data you've provided.");
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
	function subscribe_user(&$input_group_rel_user)
	{
        if($this->webservice->can_execute($input_group_rel_user, 'subscribe user'))
		{
            unset($input_group_rel_user[hash]);
            if($this->validator->validate_subscribe_or_unsubscribe($input_group_rel_user))
            {
                $gru = new GroupRelUser($input_group_rel_user[group_id],$input_group_rel_user[user_id]);
                return $this->webservice->raise_message($gru->create());
            }
            else
            {
                return $this->webservice->raise_error("Could not subscribe user to group. Please check the data you've provided.");
            }
         }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
	function unsubscribe_user(&$input_group_rel_user)
	{
        if($this->webservice->can_execute($input_group_rel_user, 'unsubscribe user'))
		{
            unset($input_group_rel_user[hash]);
            if($this->validator->validate_subscribe_or_unsubscribe($input_group_rel_user))
            {
                $gru = new GroupRelUser($input_group_rel_user[group_id],$input_group_rel_user[user_id]);
                return $this->webservice->raise_message($gru->delete());
            }
            else
            {
                return $this->webservice->raise_error("Could not unsubscribe user from group. Please check the data you've provided.");
            }
         }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
}