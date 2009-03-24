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
			'input' => new InputCourse(),
			'output' => new Course(),
            'require_hash' => true
		);
		
		$functions['delete_course'] = array(
			'input' => new Course(),
			'require_hash' => true
		);
		
		$functions['update_course'] = array(
			'input' => new Course(),
			'require_hash' => true
		);
		
		$functions['create_course'] = array(
			'input' => new Course(),
			'require_hash' => true
		);
		
		$functions['subscribe_user'] = array(
			'input' => new CourseUserRelation(),
			'require_hash' => true
		);
		
		$functions['unsubscribe_user'] = array(
			'input' => new CourseUserRelation(),
			'require_hash' => true
		);
		
		$functions['subscribe_group'] = array(
			'input' => new CourseGroup(),
			'require_hash' => true
		);		
		
		$functions['unsubscribe_group'] = array(
			'input' => new CourseGroup(),
			'require_hash' => true
		);
		
		$functions['get_user_courses'] = array(
			'input' => new InputUser(),
			'output' => array(new Course()),
			'array' => true,
            'require_hash' => true
		);
		
		$functions['get_course_users'] = array(
			'input' => new InputCourse(),
			'output' => array(new User()),
			'array' => true,
            'require_hash' => true
		);
		
		$functions['get_new_publications_in_course'] = array(
            'input' => new InputCourse(),
            'output' => array(new LearningObject()),
			'array' => true,
            'require_hash' => true
		);

        $functions['get_new_publications_in_course_tool'] = array(
            'input' => new InputCourse(),
            'output' => array(new LearningObject()),
			'array' => true,
            'require_hash' => true
		);

        $functions['get_publications_for_user'] = array(
            'input' => new InputUser(),
            'output' => array(new LearningObject()),
			'array' => true,
            'require_hash' => true
		);

        $functions['get_publications_for_course'] = array(
            'input' => new InputCourse(),
            'output' => array(new LearningObject()),
			'array' => true,
            'require_hash' => true
		);
		
		$this->webservice->provide_webservice($functions);

	}
	
	function get_course($input_course)
	{
        if($this->webservice->can_execute($input_course, 'get course'))
		{
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            if($this->validator->validate_retrieve($input_course)) //input validation
            {
                $course = $wdm->retrieve_course($input_course[id]);
                if(count($course->get_default_properties())>0)
                {
                    return $course->get_default_properties();
                }
                else
                {
                    return $this->webservice->raise_error('Course '.$input_course[id].' not found.');
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
            unset($input_course[hash]);
            $c = new Course($input_course[id],$input_course);
            return $this->webservice->raise_message($c->delete());
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
            unset($input_course[hash]);
            if($this->validator->validate_update($input_course)) //input validation
            {
                $c = new Course($input_course[id],$input_course);
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
            unset($input_course[hash]);
            unset($input_course[id]);
            if($this->validator->validate_create($input_course)) //input validation
            {
                $c = new Course(0,$input_course);
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
	
	function subscribe_user($input_course) //course user relation object
	{
        if($this->webservice->can_execute($input_course, 'subscribe user'))
		{
            unset($input_course[hash]);            
            if($this->validator->validate_subscribe_user($input_course)) //input validation
            {                               
                $cur = new CourseUserRelation($input_course[course_code],$input_course[user_id]);                
                return $this->webservice->raise_message($cur->create());
            }
            else
            {               
                return $this->webservice->raise_error('Could not subscribe user to course. Please check the data you\'ve provided.');
            }
            
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
    }
	
	function unsubscribe_user($input_course)
	{
		if($this->webservice->can_execute($input_course, 'unsubscribe user'))
		{
           unset($input_course[hash]);
           if($this->validator->validate_unsubscribe_user($input_course)) //input validation
            {
                $cur = new CourseUserRelation($input_course[course_code],$input_course[user_id]);
                return $this->webservice->raise_message($cur->delete());
            }
            else
            {
                return $this->webservice->raise_error('Could not unsubscribe from course. Please check the data you\'ve provided.');
            }
           
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function subscribe_group($input_group)
	{
		if($this->webservice->can_execute($input_group, 'subscribe group'))
		{
            unset($input_group[hash]);
            $cg = new CourseGroup($input_group[id],$input_group[course_code]);
            unset($input_group['id']);
            unset($input_group['course_code']);
            $cg->set_default_properties($input_group);
            return $this->webservice->raise_message($cg->create());
        }
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function unsubscribe_group($input_group)
	{
		if($this->webservice->can_execute($input_group, 'unsubscribe group'))
		{
            unset($input_group[hash]);
            $cg = new CourseGroup($input_group[id],$input_group[course_code]);
            unset($input_group['id']);
            unset($input_group['course_code']);
            $cg->set_default_properties($input_group);
            return $this->webservice->raise_message($cg->delete());
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
            $courses = $wdm->retrieve_user_courses(new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $input_user[id]));
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
            unset($input_course[hash]);
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
        else
        {
            return $this->webservice->raise_error($this->webservice->get_message());
        }
	}
	
	function get_new_publications_in_course($input_course)
	{
        if($this->webservice->can_execute($input_course, 'get new publications in course'))
		{
            unset($input_course[hash]);
            $udm = DatabaseUserDataManager :: get_instance();
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $user = $udm->retrieve_user($input_course[user_id]);
            $course = $wdm->retrieve_course($input_course[id]);
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
            unset($input_course[hash]);
            $udm = DatabaseUserDataManager :: get_instance();
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $user = $udm->retrieve_user($input_course[user_id]);
            $course = $wdm->retrieve_course($input_course[id]);
            $weblcms = new Weblcms($user,null);
            $weblcms->set_course($course);
            $weblcms->load_tools();
            $conditions[1] = new InequalityCondition(LearningObjectPublication :: PROPERTY_MODIFIED_DATE,InequalityCondition :: LESS_THAN_OR_EQUAL,mktime(0,0,0,date('m'),date('d')+1,date('Y')));
            if($weblcms->tool_has_new_publications($input_course[tool]))
            {
                $lastVisit = $weblcms->get_last_visit_date($input_course[tool]);
                $conditions[0] = new InequalityCondition(LearningObjectPublication :: PROPERTY_MODIFIED_DATE,InequalityCondition :: GREATER_THAN_OR_EQUAL,mktime(0,0,0,date('m',$lastVisit),date('d',$lastVisit),date('Y',$lastVisit)));
                $conditions[2] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,$input_course[tool]);
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
            unset($input_user[hash]);
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $pubs = $wdm->retrieve_learning_object_publications(null,null,$input_user[id]);
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
            unset($input_course[hash]);
            $wdm = DatabaseWeblcmsDataManager :: get_instance();
            $pubs = $wdm->retrieve_learning_object_publications($input_course[id]);
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
	
	/*function casttoclass($class,$object)
	{
		return unserialize(preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', serialize($object)));
	}*/

	

}