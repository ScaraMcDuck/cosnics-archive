<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', 7200);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/course_import.csv';
$courses = parse_csv($file);
$location = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

foreach($courses as $course)
{
	$action = $course['action'];
	switch($action)
	{
		case 'I': create_course($course); break;
		case 'i': create_course($course); break;
		case 'U': update_course($course); break;
		case 'u': update_course($course); break;
		case 'D': delete_course($course); break;
		case 'd': delete_course($course); break;       

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
		$courses = array();
		
		while($course_data = fgetcsv($fp, 1000, ";"))
		{
			$course = array();
			foreach($keys as $index => $key)
			{
				$course[$key] = trim($course_data[$index]);
			}
			$courses[] = $course;
		}
		fclose($fp);
	}
	else
	{
		log("ERROR: Can't open file ($file)");
	}
	
	return $courses;
}

function create_course($course)
{
	global $hash, $client;
	log_message('Creating course ' . $course['title']);
	$hash = ($hash == '') ? login() : $hash;
    $course['hash'] = $hash;
    $course['titular'] = 'Soliber';
    $course['category'] = 'Language skills';
    $course['disk_quota'] = '200';
	$result = $client->call('WebServicesCourse.create_course', $course);    
	if($result == 1)
    {
        log_message(print_r('Course successfully created', true));
    }
    else
    	log_message(print_r($result, true));
}

function update_course($course)
{
	global $hash, $client;
	log_message('Updating course ' . $course['title']);
	$hash = ($hash == '') ? login() : $hash;
    $course['hash'] = $hash;
    //$course['id'] = '33';
    //$course['titular'] = 'Soliber';
    //$course['category'] = 'Language skills';
    //$course['disk_quota'] = '200';
	$result = $client->call('WebServicesCourse.update_course', $course);
    if($result == 1)
    {
        log_message(print_r('Course successfully updated', true));
    }
    else
    	log_message(print_r($result, true));
}

function delete_course($course)
{
	global $hash, $client;
	log_message('Deleting course ' . $course['title']);
	$hash = ($hash == '') ? login() : $hash;
    $course['hash'] = $hash;
    //$course['id'] = '51';
	$result = $client->call('WebServicesCourse.delete_course', $course);
    if($result == 1)
    {
        log_message(print_r('Course successfully deleted', true));
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