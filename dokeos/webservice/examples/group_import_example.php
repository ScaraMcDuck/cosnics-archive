<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', 7200);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/group_subscribe.csv';
$groups = parse_csv($file);
$location = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

foreach($groups as $group)
{
	$action = $group['action'];
	switch($action)
	{
		case 'I': create_group($group); break;
		case 'i': create_group($group); break;
		case 'U': update_group($group); break;
		case 'u': update_group($group); break;
		case 'D': delete_group($group); break;
		case 'd': delete_group($group); break;
        case 'S': subscribe_user($group); break;
        case 's': subscribe_user($group); break;
        case 'US': unsubscribe_user($group); break;
        case 'us': unsubscribe_user($group); break;
	}
}

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Execution time was  $time seconds\n";

function parse_csv($file)
{
	if(file_exists($file) && $fp = fopen($file, "r"))
	{
		$keys = fgetcsv($fp, 1000, ";");
		$groups = array();
		while($group_data = fgetcsv($fp, 1000, ";"))
		{
			$group = array();
			foreach($keys as $index => $key)
			{
				$group[$key] = trim($group_data[$index]);
			}
			$groups[] = $group;
		}
		fclose($fp);
	}
	else
	{
		log("ERROR: Can't open file ($file)");
	}
    return $groups;
}

function create_group($group)
{
    global $hash, $client;
	log_message('Creating group ' . $group['name']);
    if(empty($hash))
    $hash = login();
    $group['hash'] = $hash;
    $result = $client->call('WebServicesGroup.create_group', $group);
    if($result == 1)
    {
        log_message(print_r('Group successfully created', true));
    }
    else
    	log_message(print_r($result, true));
}

function update_group($group)
{
    global $hash, $client;
	log_message('Updating group ' . $group['name']);
	if(empty($hash))
    $hash = login();
    $group['hash'] = $hash;
    $result = $client->call('WebServicesGroup.update_group', $group);
    if($result == 1)
    {
        log_message(print_r('Group successfully updated', true));
    }
    else
    	log_message(print_r($result, true));
}

function delete_group($group)
{
    global $hash, $client;
	log_message('Deleting group: ' . $group['name']);
    if(empty($hash))
    $hash = login();
    $group['hash'] = $hash;
	$result = $client->call('WebServicesGroup.delete_group', $group);
    if($result == 1)
    {
        log_message(print_r('Group successfully deleted', true));
    }
    else
    	log_message(print_r($result, true));
}

function subscribe_user($group_rel_user)
{
    global $hash, $client;
	log_message('Subscribing user ' . $group_rel_user['user_id'] . ' to group '. $group_rel_user['group_id']);
    if(empty($hash))
    $hash = login();
    $group_rel_user['hash'] = $hash;
	$result = $client->call('WebServicesGroup.subscribe_user', $group_rel_user);
    if($result == 1)
    {
        log_message(print_r('User successfully subscribed', true));
    }
    else
    	log_message(print_r($result, true));
}

function unsubscribe_user($group_rel_user)
{
    global $hash, $client;
	log_message('Unsubscribing user ' . $group_rel_user['user_id'] . ' to group '. $group_rel_user['group_id']);
    if(empty($hash))
    $hash = login();
    $group_rel_user['hash'] = $hash;
	$result = $client->call('WebServicesGroup.unsubscribe_user', $group_rel_user);
    if($result == 1)
    {
        log_message(print_r('User successfully unsubscribed', true));
    }
    else
    	log_message(print_r($result, true));
}

function login()
{
    global $client;
	$username = 'Soliber';
	$password = 'c14d68b0ef49d97929c36f7725842b5adbf5f006';
    $login_client = new nusoap_client('http://localhost/user/webservices/login_webservice.class.php?wsdl', 'wsdl');
	$result = $login_client->call('LoginWebservice.login', array('username' => $username, 'password' => $password));
    //log_message(print_r($result, true));
    if(!empty($result['hash']))
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