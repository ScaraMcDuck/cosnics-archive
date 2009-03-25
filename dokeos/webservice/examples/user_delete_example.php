<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', 7200);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/user_delete.csv';
$users = parse_csv($file);
/*
 * change location to the location of the test server
 */
$location = 'http://www.dokeosplanet.org/demo_portal/user/webservices/webservices_user.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

//dump($users);

foreach($users as $user)
{
	delete_user($user);
}

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

function delete_user($user)
{
	global $hash, $client;
	log_message('Deleting user: ' . $user['username']);
    $user['hash'] = $hash;
    //$user['user_id'] = '45';
	$hash = ($hash == '') ? login() : $hash;
	$result = $client->call('WebServicesUser.delete_user', array('username' => $user['username'],'user_id'=> $user['user_id'],'hash' => $hash));
    if($result == 1)
    {
        log_message(print_r('User successfully deleted', true));
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
	$password = 'ef1fffa5a0b8736f2d8a5f0c913bb0930d1f16c0';

	$login_client = new nusoap_client('http://localhost/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
	$result = $login_client->call('LoginWebservice.login', array('username' => $username, 'password' => $password));

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
	echo date('[H:m] ', time()) . $text . '<br />';
}

function debug($client)
{
	echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
	echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
}



?>