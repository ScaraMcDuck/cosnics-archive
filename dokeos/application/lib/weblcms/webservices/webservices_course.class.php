<?php
require_once(dirname(__FILE__) . '/../../../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../data_manager/database.class.php';
require_once dirname(__FILE__) . '/../course/course.class.php';
require_once dirname(__FILE__) . '/../course/course_user_relation.class.php';
require_once dirname(__FILE__) . '/../../../../common/webservices/input_user.class.php';
require_once dirname(__FILE__) . '/../../../../user/lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../data_manager/database.class.php';
require_once dirname(__FILE__) . '/../../../../repository/lib/learning_object.class.php';
require_once dirname(__FILE__) . '/../learning_object_publication.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager/weblcms.class.php';
require_once Path :: get_library_path() . 'validator/validator.class.php';

ini_set('max_execution_time', -1);
ini_set('memory_limit',-1);

$handler = new WebServicesCourse();
$handler->run();

class WebServicesCourse
{
	private $webservice;
    private $validator;
	
	function WebServicesCourse()
	{
		$this->webservice = Webservice :: factory($this);
        $this->validator = Validator :: get_validator('course');
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['get_course'] = array(
			'input' => new Course(),
			'output' => new Course()
		);
		
		$functions['delete_course'] = array(
			'input' => new Course()
		);

        $functions['delete_courses'] = array(
            'array_input' => true,
			'input' => array(new Course())
		);
		
		$functions['update_course'] = array(
			'input' => new Course()
		);

        $functions['update_courses'] = array(
			'array_input' => true,
			'input' => array(new Course())
		);
		
		$functions['create_course'] = array(
			'input' => new Course()
		);

        $functions['create_courses'] = array(
			'array_input' => true,
			'input' => array(new Course())
		);
		
		$functions['subscribe_user'] = array(
			'input' => new CourseUserRelation()
		);

        $functions['subscribe_users'] = array(
            'array_input' => true,
			'input' => array(new CourseUserRelation())
		);
		
		$functions['unsubscribe_user'] = array(
			'input' => new CourseUserRelation()
		);

        $functions['unsubscribe_users'] = array(
			'array_input' => true,
			'input' => array(new CourseUserRelation())
		);
		
		$functions['subscribe_group'] = array(
			'input' => new CourseGroup()
		);

        $functions['subscribe_groups'] = array(
            'array_input' => true,
			'input' => array(new CourseGroup())
		);
		
		$functions['unsubscribe_group'] = array(
			'input' => new CourseGroup()
		);

        $functions['unsubscribe_groups'] = array(
            'array_input' => true,
			'input' => array(new CourseGroup())
		);
		
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
	
	function get_course($input_course)
	{
        if($this->webservice->can_execute($input_course, 'get course'))
		{
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            if($this->validator->validate_retrieve($input_course[input])) //input validation
            {
                $course = $wdm->retrieve_course_by_visual_code($input_course[input][visual_code]);
                if(!empty($course))
                {
                    return $course->get_default_properties();
                }
                else
                {
                    return $this->webservice->raise_error(Translation :: get('Course').' '.$input_course[input][visual_code].Translation :: get('Not Found').'.');
                }
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
	
	function delete_course($input_course)
	{
        if($this->webservice->can_execute($input_course, 'delete course'))
		{
            if($this->validator->validate_delete($input_course[input])) //input validation
            {
                $c = new Course($input_course[input][id],$input_course[input]);
                return $this->webservice->raise_message($c->delete());
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

    function delete_courses($input_course)
	{
        if($this->webservice->can_execute($input_course, 'delete courses'))
		{
            foreach($input_course[input] as $course)
            {
                if($this->validator->validate_delete($course)) //input validation
                {
                    $course = new Course($course[id],$course);
                    $course->delete();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message());
                }
            }
            return $this->webservice->raise_message(Translation :: get('CoursesDeleted').'.');
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	
	function update_course($input_course)
	{
		if($this->webservice->can_execute($input_course, 'update course'))
		{
            if($this->validator->validate_update($input_course[input])) //input validation
            {
                $c = new Course($input_course[input][id],$input_course[input]);
                return $this->webservice->raise_message($c->update());
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

    function update_courses($input_course)
	{
		if($this->webservice->can_execute($input_course, 'update courses'))
		{
            foreach($input_course[input] as $course)
            {
                if($this->validator->validate_update($course)) //input validation
                {
                    $course = new Course($course[id],$course);
                    $course->update();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message());
                }
            }
            return $this->webservice->raise_message(Translation :: get('CoursesUpdated').'.');
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function create_course($input_course)
	{
		if($this->webservice->can_execute($input_course, 'create course'))
		{
            unset($input_course[input][id]);
            if($this->validator->validate_create($input_course[input])) //input validation
            {
                $c = new Course(0,$input_course[input]);
                return $this->webservice->raise_message($c->create());
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

    function create_courses($input_course)
	{
		if($this->webservice->can_execute($input_course, 'create courses'))
		{
            foreach($input_course[input] as $course)
            {
                unset($course[id]);
                if($this->validator->validate_create($course)) //input validation
                {
                    $course = new Course(0,$course);
                    $course->create();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message());
                }
            }
            return $this->webservice->raise_message(Translation :: get('CoursesCreated').'.');
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function subscribe_user(&$input_course) //course user relation object
	{
        if($this->webservice->can_execute($input_course, 'subscribe user'))
		{            
            if($this->validator->validate_subscribe_user($input_course[input])) //input validation
            {                
                $cur = new CourseUserRelation($input_course[input][course_code],$input_course[input][user_id]);
                unset($input_course[input][course_code]);
                unset($input_course[input][user_id]);
                $cur->set_default_properties($input_course[input]);
                return $this->webservice->raise_message($cur->create());
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

    function subscribe_users(&$input_course) //course user relation object
	{
        if($this->webservice->can_execute($input_course, 'subscribe users'))
		{
            foreach($input_course[input] as $c)
            {
                if($this->validator->validate_subscribe_user($c)) //input validation
                {
                    $cur = new CourseUserRelation($c[course_code],$c[user_id]);
                    unset($c[course_code]);
                    unset($c[user_id]);
                    $cur->set_default_properties($c);
                    $cur->create();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message());
                }
            }
            return $this->webservice->raise_message(Translation :: get('UsersSubscribed').'.');

        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }
	
	function unsubscribe_user(&$input_course)
	{
		if($this->webservice->can_execute($input_course, 'unsubscribe user'))
		{
           if($this->validator->validate_unsubscribe_user($input_course[input])) //input validation
            {
                $cur = new CourseUserRelation($input_course[input][course_code],$input_course[input][user_id]);
                return $this->webservice->raise_message($cur->delete());
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

    function unsubscribe_users(&$input_course)
	{
		if($this->webservice->can_execute($input_course, 'unsubscribe users'))
		{
            foreach($input_course[input] as $c)
            {
                if($this->validator->validate_unsubscribe_user($c)) //input validation
                {
                    $cur = new CourseUserRelation($c[course_code],$c[user_id]);
                    $cur->delete();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message());
                }
            }
           return $this->webservice->raise_message(Translation :: get('UsersUnsubscribed'));
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function subscribe_group(&$input_group)
	{
		if($this->webservice->can_execute($input_group, 'subscribe group'))
		{
            if($this->validator->validate_subscribe_group($input_group[input])) //input validation
            {
                $cg = new CourseGroup($input_group[input][id],$input_group[input][course_code]);
                unset($input_group[input]['id']);
                unset($input_group[input]['course_code']);
                $cg->set_default_properties($input_group[input]);
                return $this->webservice->raise_message($cg->create());
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

    function subscribe_groups(&$input_group)
	{
		if($this->webservice->can_execute($input_group, 'subscribe groups'))
		{
            foreach($input_group[input] as $course_group)
            {
                if($this->validator->validate_subscribe_group($course_group)) //input validation
                {
                    $cg = new CourseGroup($course_group[id],$course_group[course_code]);
                    unset($course_group['id']);
                    unset($course_group['course_code']);
                    $cg->set_default_properties($course_group);
                    $cg->create();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message());
                }
            }
            return $this->webservice->raise_message(Translation :: get('GroupsSubscribed'));
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function unsubscribe_group(&$input_group)
	{
		if($this->webservice->can_execute($input_group, 'unsubscribe group'))
		{
            if($this->validator->validate_unsubscribe_group($input_group[input])) //input validation
            {
                $cg = new CourseGroup($input_group[input][id],$input_group[input][course_code]);
                unset($input_group[input]['id']);
                unset($input_group[input]['course_code']);
                $cg->set_default_properties($input_group[input]);
                return $this->webservice->raise_message($cg->delete());
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

    function unsubscribe_groups(&$input_group)
	{
		if($this->webservice->can_execute($input_group, 'unsubscribe groups'))
		{
            foreach($input_group[input] as $course_group)
            {
                if($this->validator->validate_unsubscribe_group($course_group)) //input validation
                {
                    $cg = new CourseGroup($course_group[id],$course_group[course_code]);
                    unset($course_group['id']);
                    unset($course_group['course_code']);
                    $cg->set_default_properties($course_group);
                    $cg->delete();
                }
                else
                {
                    return $this->webservice->raise_error($this->validator->get_error_message());
                }
            }
            return $this->webservice->raise_message(Translation :: get('GroupsUnsubscribed'));
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
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