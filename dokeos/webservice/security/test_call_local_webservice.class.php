<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__).'/../lib/webservice_credential.class.php';


$handler = new TestCallLocalWebservice();
$handler->run();
class TestCallLocalWebservice
{
	private $webservice;
	
	function TestCallLocalWebservice()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{		
		//$wsdl = Path :: get(WEB_PATH) . 'webservice/security/webservice_login.class.php?wsdl';
		$wsdl = Path :: get(WEB_PATH) . 'user/webservices/retrive_user.class.php?wsdl';
		$functions = array();
		$user = new User();
		$user->set_username('Soliber');
		$user->set_password('werk');
        $user = $user->get_default_properties();
        $user['hash'] = '';
		$functions[] = array(
				'name' => 'RetriveUser.retrive',
				'parameters' =>$user,
				'handler' => 'handle_webservice'
		);

        //echo readfile('http://localhost/webservice/security/remote_addr.php');
        
		/*$wsdl = Path :: get(WEB_PATH) . 'webservice/security/webservice_login.class.php?wsdl';
		$functions = array();
		$c = new WebserviceCredential();
		$c->set_hash('d41d8cd98f00b204e9800998ecf8427e');
		$functions[] = array(
				'name' => 'WebServiceLogin.complete_login',
				'parameters' =>$c->get_default_properties(),
				'handler' => 'handle_webservice'
		);*/		
		

		$this->webservice->call_webservice($wsdl, $functions,'d41d8cd98f00b204e9800998ecf8427e');
}
	
	function handle_webservice($result)
	{
		/*global $file;
		fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");*/
		dump($result);
	}
}


?>