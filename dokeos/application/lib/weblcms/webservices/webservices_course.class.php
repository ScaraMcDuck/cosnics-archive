<?php
require_once(dirname(__FILE__) . '/../../../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_course.class.php';
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
		
		$functions['update_course'] = array(
			'input' => new Course()
		);
		
		$functions['create_course'] = array(
			'input' => new Course()
		);
		
		$functions['subscribe_user'] = array(
			'input' => new CourseUserRelation()
		);
		
		$functions['unsubscribe_user'] = array(
			'input' => new CourseUserRelation()
		);
		
		$functions['subscribe_group'] = array(
			'input' => new CourseGroup()
		);		
		
		$functions['unsubscribe_group'] = array(
			'input' => new CourseGroup()
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
		
		$functions['get_new_publications_in_course'] = array(
            'input' => new InputCourse(),
            'output' => array(new LearningObject()),
			'array' => true
		);

        $functions['get_new_publications_in_course_tool'] = array(
            'input' => new InputCourse(),
            'output' => array(new LearningObject()),
			'array' => true
		);

        $functions['get_publications_for_user'] = array(
            'input' => new InputUser(),
            'output' => array(new LearningObject()),
			'array' => true
		);

        $functions['get_publications_for_course'] = array(
            'input' => new InputCourse(),
            'output' => array(new LearningObject()),
			'array' => true
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
                    return $this->webservice->raise_error('Course '.$input_course[input][visual_code].' not found.');
                }
            }
            else
            {
                return $this->webservice->raise_error('Could not retrieve course. Please check the data you\'ve provided.');
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
                return $this->webservice->raise_error('Could not delete course. Please check the data you\'ve provided.');
            }
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
                return $this->webservice->raise_error('Could not update course. Please check the data you\'ve provided.');
            }
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
                return $this->webservice->raise_error('Could not create course. Please check the data you\'ve provided.');
            }
            
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
                return $this->webservice->raise_error('Could not subscribe user to course. Either there\'s something wrong with the data you\'ve provided, or subscriptions to this course are not allowed.');
            }
            
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
                return $this->webservice->raise_error('Could not unsubscribe from course. Either there\'s something wrong with the data you\'ve provided, or unsubscribing from this course is not allowed.');
            }
           
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
                 return $this->webservice->raise_error('Could not subscribe group to course. Either something is wrong with the data you\'ve provided, or subscriptions are not allowed for this course.');
            }
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
                 return $this->webservice->raise_error('Could not unsubscribe group to course. Either something is wrong with the data you\'ve provided, or unsubscribing is not allowed for this course.');
            }
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function get_user_courses($input_user)
	{
        if($this->webservice->can_execute($input_user, 'get user courses'))
		{
            $wdm = DatabaseWeblcmsDataManager :: get_instance();            
            $courses = $wdm->retrieve_user_courses(new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $input_user[input][id]));
            $courses = $courses->as_array();
            foreach($courses as &$course)
            {
                $course = $course->get_default_properties();
            }
            return $courses;
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function get_course_users($input_course)
	{
		if($this->webservice->can_execute($input_course, 'get course users'))
		{
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $udm = DatabaseUserDataManager :: get_instance();
            $course = new Course($input_user[input][id],$input_user[input]);
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