<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';

$file = dirname(__FILE__) . '/user_import.csv';
$users = parse_csv($file);
$location = 'http://localhost/lcms/user/webservices/webservices_user.class.php';
$client = new nusoap_client($location, 'wsdl');

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

$hash = '';

function create_user($user)
{
	global $hash, $client;
	log_message('Creating user ' . $user['official_code']);
	
	$hash = ($hash == '') ? login() : $hash;
	$result = $client->call('create_user', array('user' => $user, 'hash' => $hash));
	
	log_message(print_r($result, true));
}

function update_user($user)
{
	global $hash, $client;
	log_message('Updating user ' . $user['official_code']);
	
	$hash = ($hash == '') ? login() : $hash;
	$result = $client->call('update_user', array('user' => $user, 'hash' => $hash));
	
	log_message(print_r($result, true));
}

function delete_user($user)
{
	global $hash, $client;
	log_message('Deleting user: ' . $user['official_code']);
	
	$hash = ($hash == '') ? login() : $hash;
	$result = $client->call('delete_user', array('official_code' => $user['official_code'], 'hash' => $hash));
	
	log_message(print_r($result, true));
}

function login()
{
	global $client, $username, $password;
	
	$login_client = new nusoap_client('http://localhost/lcms/user/webservices/retrive_user.class.php?wsdl', 'wsdl');
	$result = $login_client->call('retrive_user', array('username' => $username, 'password' => $password));
	dump($result);
	return $result['hash'];
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

?>