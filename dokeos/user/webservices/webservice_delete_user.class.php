<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServiceDeleteUser();
$handler->run();

class WebServiceDeleteUser
{
	private $webservice;
	private $functions;
	
	function WebServiceDeleteUser()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['delete_user'] = array(
			'input' => new User(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function delete_user($input_user)
	{
		$u = new User(0,$input_user);
		$udm = DatabaseUserDataManager :: get_instance();
		$success = new ActionSuccess();
		$success->set_success($udm->delete_user($u));
		return $success->get_default_properties();	
	}
}