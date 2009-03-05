<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_user.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServicesUser();
$handler->run();

class WebServicesUser
{
	private $webservice;
	private $functions;	
	
	function WebServicesUser()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['get_user'] = array(
			'input' => new InputUser(),
			'output' => new User()
		);
		
		$functions['get_all_users'] = array(
			'output' => array(new User()),
			'array' => true
		);
		
		$functions['delete_user'] = array(
			'input' => new User(),
			'output' => new ActionSuccess()
		);
		
		$functions['create_user'] = array(
			'input' => new User(),
			'output' => new ActionSuccess()
		);
		
		$functions['update_user'] = array(
			'input' => new User(),
			'output' => new ActionSuccess()
		);
		
		$functions['validate'] = array(
			'input' => new User(),
			'output' => new ActionSuccess()
		);

		$this->webservice->provide_webservice($functions);
	}
	
	function get_user($input_user)
	{		
		$udm = DatabaseUserDataManager :: get_instance();
		$user = $udm->retrieve_user($input_user[id]);
		if(isset($user))
		{					
			return $user->get_default_properties();
		}		
		else
		return new ActionSuccess(0);
	}
	
	
	function get_all_users()
	{
		$udm = DatabaseUserDataManager :: get_instance();
		$users = $udm->retrieve_users();
		$users = $users->as_array();
		foreach($users as &$user)
		{			
			$user = $user->get_default_properties();			
		}
		return $users;
	}
	
	function delete_user($input_user)
	{
		$u = new User(0,$input_user);
		$success = new ActionSuccess();
		$success->set_success($u->delete());
		return $success->get_default_properties();
	}
	
	function create_user($input_user)
	{
		$u = new User(0,$input_user);
		$success = new ActionSuccess();
		$success->set_success($u->create());
		return $success->get_default_properties();
	}
	
	function update_user($input_user)
	{
		$u = new User(0,$input_user);
		$success = new ActionSuccess();
		$success->set_success($u->update());
		return $success->get_default_properties();
	}	
	
	function validate($input_user)
	{
		
		$udm = DatabaseUserDataManager :: get_instance();
		//echo 'username : ' . $input_user[username];
		//echo 'password : ' . $input_user[password];
		$user = $udm->retrieve_user_by_username($input_user[username]);		
		if(isset($user)) //user exists
		{	
			$username = $input_user[username];		
			$db_password = $user->get_password();			
			$password = $input_user[password];
			if($db_password == $password) //check passwords
			{				
				echo 'username and password accepted';				
				echo 'creating hash : ';					
				$parameters = '' . $username . '' . $password;
				echo 'parameters : ' . $parameters;
				$hash = md5($parameters);
				echo 'hash : ' . $hash;
				echo '</body></html>';
				
			}
			else
			{
				echo 'wrong values';
			}			
		}
		else
		{
			echo 'input is geen user object';
		}		
	
	}
	
	
}