<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServiceUpdateUser();
$handler->run();

class WebServiceUpdateUser
{
	private $webservice;
	private $functions;
	
	function WebServiceUpdateUser()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['update_user'] = array(
			'input' => new User(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function update_user($input_user)
	{
		$u = new User(0,$input_user);
		$udm = DatabaseUserDataManager :: get_instance();
		$success = new ActionSuccess();
		$success->set_success($udm->update_user($u));
		return $success->get_default_properties();	
	}
}