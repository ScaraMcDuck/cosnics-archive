<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', 7200);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/group_update.csv';
$groups = parse_csv($file);
$location = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

foreach($groups as $group)
{
	update_group($group);
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

function login()
{
    global $client;
	$username = 'Soliber';
	$password = '58350136959beae3f874cd512ebcf320a7afa507';
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