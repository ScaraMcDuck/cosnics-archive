<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServiceCreateUser();
$handler->run();

class WebServiceCreateUser
{
	private $webservice;
	private $functions;
	
	function WebServiceCreateUser()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['create_user'] = array(
			'input' => new User(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function create_user($input_user)
	{
		$udm = DatabaseUserDataManager :: get_instance();
		$u = new User(0,$input_user);
		$u->set_id($udm->get_next_user_id());
		$success = new ActionSuccess();
		$success->set_success($udm->create_user($u));
		return $success->get_default_properties();	
	}
}