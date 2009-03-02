<?php
require_once(dirname(__FILE__) . '/../../../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_course.class.php';
require_once dirname(__FILE__) . '/../data_manager/database.class.php';
require_once dirname(__FILE__) . '/../../../../common/webservices/action_success.class.php';

$handler = new WebServicesCourse();
$handler->run();

class WebServicesCourse
{
	private $webservice;
	private $functions;
	
	function WebServicesCourse()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['get_course'] = array(
			'input' => new InputCourse(),
			'output' => new Course()
		);
		
		$functions['delete_course'] = array(
			'input' => new Course(),
			'output' => new ActionSuccess()
		);
		
		$functions['update_course'] = array(
			'input' => new Course(),
			'output' => new ActionSuccess()
		);
		
		$functions['create_course'] = array(
			'input' => new Course(),
			'output' => new ActionSuccess()
		);
		
		$functions['subscribe_user'] = array(
			'input' => new CourseUserRelation(),
			'output' => new ActionSuccess()
		);
		
		$functions['unsubscribe_user'] = array(
			'input' => new CourseUserRelation(),
			'output' => new ActionSuccess()
		);
		
		$functions['subscribe_group'] = array(
			'input' => new CourseGroup(),
			'output' => new ActionSuccess()
		);		
		
		$functions['unsubscribe_group'] = array(
			'input' => new CourseGroup(),
			'output' => new ActionSuccess()
		);
		
		$this->webservice->provide_webservice($functions);

	}
	
	function get_course($input_course)
	{
		$wdm = DatabaseWeblcmsDataManager :: get_instance();
		$course = $wdm->retrieve_course($input_course[id]);
		return $course->get_default_properties();
	}
	
	function delete_course($input_course)
	{
		$c = new Course($input_course[id],$input_course);
		$success = new ActionSuccess();
		$success->set_success($c->delete());
		return $success->get_default_properties();
	}
	
	
	function update_course($input_course)
	{
		$c = new Course($input_course[id],$input_course);
		$success = new ActionSuccess();
		$success->set_success($c->update());
		return $success->get_default_properties();
	}
	
	function create_course($input_course)
	{
		unset($input_course['id']);
		$c = new Course(0,$input_course);
		$success = new ActionSuccess();
		$success->set_success($c->create());
		return $success->get_default_properties();
	}
	
	function subscribe_user($input_course)
	{
		$cur = new CourseUserRelation($input_course[course_code],$input_course[user_id]);
		unset($input_course['user_id']);
		unset($input_course['course_code']);
		$cur->set_default_properties($input_course);
		$success = new ActionSuccess();
		$success->set_success($cur->create());
		return $success->get_default_properties();
	}
	
	function unsubscribe_user($input_course)
	{
		$cur = new CourseUserRelation($input_course[course_code],$input_course[user_id]);
		unset($input_course['user_id']);
		unset($input_course['course_code']);
		$cur->set_default_properties($input_course);
		$success = new ActionSuccess();
		$success->set_success($cur->delete());
		return $success->get_default_properties();
	}
	
	function subscribe_group($input_group)
	{
		$cg = new CourseGroup($input_group[id],$input_group[course_code]);
		unset($input_group['id']);
		unset($input_group['course_code']);
		$cg->set_default_properties($input_group);
		$success = new ActionSuccess();
		$success->set_success($cg->create());
		return $success->get_default_properties();
	}
	
	function unsubscribe_group($input_group)
	{
		$cg = new CourseGroup($input_group[id],$input_group[course_code]);
		unset($input_group['id']);
		unset($input_group['course_code']);
		$cg->set_default_properties($input_group);
		$success = new ActionSuccess();
		$success->set_success($cg->delete());
		return $success->get_default_properties();
	}

}