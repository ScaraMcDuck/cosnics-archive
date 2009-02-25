<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_user.class.php';
require_once dirname(__FILE__) . '/provider/output_user.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';

$handler = new WebServiceGetUser();
$handler->run();

class WebServiceGetUser
{
	private $webservice;
	private $functions;
	
	function WebServiceGetUser()
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
		
		
		$this->webservice->provide_webservice($functions);

		//$this->webservice->provide_webservice_with_wsdl(dirname(__FILE__) . "/wsdl.xml");
	}
	
	function get_user($input_user)
	{
		$udm = DatabaseUserDataManager :: get_instance();
		$user = $udm->retrieve_user($input_user[id]);
		return $user->to_array();
	}
}