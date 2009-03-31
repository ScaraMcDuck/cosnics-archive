<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', 7200);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/user_import.csv';
$users = parse_csv($file);
/*
 * change location to the location of the test server
 */
$location = 'http://www.dokeosplanet.org/demo_portal/user/webservices/webservices_user.class.php?wsdl';
//$location = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

dump($users);

//create_users($users);
/*foreach($users as $user)
{
	create_user($user); 
}*/

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Execution time was  $time seconds\n";

function parse_csv($file)
{    
	if(file_exists($file) && $fp = fopen($file, "r"))
	{
		$keys = fgetcsv($fp, 1000, ";");
		$users = array();
		
		while($user_data = fgetcsv($fp, 1000, ";"))
		{
			$user = array();
			foreach($keys as $index => $key)
			{
				$user[$key] = trim($user_data[$index]);	
			}
			$users[] = $user;
		}
		fclose($fp);        
	}
	else
	{
		log("ERROR: Can't open file ($file)");
	}
	
	return $users;
}

function create_user(&$user)
{
	global $hash, $client;
	log_message('Creating user ' . $user['username']);	
	if($hash == '')
    $hash = login();    
    
    $user['password'] = 'ae12e345f679aaf';    
    $user['disk_quota'] = '209715200';
    $user['database_quota'] = '300';
    $user['version_quota'] = '20';
    $user['creator_id'] = 'admin';
    $user['registration_date'] = '0';
    log_message('CALL NAAR DE WEBSERVICE');
	$result = $client->call('WebServicesUser.create_user', array('input' => $user, 'hash' => $hash));
    log_message('RETURN GEKREGEN VAN DE WEBSERVICE');
	if($result == 1)
    {
        log_message(print_r('User successfully created', true));
    }
    else
    	log_message(print_r($result, true));
}

function create_users(&$users)
{
    global $hash, $client;
	log_message('Creating users ');
	if($hash == '')
    $hash = login();    
    
    $result = $client->call('WebServicesUser.create_users', array('input' => $users, 'hash' => $hash));
    
    if($result == 1)
    {
        log_message(print_r('Users successfully created', true));
    }
    else
    	log_message(print_r($result, true));

}

function login()
{    
	global $client;
	
	/* Change the username and password to the ones corresponding to  your database.
     * The password for the login service is :
     * IP = the ip from where the call to the webservice is made
     * PW = your hashed password from the db
     *
     * $password = Hash(IP+PW) ;
     */

	$username = 'admin';
	$password = '772d9ed50e3b34cbe3f9e36b77337c6b2f4e0cfa';

    //$username = 'Soliber';
//    $password = 'c14d68b0ef49d97929c36f7725842b5adbf5f006';
    //$password = hash('sha1,193.190.172.141',hash('sha1','admin'));
	//$username = 'admin';
	//$password = 'c14d68b0ef49d97929c36f7725842b5adbf5f006';

	
	/*
     * change location to server location for the wsdl
     */

	$login_client = new nusoap_client('http://www.dokeosplanet.org/demo_portal/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
    //$login_client = new nusoap_client('http://localhost/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
    $result = $login_client->call('LoginWebservice.login', array('input' => array('username' => $username, 'password' => $password), 'hash' => ''));
    log_message(print_r($result, true));
    if(is_array($result) && array_key_exists('hash', $result))
        return $result['hash']; //hash 3

    
		
	return '';
    
}

function dump($value)
{
	echo '<pre>';
	print_r($value);
	echo '</pre>';
}

function log_message($text)
{      
	echo date('[H:m:s] ', time()) . $text . '<br />';
}

function debug($client)
{
	echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
	echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';		
}



?>