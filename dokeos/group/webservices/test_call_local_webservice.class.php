<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';
//require_once dirname(__FILE__) . '/../lib/group_rel_user.class.php';
require_once dirname(__FILE__) . '/provider/input_group.class.php';
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
		/*A test to retrieve a group from the db
		 * 
		 */
		
		/*$wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesGroup.get_group',
				'parameters' => array('id' => 4),
				'handler' => 'handle_webservice'
			);
		}*/
		
		
		
		/*A test to delete a group in the db
		 * 
		 */
		
		  /*$group = new InputGroup();
		  $group->set_id(4);
		  $wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesGroup.delete_group',
				'parameters' => $group->get_default_properties(),
		  		'handler' => 'handle_webservice'			
			);*/
		
		
		
		/*A test to create a group in the db
		 * 
		 */
		
		  /*$group = new Group();
		  $group->set_default_properties(array (
			    'id' => '5',
			    'name' => 'de coolste groep',
			    'description' => 'test',
			    'sort' => '1',
			    'parent' => '1',
			));
		  $wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesGroup.create_group',
				'parameters' => $group->get_default_properties(),
		  		'handler' => 'handle_webservice'			
			);*/
		
		
		/*A test to update a group in the db
		 * 
		 */
		
		  /*$group = new Group();
		  $group->set_default_properties(array (
				'id' => '3',
				'name' => 'Shinsengumi',
				'description' => 'cool',
				'sort' => '1',
				'parent' => '1',
		    ));
		  $wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesGroup.update_group',
				'parameters' => $group->get_default_properties(),
		  		'handler' => 'handle_webservice'			
			);*/
		
		
		
		/*A test subscribe a user to a group
		 * 
		 */
		
		  /*$group_rel_user = new GroupRelUser();
		  $group_rel_user->set_user_id(2);
		  $group_rel_user->set_group_id(1);
		  $wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesGroup.subscribe_user',
				'parameters' => $group_rel_user->get_default_properties(),
		  		'handler' => 'handle_webservice'			
			);*/
		
		
		
		/*A test subscribe a user to a group
		 * 
		 */
		
		  /*$group_rel_user = new GroupRelUser();
		  $group_rel_user->set_user_id(2);
		  $group_rel_user->set_group_id(1);
		  $wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesGroup.unsubscribe_user',
				'parameters' => $group_rel_user->get_default_properties(),
		  		'handler' => 'handle_webservice'			
			);*/
			
			
		$this->webservice->call_webservice($wsdl, $functions);
	}	
	function handle_webservice($result)
	{
		//global $file;
		//fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");
		//echo ('<p>'.date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n".'</p>');
		echo '<pre>'.var_export($result,true).'</pre>';
	}
}

?>