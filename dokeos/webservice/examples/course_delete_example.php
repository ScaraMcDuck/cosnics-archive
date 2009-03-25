<?php
require_once dirname(__FILE__) . '/../../plugin/nusoap/nusoap.php';
ini_set('max_execution_time', 7200);
$time_start = microtime(true);

$file = dirname(__FILE__) . '/course_delete.csv';
$courses = parse_csv($file);
$location = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
$client = new nusoap_client($location, 'wsdl');
$hash = '';

foreach($courses as $course)
{
	delete_course($course);
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