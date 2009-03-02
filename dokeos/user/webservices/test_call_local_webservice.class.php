<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';
/*// No PHP-memory limits
ini_set("memory_limit", "3500M"	);
// Two hours should be enough
ini_set("max_execution_time", "7200");
*/
$handler = new TestCallLocalWebservice();
/*
$start_total = microtime(true);
$file = fopen(dirname(__FILE__) . 'test.txt', 'w');
*/
$handler->run();
/*
$stop_total = microtime(true);
$time = $stop_total - $start_total;
fwrite($file, 'Total: ' . $time . ' s');
fclose($file);
*/
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
		
		{
			$functions[] = array(
				'name' => 'WebServicesUser.get_all_users',
				'parameters' => '',
				'handler' => 'handle_webservice'
			);
		}
		
		
		/*$user = new User();
		$user->set_id(27);
		echo '<pre>'.var_export($user->get_default_properties(),true).'</pre>';
		$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesUser.delete_user',
				'parameters' => $user->get_default_properties(),
		  		'handler' => 'handle_webservice'			
			);*/

		/*$user = new User();
		$user->set_default_properties(array (
		  'lastname' => 'Joske',
		  'firstname' => 'Den Os',
		  'username' => 'admin',
		  'password' => '4a0091108fb271e05f34da7cf77c975f',
		  'auth_source' => 'platform',
		  'email' => 'admin@localhost.localdomain',
		  'status' => '1',
		  'admin' => '1',
		  'phone' => NULL,
		  'official_code' => 'ADMIN',
		  'picture_uri' => NULL,
		  'creator_id' => NULL,
		  'language' => 'english',
		  'disk_quota' => '209715200',
		  'database_quota' => '300',
		  'version_quota' => '20',
		  'theme' => NULL,
		  'activation_date' => '0',
		  'expiration_date' => '0',
		  'registration_date' => '1234774883',
		  'active' => '1',
		));
		echo '<pre>'.var_export($user->get_default_properties(),true).'</pre>';
		$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesUser.create_user',
				'parameters' => $user->get_default_properties(),
		  		'handler' => 'handle_webservice'			
			);*/
		
		/*$user = new User();
		$user->set_default_properties(array (
		  'user_id' => '35',
		  'lastname' => 'Bond',
		  'firstname' => 'James',
		  'username' => 'admin',
		  'password' => '4a0091108fb271e05f34da7cf77c975f',
		  'auth_source' => 'platform',
		  'email' => 'admin@localhost.localdomain',
		  'status' => '1',
		  'admin' => '1',
		  'phone' => NULL,
		  'official_code' => 'ADMIN',
		  'picture_uri' => NULL,
		  'creator_id' => NULL,
		  'language' => 'english',
		  'disk_quota' => '209715200',
		  'database_quota' => '3000',
		  'version_quota' => '20',
		  'theme' => NULL,
		  'activation_date' => '0',
		  'expiration_date' => '0',
		  'registration_date' => '1234774883',
		  'active' => '1',
		));
		//echo '<pre>'.var_export($user->get_default_properties(),true).'</pre>';
		$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesUser.update_user',
				'parameters' => $user->get_default_properties(),
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