<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_user.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';
require_once Path :: get_webservice_path() . '/security/webservice_security_manager.class.php';
require_once Path :: get_library_path() . 'validator/validator.class.php';

$handler = new WebServicesUser();
$handler->run();

class WebServicesUser
{
	private $webservice;
	private $functions;
    private $validator;
	
	function WebServicesUser()
	{
		$this->webservice = Webservice :: factory($this);
		$this->wsm = WebserviceSecurityManager :: get_instance($this);
        $this->validator = Validator :: get_validator('user');
	}
	
	function run()
	{

		$functions = array();
		
		$functions['get_user'] = array(
			'input' => new User(),
			'output' => new User(),
            'require_hash' => true
        );
		
		$functions['get_all_users'] = array(
			'output' => array(new User()),
			'array' => true,
            'require_hash' => true
		);
		
		$functions['delete_user'] = array(
			'input' => new User(),
			'require_hash' => true
		);
		
		$functions['create_user'] = array(
			'input' => new User(),
			'require_hash' => true
		);
		
		$functions['update_user'] = array(
			'input' => new User(),
			'require_hash' => true
		);
        $this->webservice->provide_webservice($functions); //function and no hash
		//$this->webservice->check_rights($functions);
	}
	
	function get_user($input_user)
	{
        if($this->wsm->validate_function($input_user[hash]))
		{
            $udm = DatabaseUserDataManager :: get_instance();
            if($this->validator->validate_retrieve($input_user))
            {
                $user = $udm->retrieve_user_by_username($input_user[username]);
                if(isset($user) && count($user->get_default_properties())>0)
                {
                    return $user->get_default_properties();
                }
                else
                {
                    return $this->webservice->raise_error('User '.$input_user[username].' not found.');
                }
            }
            else
            {
                return $this->webservice->raise_error('Could not retrieve user. Please check the data you\'ve provided.');
            }
		}
        else
        {
            return $this->webservice->raise_error('Hash authentication failed.');
        }
		
	}
	
	
	function get_all_users($input_user)
	{
        if($this->wsm->validate_function($input_user[hash]))
		{
            $udm = DatabaseUserDataManager :: get_instance();
            $users = $udm->retrieve_users();
            $users = $users->as_array();
            if(!count($users)==0)
            {
                foreach($users as &$user)
                {
                    $user = $user->get_default_properties();
                }
                return $users;
            }
            else
            {
                return $this->webservice->raise_error('Hash authentication failed.');
            }
        }
        else
        {
            return $this->webservice->raise_error('Hash authentication failed.');
        }
	}
	
	function delete_user(&$input_user)
	{
        if($this->wsm->validate_function($input_user[hash]))
		{
            unset($input_user[hash]);
            if($this->validator->validate_delete($input_user))
            {
                $u = new User(0,$input_user);
                return $this->webservice->raise_message($u->delete());
            }
            else
            {
                return $this->webservice->raise_error('Could not delete user. Please check the data you\'ve provided.');
            }

        }
        else
        {
            return $this->webservice->raise_error('Hash authentication failed.');
        }
	}
	
	function create_user(&$input_user)
	{
        if($this->wsm->validate_function($input_user[hash]))
		{
            unset($input_user[hash]);
            if($this->validator->validate_create($input_user))
            {
                $u = new User(0,$input_user);
                return $this->webservice->raise_message($u->create());
            }
            else
            {
                return $this->webservice->raise_error('Could not create user. Please check the data you\'ve provided.');
            }
        }
        else
        {
            return $this->webservice->raise_error('Hash authentication failed.');
        }
	}
	
	function update_user(&$input_user)
	{
        if($this->wsm->validate_function($input_user[hash]))
		{
            unset($input_user[hash]);
            if($this->validator->validate_update($input_user))
            {
                $u = new User(0,$input_user);
                return $this->webservice->raise_message($u->update());
            }
            else
            {
                return $this->webservice->raise_error('Could not update user. Please check the data you\'ve provided.');
            }
        }
        else
        {
            return $this->webservice->raise_error('Hash authentication failed.');
        }
	}
	
	
	
	
}