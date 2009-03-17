<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';

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
		$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.get_user',
				'parameters' => array('username' => 'Soliber','hash'=>'ef903275e1fcd9678c036b700db0fd21d4a8c430354fdf33da40b8058b74d0efc4220e6078024e33d1ee63cbcddbca4c86255d5c9857c94c5fc4cc7263996ec2'),
				'handler' => 'handle_webservice'
		);
		
		
		/*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesUser.get_all_users',
				'parameters' => array('hash'=>'8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/
		
		
		/*$user = array('username' => 'Kuchiki', 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78');
        $wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesUser.delete_user',
				'parameters' => $user,
		  		'handler' => 'handle_webservice'			
			);*/

		  /*$user = array (
		  'lastname' => 'Joske',
		  'firstname' => 'Den Os',
		  'username' => 'Joske',
		  'password' => '4a0091108fb271e05f34da7cf77c975f',
		  'auth_source' => 'platform',
		  'email' => 'admin@localhost.localdomain',
		  'status' => '1',
		  'admin' => '1',
		  'phone' => NULL,
		  'official_code' => 'ADMIN',
		  'picture_uri' => NULL,
		  'creator_id' => 'Soliber',
		  'language' => 'english',
		  'disk_quota' => '209715200',
		  'database_quota' => '300',
		  'version_quota' => '20',
		  'theme' => NULL,
		  'activation_date' => '0',
		  'expiration_date' => '0',
		  'registration_date' => '1234774883',
		  'active' => '1',
          'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'
		);
		$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesUser.create_user',
				'parameters' => $user,
		  		'handler' => 'handle_webservice'			
			);*/
		
		  /*$user = array (
		  'lastname' => 'Joske',
		  'firstname' => 'Den Os',
          'username' => 'Joske',
		  'password' => '4a0091108fb271e05f34da7cf77c975f',
		  'auth_source' => 'platform',
		  'email' => 'admin@localhost.localdomain',
		  'status' => '1',
		  'admin' => '1',
		  'phone' => NULL,
		  'official_code' => 'TEST_USER',
		  'picture_uri' => NULL,
		  'creator_id' => 'Soliber',
		  'language' => 'english',
		  'disk_quota' => '209715200',
		  'database_quota' => '300',
		  'version_quota' => '20',
		  'theme' => NULL,
		  'activation_date' => '0',
		  'expiration_date' => '0',
		  'registration_date' => '1234774883',
		  'active' => '1',
          'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'
		);
        $wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesUser.update_user',
				'parameters' => $user,
		  		'handler' => 'handle_webservice'			
			);*/
	
		$this->webservice->call_webservice($wsdl, $functions);
	}
	
	function handle_webservice($result)
	{
		//global $file;
		//fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");
		//echo ('<p>'.date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n".'</p>');
		//echo '<pre>'.var_export($result,true).'</pre>';
		dump($result);
	}
}

?>