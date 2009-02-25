<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_user.class.php';
require_once dirname(__FILE__) . '/provider/output_user.class.php';
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

		//$this->webservice->provide_webservice_with_wsdl(dirname(__FILE__) . "/wsdl.xml");
	}
	
	function create_user($input_user)
	{
		$u = new User();
		$u->set_default_properties($input_user);
		$udm = DatabaseUserDataManager :: get_instance();
		$success = new ActionSuccess();
		$success->set_success($udm->create_user($u));
		return $success->to_array();	
	}
}