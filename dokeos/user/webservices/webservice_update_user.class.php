<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_user.class.php';
require_once dirname(__FILE__) . '/provider/output_user.class.php';
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

		//$this->webservice->provide_webservice_with_wsdl(dirname(__FILE__) . "/wsdl.xml");
	}
	
	function update_user($input_user)
	{
		$u = new User();
		$u->set_default_properties($input_user);
		$udm = DatabaseUserDataManager :: get_instance();
		$success = new ActionSuccess();
		$success->set_success($udm->update_user($u));
		return $success->to_array();	
	}
}