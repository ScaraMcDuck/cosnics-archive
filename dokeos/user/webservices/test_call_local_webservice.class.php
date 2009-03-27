<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';

$handler = new TestCallLocalWebservice();

$handler->run();

class TestCallLocalWebservice
{
	private $webservice;
	
	function TestCallLocalWebservice()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		//TEST 1 :  Get User

        /*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.get_user',
				'parameters' => array('username' => 'Soliber','hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
		);*/

        //TEST 2 : Get User Courses

          /*$wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.get_user_courses',
				'parameters' => array('id' => '4','hash'=>'550859312670dd7996153002d046737f08ba2c9f'),
		  		'handler' => 'handle_webservice'
			);*/

		
		//TEST 3 : Get Group

		/*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();
		
		{
			$functions[] = array(
				'name' => 'WebServicesUser.get_all_users',
				'parameters' => array('hash'=>'8856ffce09dad0fd33bfe3ae803cd97cc4540a78'),
				'handler' => 'handle_webservice'
			);
		}*/
        
        //TEST 4 : Login Webservice
		/*$wsdl = 'http://www.dokeosplanet.org/demo_portal/user/webservices/login_webservice.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'LoginWebservice.login',
				'parameters' => array('username'=>'admin','password'=>'772d9ed50e3b34cbe3f9e36b77337c6b2f4e0cfa'), //password is actually hash 1
				'handler' => 'handle_webservice'
			);
		}*/
        
        //TEST 5 :  Create User

        /*$user = array (
		  'lastname' => 'Joske',
		  'firstname' => 'Den Os',
		  'username' => 'Jefke',
		  'password' => '4a0091108fb271e05f34da7cf77c975f',
		  'auth_source' => 'platform',
		  'email' => 'admin@localhost.localdomain',
		  'status' => '1',
		  'admin' => '1',
		  'phone' => NULL,
		  'official_code' => 'ADMIN',
		  'picture_uri' => NULL,
		  'creator_id' => 'admin',
		  'language' => 'english',
		  'disk_quota' => '209715200',
		  'database_quota' => '300',
		  'version_quota' => '20',
		  'theme' => NULL,
		  'activation_date' => '0',
		  'expiration_date' => '0',
		  'registration_date' => '1234774883',
		  'active' => '1',
          'hash' => '95abbf9f0c9d666c66aa30ce36da3ec0f57df48c' //hash 3 needed for credential
		);

        $wsdl = 'http://www.dokeosplanet.org/demo_portal/user/webservices/webservices_user.class.php?wsdl';
        $functions = array();
        $functions[] = array(
            'name' => 'WebServicesUser.create_user',
            'parameters' => $user,
            'handler' => 'handle_webservice'
        );*/

       //TEST 6 :: Update User

       /*$user = array (
          'user_id' => '17',
		  'lastname' => 'Joske',
		  'firstname' => 'Den Os',
		  'username' => 'tetjes',
		  'password' => '4a0091108fb271e05f34da7cf77c975f',
		  'auth_source' => 'platform',
		  'email' => 'admin@localhost.localdomain',
		  'status' => '1',
		  'admin' => '1',
		  'phone' => NULL,
		  'official_code' => 'ADMIN',
		  'picture_uri' => NULL,
		  'creator_id' => NULL,
		  'language' => 'english',
		  'disk_quota' => '209715200',
		  'database_quota' => '300',
		  'version_quota' => '20',
		  'theme' => NULL,
		  'activation_date' => '0',
		  'expiration_date' => '0',
		  'registration_date' => '1234774883',
		  'active' => '1',
          'hash' => '550859312670dd7996153002d046737f08ba2c9f' //hash 3 needed for credential
		);

        $wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.update_user',
				'parameters' => $user,
				'handler' => 'handle_webservice'
		);*/


        //TEST 7 : Delete User

        /*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.delete_user',
				'parameters' => array('user_id' => '32','username'=>'ka', 'hash'=>'550859312670dd7996153002d046737f08ba2c9f'),
				'handler' => 'handle_webservice'
		);*/

        //TEST 8 : Create Course

