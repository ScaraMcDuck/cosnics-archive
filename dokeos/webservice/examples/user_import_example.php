<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';

$file = dirname(__FILE__) . '/user_import.csv';
$users = parse_csv($file);
$location = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

//dump($users);

foreach($users as $user)
{
	$action = $user['action'];
	switch($action)
	{
		case 'I': create_user($user); break;
		case 'i': create_user($user); break;
		case 'U': update_user($user); break;
		case 'u': update_user($user); break;
		case 'D': delete_user($user); break;
		case 'd': delete_user($user); break;
	}
}

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
		log("FOUT: Kan het bestand niet openen ($file)");
	}
	
	return $users;
}

function create_user($user)
{
	global $hash, $client;
	log_message('Creating user ' . $user['username']);	
	$hash = ($hash == '') ? login() : $hash;
    $user['hash'] = $hash;
    $user['password'] = 'ae12e345f679aaf';
    $user['registration_date'] = '0';
    $user['disk_quota'] = '209715200';
    $user['database_quota'] = '300';
    $user['version_quota'] = '20';    
	$result = $client->call('WebServicesUser.create_user', $user);    
	if($result == 1)
    {
        log_message(print_r('User successfully created', true));
    }
    else
    	log_message(print_r($result, true));
}

function update_user($user)
{
	global $hash, $client;
	log_message('Updating user ' . $user['username']);	
	$hash = ($hash == '') ? login() : $hash;
    $user['hash'] = $hash;
    $user['user_id'] = '17';
    $user['password'] = 'ae12e345f679aaf';
    $user['registration_date'] = '0';
    $user['disk_quota'] = '209715200';
    $user['database_quota'] = '300';
    $user['version_quota'] = '20';    
	$result = $client->call('WebServicesUser.update_user', $user);
    if($result == 1)
    {
        log_message(print_r('User successfully updated', true));
    }
    else
    	log_message(print_r($result, true));
}

function delete_user($user)
{
	global $hash, $client;
	log_message('Deleting user: ' . $user['username']);
    $user['hash'] = $hash;
    $user['user_id'] = '44';
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
	
	$username = 'Soliber';
	$password = '58350136959beae3f874cd512ebcf320a7afa507';
	
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