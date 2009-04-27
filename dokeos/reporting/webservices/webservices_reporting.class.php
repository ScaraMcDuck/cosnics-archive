<?php
require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/user.class.php';
require_once Path :: get_library_path() . 'validator/validator.class.php';

ini_set('max_execution_time', -1);
ini_set('memory_limit',-1);

$handler = new WebServicesUser();
$handler->run();

class WebServicesReporting
{
	private $webservice;
    private $validator;

    function WebServicesReporting()
	{
		$this->webservice = Webservice :: factory($this);
        $this->validator = Validator :: get_validator('course');
	}

    function run()
	{
		$functions = array();

        $functions['get_user_courses'] = array(
			'input' => new User(),
			'output' => array(new Course()),
			'array_output' => true
		);

		$functions['get_course_users'] = array(
			'input' => new Course(),
			'output' => array(new User()),
			'array_output' => true
		);

		$functions['get_new_publications_in_course'] = array(
            'input' => new Course(),
            'output' => array(new LearningObject()),
			'array_output' => true
		);

        $functions['get_new_publications_in_course_tool'] = array(
            'input' => new Course(),
            'output' => array(new LearningObject()),
			'array_output' => true
		);

        $functions['get_publications_for_user'] = array(
            'input' => new InputUser(),
            'output' => array(new LearningObject()),
			'array_output' => true
		);

        $functions['get_publications_for_course'] = array(
            'input' => new Course(),
            'output' => array(new LearningObject()),
			'array_output' => true
		);

        $this->webservice->provide_webservice($functions);

	}

    function get_user_courses(&$input_user)
	{
        if($this->webservice->can_execute($input_user, 'get user courses'))
		{
            if($this->validator->validate_get_user_courses($input_user[input]))
            {
                $wdm = DatabaseWeblcmsDataManager :: get_instance();
                $courses = $wdm->retrieve_user_courses(new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $input_user[input][User :: PROPERTY_USER_ID]));
                $courses = $courses->as_array();
                foreach($courses as &$course)
                {
                    $course = $course->get_default_properties();
                }
                return $courses;
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message());
            }
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}

	function get_course_users(&$input_course)
	{
		if($this->webservice->can_execute($input_course, 'get course users'))
		{
            if($this->validator->validate_get_course_users($input_course[input]))
            {
                $wdm = DatabaseWeblcmsDataManager :: get_instance();
                $udm = DatabaseUserDataManager :: get_instance();
                $course = new Course($input_course[input][Course :: PROPERTY_ID],$input_course[input]);
                $users = $wdm->retrieve_course_users($course);
                $users = $users->as_array();
                foreach($users as &$user)
                {
                    $user = $udm->retrieve_user($user->get_user());
                    $user = $user->get_default_properties();
                }
                return $users;
            }
            else
            {
                return $this->webservice->raise_error($this->validator->get_error_message());
            }
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}

	function get_new_publications_in_course($input_course)
	{
        if($this->webservice->can_execute($input_course, 'get new publications in course'))
		{
            $udm = DatabaseUserDataManager :: get_instance();
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $user = $udm->retrieve_user($input_course[input][user_id]);
            $course = $wdm->retrieve_course($input_course[input][id]);
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
                $pub = $pub->get_default_properties();
            }
            return $pubs;
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}

    function get_new_publications_in_course_tool($input_course)
	{
        if($this->webservice->can_execute($input_course, 'get new publications in course tool'))
		{
            $udm = DatabaseUserDataManager :: get_instance();
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $user = $udm->retrieve_user($input_course[input][user_id]);
            $course = $wdm->retrieve_course($input_course[input][id]);
            $weblcms = new Weblcms($user,null);
            $weblcms->set_course($course);
            $weblcms->load_tools();
            $conditions[1] = new InequalityCondition(LearningObjectPublication :: PROPERTY_MODIFIED_DATE,InequalityCondition :: LESS_THAN_OR_EQUAL,mktime(0,0,0,date('m'),date('d')+1,date('Y')));
            if($weblcms->tool_has_new_publications($input_course[input][tool]))
            {
                $lastVisit = $weblcms->get_last_visit_date($input_course[input][tool]);
                $conditions[0] = new InequalityCondition(LearningObjectPublication :: PROPERTY_MODIFIED_DATE,InequalityCondition :: GREATER_THAN_OR_EQUAL,mktime(0,0,0,date('m',$lastVisit),date('d',$lastVisit),date('Y',$lastVisit)));
                $conditions[2] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,$input_course[input][tool]);
                $condition = new AndCondition($conditions);
                $pubs = $wdm->retrieve_learning_object_publications(null,null,null,null,$condition);
                $pubs = $pubs->as_array();
            }
            foreach($pubs as &$pub)
            {
                $pub = $pub->get_learning_object();
                $pub = $pub->get_default_properties();
            }
            return $pubs;
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}

    function get_publications_for_user($input_user)
	{
        if($this->webservice->can_execute($input_user, 'get publications for user'))
		{
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $pubs = $wdm->retrieve_learning_object_publications(null,null,$input_user[input][id]);
            $pubs = $pubs->as_array();
            foreach($pubs as &$pub)
            {
                $pub = $pub->get_learning_object();
                $pub = $pub->get_default_properties();
            }
            return $pubs;
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}

    function get_publications_for_course($input_course)
	{
        if($this->webservice->can_execute($input_course, 'get publications for course'))
		{
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $pubs = $wdm->retrieve_learning_object_publications($input_course[input][id]);
            $pubs = $pubs->as_array();
            foreach($pubs as &$pub)
            {
                $pub = $pub->get_learning_object();
                $pub = $pub->get_default_properties();
            }
            return $pubs;
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}

}
?>