          /*$course = array (
            'course_language' => 'english',
            'title' => 'LocalTest',
            'description' => '',
            'category' 	=> 'Language skills', //needs the name, not the id
            'visibility' => '1',
            'show_score' => '1',
            'titular' => 'admin', //needs the username, not the id
            'visual_code' => '12456789',
            'department_name' => '',
            'department_url' => '',
            'disk_quota' => '200', //needs to > 1
            'target_course_code' => '',
            'layout' => '1',
            'subscribe' => '1',
            'unsubscribe' => '0',
            'theme' => '1',
            'tool_shortcut' => '1',
            'menu' 	=> '1',
            'breadcrumb' => '1',
            'allow_feedback' => '1',
            'hash' => '95abbf9f0c9d666c66aa30ce36da3ec0f57df48c' //hash 3 needed for credential
            );
            
          $wsdl = 'http://www.dokeosplanet.org/demo_portal/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.create_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/

        //TEST 9 : Update Course

          /*$course = array (
            'id' => '33',
            'course_language' => 'english',
            'title' => 'Sweetdreams',
            'description' => '',
            'category' 	=> 'Language skills', //needs the name, not the id
            'visibility' => '1',
            'show_score' => '1',
            'titular' => 'Soliber', //needs the username, not the id
            'visual_code' => '12345',
            'department_name' => '',
            'department_url' => '',
            'disk_quota' => '200', //needs to > 1
            'target_course_code' => '',
            'layout' => '1',
            'subscribe' => '1',
            'unsubscribe' => '0',
            'theme' => '1',
            'tool_shortcut' => '1',
            'menu' 	=> '1',
            'breadcrumb' => '1',
            'allow_feedback' => '1',
            'hash' => '550859312670dd7996153002d046737f08ba2c9f' //hash 3 needed for credential
            );

          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.update_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/

        //TEST 10 : Delete Course

        /*$course = array (
            'id' => '37',
            'hash' => '550859312670dd7996153002d046737f08ba2c9f' //hash 3 needed for credential
            );

          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.delete_course',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/

        //TEST 11 : Subscribe User

       /*$course = array (

            'user_id' => 'Soliber', //expect name
            'tutor_id' => '1',
            'status' => '1',
            'course_group_id' => '0',
            'course_code' => 'H1', //the name is course_code, because we expect a course_user_rel, but the the value is visual_code
            'hash' => '550859312670dd7996153002d046737f08ba2c9f' //hash 3 needed for credential
            );

          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.subscribe_user',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/

        //TEST 12 : Unsubscribe User

        /*$course = array (
            'user_id' => 'Soliber', //expect name
            'tutor_id' => '1',
            'status' => '1',
            'course_group_id' => '0',
            'course_code' => 'H1', //the name is course_code, because we expect a course_user_rel, but the the value is visual_code
            'hash' => '550859312670dd7996153002d046737f08ba2c9f' //hash 3 needed for credential
            );

          $wsdl = 'http://localhost/application/lib/weblcms/webservices/webservices_course.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesCourse.unsubscribe_user',
				'parameters' => $course,
		  		'handler' => 'handle_webservice'
			);*/

        //TEST 13 :  Get all users

        /*$wsdl = 'http://localhost/user/webservices/webservices_user.class.php?wsdl';
		$functions = array();

		$functions[] = array(
				'name' => 'WebServicesUser.get_all_users',
				'parameters' => array('hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
		);*/

        //TEST 14 : Get Group
		/*$wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		$functions = array();

		{
			$functions[] = array(
				'name' => 'WebServicesGroup.get_group',
				'parameters' => array('name' => 'SShinsengumi','hash'=>'c31ec0d4e5296ec2b12b11cf1f7ac9eb3014857f'),
				'handler' => 'handle_webservice'
			);
		}*/

		//TEST 15 : Delete Group
		  $group = array (
			    'name' => 'de coolste groep',
			    'description' => 'test',
			    'sort' => '1',
			    'parent' => '1',
                'hash' => '550859312670dd7996153002d046737f08ba2c9f'
			);

		  $wsdl = 'http://localhost/group/webservices/webservices_group.class.php?wsdl';
		  $functions = array();
		  $functions[] = array(
				'name' => 'WebServicesGroup.delete_group',
				'parameters' => $group,
		  		'handler' => 'handle_webservice'			
			);

	
		$this->webservice->call_webservice($wsdl, $functions);
	}
	
	function handle_webservice($result)
	{
		//global $file;
		//fwrite($file, date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n");
		//echo ('<p>'.date('[H:i]') . 'Called webservice :' . "\n" . var_export($result, true) . "\n".'</p>');
		//echo '<pre>'.var_export($result,true).'</pre>';
		dump($result);
	}
}

?>