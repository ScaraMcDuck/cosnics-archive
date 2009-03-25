<?php
require_once(dirname(__FILE__) . '/../../../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_course.class.php';
require_once dirname(__FILE__) . '/../course/course.class.php';

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
		/*A test to retrieve a course from the db
		 * 
		 */
		
		/*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_course',
				'parameters' => array('visual_code' => 'KIT', 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/
		
		/*A test to retrieve courses of a user from the db
		 * 
		 */
		
		/*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
	$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_user_courses',
				'parameters' => array('id' => 2, 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}
		
		/*A test to retrieve users of a course from the db
		 * 
		 */
		
		/*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesUser.delete_user',
				'parameters' => array('id' => 3, 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/
		
		/*A test to get new publications in course X from the db
		 * 
		 */
		
		/*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_new_publications_in_course',
				'parameters' => array('user_id' => 2, 'id' => 3, 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/
		
		/*A test to get new publications in course X, tool Y from the db
		 *
		 */

		/*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_new_publications_in_course_tool',
				'parameters' => array('user_id' => 2, 'id' => 3, 'tool' => 'announcement', 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/

        /*A test to get publications for user X from the db
		 *
		 */

		/*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_publications_for_user',
				'parameters' => array('id' => 1, 'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/

        /*A test to get publications for course X from the db
		 *
		 */

		$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesCourse.get_publications_for_course',
				'parameters' => array('id' => 1, 'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}
		
		/*A test to delete a course in the db
		 * 
		 */
		
		/*$course = array (
          'id' => '2',
          'layout' => '1',
          'visual_code' => 'Tjaffen',
          'category' => '1',
          'title' => 'Tjaffen',
          'titular' => '2',
          'course_language' => 'english',
          'department_url' => NULL,
          'department_name' => NULL,
          'visibility' => '3',
          'subscribe' => '1',
          'unsubscribe' => '0',
          'theme' => NULL,
          'tool_shortcut' => '1',
          'menu' => '1',
          'breadcrumb' => '1',
          'allow_feedback' => '1',
          'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'
        );

		$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.delete_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'			
			);*/
		
		
		
		/*A test to create a course in the db
		 * 
		 */
		
		  /*$course = array (
          'id' => '3',
          'layout' => '1',
          'visual_code' => 'Vissen',
          'category' => '1',
          'title' => 'Vissen',
          'titular' => '2',
          'course_language' => 'Japanese',
          'department_url' => NULL,
          'department_name' => NULL,
          'visibility' => '3',
          'subscribe' => '1',
          'unsubscribe' => '0',
          'theme' => NULL,
          'tool_shortcut' => '1',
          'menu' => '1',
          'breadcrumb' => '1',
          'allow_feedback' => '1',
          'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'
             );
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.create_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'			
			);*/
		
		/*A test to subscribe a user to a course in the db
		 * 
		 */
		
		  $course = array (
			  'course_code' => 'KIT',
			  'user_id' => 'Soliber',
			  'status' => '1',
			  'role' => 'NULL',
			  'course_group_id' => '0',
			  'tutor_id' => '1',
			  'sort' => '1',
			  'user_course_cat' => '0',
              'hash' => '550859312670dd7996153002d046737f08ba2c9f'
			);

		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.subscribe_user',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'			
			);
		
		/*A test to unsubscribe a user from a course in the db
		 * 
		 */
		
		 /*$course = array (
			  'course_code' => 'KIT',
			  'user_id' => 'Soliber',
			  'status' => '1',
			  'role' => 'NULL',
			  'course_group_id' => '0',
			  'tutor_id' => '1',
			  'sort' => '1',
			  'user_course_cat' => '0',
              'hash' => 'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'
			);
	   	  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.unsubscribe_user',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'			
			);*/

		/*A test to subscribe a group to a course in the db
		 * 
		 */
		
		  /*$coursegroup = array (
		  	  'id' => '1',
			  'course_code' => '2',
			  'name' => 'test',
			  'description' => 'test',
			  'max_number_of_members' => '999',
			  'self_reg_allowed' => '1',
			  'self_unreg_allowed' => '1',
              'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'
			);
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.subscribe_group',
				'parameters' => $coursegroup,
		  		'handler' => 'handle_webservice'			
			);*/
			
		/*A test to unsubscribe a group from a course in the db
		 * 
		 */
		
		   /*$coursegroup = array (
		  	  'id' => '1',
			  'course_code' => '2',
			  'name' => 'test',
			  'description' => 'test',
			  'max_number_of_members' => '999',
			  'self_reg_allowed' => '1',
			  'self_unreg_allowed' => '1',
              'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'
			);
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.unsubscribe_group',
				'parameters' => $coursegroup,
		  		'handler' => 'handle_webservice'			
			);*/
		
		/*A test to update a course in the db
		 * 
		 */
		
		  /*$course = array (
          'id' => '3',
          'layout' => '1',
          'visual_code' => 'Tjaffen',
          'category' => '1',
          'title' => 'Tjaffen',
          'titular' => '2',
          'course_language' => 'Japanese',
          'department_url' => NULL,
          'department_name' => NULL,
          'visibility' => '3',
          'subscribe' => '1',
          'unsubscribe' => '0',
          'theme' => NULL,
          'tool_shortcut' => '1',
          'menu' => '1',
          'breadcrumb' => '1',
          'allow_feedback' => '1',
          'hash' => '8856ffce09dad0fd33bfe3ae803cd97cc4540a78'
        );
		
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.update_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'			
			);*/
		
		
		
		/*A test subscribe a user to a group
		 * 
		 */
		
		  /*$group_rel_user = new GroupRelUser();
		  $group_rel_user->set_user_id(2);
		  $group_rel_user->set_group_id(1);
		  $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServiceSubscribeUserGroup.subscribe_user',
				'parameters' => $group_rel_user->get_default_properties(),
		  		'handler' => 'handle_webservice'			
			);*/
		
		
		
		/*A test subscribe a user to a group
		 * 
		 */
		
		/*$group_rel_user = new GroupRelUser();
		  $group_rel_user->set_user_id(2);
		  $group_rel_user->set_group_id(1);
          $wsdl = 'http://localhost/group/webservices/webservice_unsubscribe_user_from_group.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServiceUnsubscribeUserGroup.unsubscribe_user',
				'parameters' => $group_rel_user->to_array(),
		  		'handler' => 'handle_webservice'			
			);*/
			
			
		$this->webservice->call_webservice($wsdl, $functions);
	}	
	function handle_webservice($result)
	{
		//global $file;
		//fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");
		//echo ('<p>'.date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n".'</p>');
		//echo '<pre>'.var_export($result,true).'</pre';
		dump($result);
	}
}

?>