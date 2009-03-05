<?php
require_once(dirname(__FILE__) . '/../../../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_course.class.php';
require_once dirname(__FILE__) . '/../data_manager/database.class.php';
require_once dirname(__FILE__) . '/../course/course.class.php';
require_once dirname(__FILE__) . '/../course/course_user_relation.class.php';
require_once dirname(__FILE__) . '/../../../../common/webservices/action_success.class.php';
require_once dirname(__FILE__) . '/../../../../common/webservices/input_user.class.php';
require_once dirname(__FILE__) . '/../../../../user/lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../data_manager/database.class.php';
require_once dirname(__FILE__) . '/../../../../repository/lib/learning_object.class.php';
require_once dirname(__FILE__) . '/../learning_object_publication.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager/weblcms.class.php';


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
		
		$functions['get_user_courses'] = array(
			'input' => new InputUser(),
			'output' => array(new Course()),
			'array' => true
		);
		
		$functions['get_course_users'] = array(
			'input' => new InputCourse(),
			'output' => array(new User()),
			'array' => true
		);
		
		$functions['get_new_publications'] = array(
			'output' => array(new LearningObject()),
			'array' => true
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
	
	function get_user_courses($user_id)
	{
		$wdm = DatabaseWeblcmsDataManager :: get_instance();
		$courses = $wdm->retrieve_user_courses(new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $user_id[id]));
		$courses = $courses->as_array();
		foreach($courses as &$course)
		{
			$course = $course->get_default_properties();
		}
		return $courses;
	}
	
	function get_course_users($input_course)
	{
		$wdm = DatabaseWeblcmsDataManager :: get_instance();
		$udm = DatabaseUserDataManager :: get_instance();
		$course = new Course($input_course[id],$input_course);
		$users = $wdm->retrieve_course_users($course);
		$users = $users->as_array();
		foreach($users as &$user)
		{
			$user = $udm->retrieve_user($user->get_user());
			$user = $user->get_default_properties();
		}
		return $users;
	}
	
	function get_new_publications()
	{
		$udm = DatabaseUserDataManager :: get_instance();
		$wdm = DatabaseWeblcmsDataManager :: get_instance();
		$user = $udm->retrieve_user(2);
		$course = $wdm->retrieve_course(2);
		$weblcms = new Weblcms($user,null);
		$weblcms->set_course($course);
		$weblcms->load_tools();
		$conditions[1] = new InequalityCondition(LearningObjectPublication :: PROPERTY_MODIFIED_DATE,InequalityCondition :: LESS_THAN_OR_EQUAL,mktime(0,0,0,date('m'),date('d')+1,date('Y')));
		foreach($weblcms->get_registered_tools() as $tool)
		{
			if($weblcms->tool_has_new_publications($tool->name))
			{
				$lastVisit = $weblcms->get_last_visit_date($tool->name);
				$conditions[0] = new InequalityCondition(LearningObjectPublication :: PROPERTY_MODIFIED_DATE,InequalityCondition :: GREATER_THAN_OR_EQUAL,mktime(0,0,0,date('m',$lastVisit),date('d',$lastVisit),date('Y',$lastVisit)));
				$conditions[2] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,$tool->name);
				$condition = new AndCondition($conditions);
				$pubs = $wdm->retrieve_learning_object_publications(null,null,null,null,$condition);
				$pubs = $pubs->as_array();
			}
			
		}
		foreach($pubs as &$pub)
		{
			$pub = $pub->get_learning_object();
			$pub = $this->casttoclass('LearningObject',$pub);
			$pub->set_creation_date(date('Y-m-d', $pub->get_creation_date()));
			$pub->set_modification_date(date('Y-m-d', $pub->get_modification_date()));
			$pub = $pub->get_default_properties();
		}
		dump($pubs);
		
		/*$wdm = DatabaseWeblcmsDataManager :: get_instance();
		$conditions[0] = new InequalityCondition(LearningObjectPublication :: PROPERTY_MODIFIED_DATE,InequalityCondition :: GREATER_THAN_OR_EQUAL,mktime(0,0,0,date('m'),date('d'),date('Y')));
		$conditions[1] = new InequalityCondition(LearningObjectPublication :: PROPERTY_MODIFIED_DATE,InequalityCondition :: LESS_THAN_OR_EQUAL,mktime(0,0,0,date('m'),date('d')+1,date('Y')));
		$condition = new AndCondition($conditions);
		$pubs = $wdm->retrieve_learning_object_publications(null,null,null,null,$condition);
		$pubs = $pubs->as_array();
		foreach($pubs as &$pub)
		{
			$pub = $pub->get_learning_object();
			$pub = $this->casttoclass('LearningObject',$pub);
			$pub->set_creation_date(date('Y-m-d', $pub->get_creation_date()));
			$pub->set_modification_date(date('Y-m-d', $pub->get_modification_date()));
			$pub = $pub->get_default_properties();
		}
		return $pubs;*/
	}
	
	function casttoclass($class,$object)
	{
		return unserialize(preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', serialize($object)));
	}

	

}