<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';
//require_once dirname(__FILE__).'/../weblcms_manager/weblcms.class.php';

class ReportingWeblcms {

    function ReportingWeblcms() {
    }

    /**
     * Returns the course information
     * @param <type> $params
     * @return <type>
     */
    public static function getCourseInformation($params)
    {
        $wdm = WeblcmsDataManager::get_instance();
        $course = $wdm->retrieve_course($params[ReportingManager :: PARAM_COURSE_ID]);
        $arr[Translation :: get('Name')][] = $course->get_name();
        $arr[Translation :: get('Titular')][] = $course->get_titular_string();
        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the learning path information from a given course & user
     * @param <type> $params
     * @return <type>
     */
    public static function getCourseUserLearningpathInformation($params)
    {
        return self :: getCourseUserExcerciseInformation($params);
        
        $array = array();
        $wdm = WeblcmsDataManager::get_instance();
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $series = $wdm->count_learning_object_publications($course_id, null, $user_id);
        $lops = $wdm->retrieve_learning_object_publications($course_id, null, $user_id);
        
        $dataserie = 0;
        $serie = 1;
        $data[$dataserie]["Name"] = Translation :: get('LearningPaths');
        while($lop = $lops->next_result())
        {
            $lpo = $lop->get_learning_object();
            $data[$dataserie]["Serie".$serie] = $lpo->get_title();
            $serie++;
        }
        
        $dataserie++;
        $serie=1;
        $data[$dataserie]["Name"] = Translation :: get('Time');
        while($serie <= $series)
        {
            $data[$dataserie]["Serie".$serie] = 'unknown';
            $serie++;
        }

        $dataserie++;
        $serie=1;
        $data[$dataserie]["Name"] = Translation :: get('Score');
        while($serie <= $series)
        {
            $data[$dataserie]["Serie".$serie] = 'unknown';
            $serie++;
        }

        $dataserie++;
        $serie=1;
        $data[$dataserie]["Name"] = Translation :: get('Completed');
        while($serie <= $series)
        {
            $data[$dataserie]["Serie".$serie] = 'unknown';
            $serie++;
        }

        $dataserie++;
        $serie=1;
        $data[$dataserie]["Name"] = Translation :: get('LastConnection');
        while($serie <= $series)
        {
            $data[$dataserie]["Serie".$serie] = 'unknown';
            $serie++;
        }
        
        $serie=1;
        $datadescription["Position"] = "Name";
        while($serie <= $series)
        {
            $datadescription["Values"][] = "Serie".$serie;
            $serie++;
        }
        
        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }

    /**
     * Returns excercise information from a course / user information
     * @param <type> $params
     * @return <type>
     */
    public static function getCourseUserExcerciseInformation($params)
    {
        $arr[''][] = 'Not Available yet';
        
        return Reporting :: getSerieArray($arr);
        /*
        $array = array();
        $data[] = array("Name"=>"Learning paths","Serie1"=>"1_Chapter 1","Serie2"=>"2_Chapter 2","Serie3"=>"3_Chapter 3","Serie4"=>"4_Chapter 4","Serie5"=>"Al bar","Serie 6"=>"Il passato prossimo","Serie 7"=>"La pronuncia","Serie 8"=>"ripasso_1semestre");
        $data[] = array("Name"=>"Time","Serie1"=>"00:22:42","Serie2"=>"00:17:02","Serie3"=>"00:03:19","Serie4"=>"00:55:14","Serie5"=>"00:40:15","Serie6"=>"00:30:15","Serie7"=>"00:10:01","Serie8"=>"1:00:02");
        $data[] = array("Name"=>"Score","Serie1"=>"72.94%","Serie2"=>"22.45%","Serie3"=>"0%","Serie4"=>"0%","Serie5"=>"0%","Serie6"=>"0%","Serie7"=>"0%","Serie8"=>"0%");
        $data[] = array("Name"=>"Completed","Serie1"=>"100%","Serie2"=>"100%","Serie3"=>"100%","Serie4"=>"100%","Serie5"=>"100%","Serie6"=>"100%","Serie7"=>"100%","Serie8"=>"100%");
        $data[] = array("Name"=>"Last Connection","Serie1"=>"12 december 2008","Serie2"=>"06 januari 2009","Serie3"=>"12 december 2008","Serie4"=>"06 januari 2009","Serie5"=>"12 december 2008","Serie6"=>"06 januari 2009","Serie7"=>"12 december 2008","Serie8"=>"06 januari 2009");
        //details ?

        $datadescription["Position"] = "Name";
        $datadescription["Values"][] = "Serie1";
        $datadescription["Values"][] = "Serie2";
        $datadescription["Values"][] = "Serie3";
        $datadescription["Values"][] = "Serie4";
        $datadescription["Values"][] = "Serie5";
        $datadescription["Values"][] = "Serie6";
        $datadescription["Values"][] = "Serie7";
        $datadescription["Values"][] = "Serie8";
        
        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
        */
    }

    /**
     * returns the number of courses currently on the system
     * @param <type> $params
     * @return <type>
     */
    public static function getNoOfCourses($params)
    {
        $wdm = WeblcmsDataManager::get_instance();
        $count = $wdm->count_courses();

        $arr[Translation :: get('CourseCount')][] = $count;

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of tools with their access statistics for a specified course
     * @param <type> $params
     */
    public static function getLastAccessToTools($params)
    {
        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';
        
        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $course = $wdm->retrieve_course($course_id);
		$tools = $wdm->get_course_modules($course_id);

        //dump($tools);
        foreach($tools as $key => $value)
        {
            $name = $value->name;
            $link = $name;
            $counter = 0;
            $date = $wdm->get_last_visit_date_per_course($course_id,$name);
            if($date)
            {
                $date = date('d F Y (G:i:s)',$date);
            }else
            {
                $date = Translation :: get('NeverAccessed');
            }
            $conditions = array();
            $conditions[] = new LikeCondition(VisitTracker::PROPERTY_LOCATION,'&course='.$course_id);
            $conditions[] = new LikeCondition(VisitTracker::PROPERTY_LOCATION,'&tool='.$name);
            $condition = new AndCondition($conditions);
            $trackerdata = $tracker->retrieve_tracker_items($condition);
            foreach ($trackerdata as $key => $value) {
                $counter++;
            }
            $arr[$link][] = $date;
            $arr[$link][] = $counter;

            $description[0] = Translation :: get('Tool');
            $description[1] = Translation :: get('LastAccess');
            $description[2] = Translation :: get('Clicks');
            $description["Orientation"] = 'vertical';
        }
        return Reporting :: getSerieArray($arr,$description);
    }

    /**
     * Returns a list of the latest acces to a course
     * If a user is specified, returns access for this user to the course, else
     * it returns a list of all users
     * @param <type> $params
     */
    public static function getLatestAccess($params)
    {
        $course_id = $params[ReportingManager::PARAM_COURSE_ID];
        $user_id = $params[ReportingManager::PARAM_USER_ID];
        //$user_id = 2;
        //$course_id = 2;
        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';
        $tracker = new VisitTracker();
        $udm = UserDataManager::get_instance();

        if(isset($user_id))
        {
            $conditions[] = new LikeCondition(VisitTracker :: PROPERTY_LOCATION,'&course='.$course_id);
            $conditions[] = new EqualityCondition(VisitTracker::PROPERTY_USER_ID,$user_id);
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new LikeCondition(VisitTracker :: PROPERTY_LOCATION,'&course='.$course_id);
        }
        $user = $udm->retrieve_user($user_id);
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        foreach ($trackerdata as $key => $value) {
            $lastaccess = $value->get_enter_date();
            if(!isset($user_id))
                $user = $udm->retrieve_user($value->get_user_id());

            $arr[Translation :: get('User')][] = $user->get_fullname();
            $arr[Translation :: get('LastAccess')][] = $lastaccess;
            $time = strtotime($value->get_leave_date()) - strtotime($value->get_enter_date());
            $time = mktime(0,0,$time,0,0,0);
            $time = date('G:i:s',$time);
            $arr[Translation :: get('TotalTime')][] = $time;
        }
        arsort($arr[Translation::get('LastAccess')]);

        $i = 0;
        foreach($arr[Translation :: get('LastAccess')] as $key => $value)
        {
            if($i < sizeof($arr[Translation :: get('LastAccess')])/2)
            {
                $bla = $arr[Translation :: get('User')][$key];
                $arr[Translation :: get('User')][$key] = $arr[Translation :: get('User')][$i];
                $arr[Translation :: get('User')][$i] = $bla;
                $bla = $arr[Translation :: get('TotalTime')][$key];
                $arr[Translation :: get('TotalTime')][$key] = $arr[Translation :: get('TotalTime')][$i];
                $arr[Translation :: get('TotalTime')][$i] = $bla;
                //$arr[Translation :: get('TotalTime')][] = $lastaccess - $value->get_enter_date();
                $i++;
            }
        }
        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the number of courses listed by language
     * @param <type> $params
     * @return <type>
     */
    public static function getNoOfCoursesByLanguage($params)
    {
        $wdm = WeblcmsDataManager::get_instance();
        $arr = array();
        $courses = $wdm->retrieve_courses();
        while($course = $courses->next_result())
        {
            $lang = $course->get_language();
            if (array_key_exists($lang, $arr))
            {
                $arr[$lang][0]++;
            }else
            {
                $arr[$lang][0] = 1;
            }
        }

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of courses active within the last 24hrs, last week, last month
     * @param array $params
     */
    public static function getMostActiveInactiveLastVisit($params)
    {
        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';
        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $courses = $wdm->retrieve_courses();

        $arr[Translation :: get('Past24hr')][0] = 0;
        $arr[Translation :: get('PastWeek')][0] = 0;
        $arr[Translation :: get('PastMonth')][0] = 0;
        $arr[Translation :: get('PastYear')][0] = 0;
        $arr[Translation :: get('NeverAccessed')][0] = 0;
        
        while($course = $courses->next_result())
        {
            $lastaccess = 0;
            $condition = new LikeCondition(VisitTracker :: PROPERTY_LOCATION,'&course='.$course->get_id());
            $trackerdata = $tracker->retrieve_tracker_items($condition);
            foreach ($trackerdata as $key => $value) {
                $lastaccess = $value->get_leave_date();
            }

            if($lastaccess == 0)
            {
                $arr[Translation :: get('NeverAccessed')][0]++;
            }
            else if(strtotime($lastaccess) > time()-86400)
            {
                $arr[Translation :: get('Past24hr')][0]++;
            }
            else if(strtotime($lastaccess) > time()-604800)
            {
                $arr[Translation :: get('PastWeek')][0]++;
            }
            else if(strtotime($lastaccess) > time()-18144000)
            {
                $arr[Translation :: get('PastMonth')][0]++;
            }
            else if(strtotime($lastaccess) > time()-31536000)
            {
                $arr[Translation :: get('PastYear')][0]++;
            }
            else
            {
                $arr[Translation :: get('MoreThenOneYear')][0]++;
            }
        }
        return Reporting :: getSerieArray($arr);
    }

        /**
     * Returns a list of courses active within the last 24hrs, last week, last month
     * @param array $params
     */
    public static function getMostActiveInactiveLastPublication($params)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->retrieve_courses();

        $arr[Translation :: get('Past24hr')][0] = 0;
        $arr[Translation :: get('PastWeek')][0] = 0;
        $arr[Translation :: get('PastMonth')][0] = 0;
        $arr[Translation :: get('PastYear')][0] = 0;
        $arr[Translation :: get('NothingPublished')][0] = 0;

        while($course = $courses->next_result())
        {
            $lastpublication = 0;

            $condition = new EqualityCondition(LearningObjectPublication::PROPERTY_COURSE_ID,$course->get_id());
            $publications = $wdm->retrieve_learning_object_publications(null, null, null, null, $condition);
            while($publication = $publications->next_result())
            {
                $lastpublication = $publication->get_modified_date();
                $lastpublication = date('Y-m-d G:i:s',$lastpublication);
            }

            if($lastpublication == 0)
            {
                $arr[Translation :: get('NothingPublished')][0]++;
            }
            else if(strtotime($lastpublication) > time()-86400)
            {
                $arr[Translation :: get('Past24hr')][0]++;
            }
            else if(strtotime($lastpublication) > time()-604800)
            {
                $arr[Translation :: get('PastWeek')][0]++;
            }
            else if(strtotime($lastpublication) > time()-18144000)
            {
                $arr[Translation :: get('PastMonth')][0]++;
            }
            else if(strtotime($lastpublication) > time()-31536000)
            {
                $arr[Translation :: get('PastYear')][0]++;
            }
            else
            {
                $arr[Translation :: get('MoreThenOneYear')][0]++;
            }
        }
        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the most active / inactive courses
     * Link to course
     * @param array $params
     */
    public static function getMostActiveInactiveDetail($params)
    {
        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';
        $wdm = WeblcmsDataManager::get_instance();
        $tracker = new VisitTracker();
        $courses = $wdm->retrieve_courses();
        while($course = $courses->next_result())
        {
            $lastaccess = Translation :: get('NeverAccessed');
            $lastpublication = Translation :: get('NothingPublished');
            

            $condition = new LikeCondition(VisitTracker::PROPERTY_LOCATION,'&course='.$course->get_id());
            $trackerdata = $tracker->retrieve_tracker_items($condition);
            foreach($trackerdata as $key => $value)
            {
                $lastaccess = $value->get_leave_date();
            }

            $condition = new EqualityCondition(LearningObjectPublication::PROPERTY_COURSE_ID,$course->get_id());
            $publications = $wdm->retrieve_learning_object_publications(null, null, null, null, $condition);
            while($publication = $publications->next_result())
            {
                $lastpublication = $publication->get_modified_date();
                //$lastpublication = date_create($lastpublication);
                $lastpublication = date('Y-m-d G:i:s',$lastpublication);
            }

            $arr[Translation :: get('Course')][] = '<a href="run.php?go=courseviewer&course='.$course->get_id().'&application=weblcms&" />'.$course->get_name().'</a>';
            $arr[Translation :: get('LastVisit')][] = $lastaccess;
            $arr[Translation :: get('LastPublication')][] = $lastpublication;
        }

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of published object types and their amount
     * @param array $params
     */
    public static function getNoOfPublishedObjectsPerType($params)
    {
        $rdm = RepositoryDataManager::get_instance();
        $list = $rdm->get_registered_types();
        foreach ($list as $key => $value) {
            $arr[Translation :: get($value)][0] = 0;
        }
        
        $wdm = WeblcmsDataManager :: get_instance();
        $learning_objects = $wdm->retrieve_learning_object_publications();
        while($learning_object = $learning_objects->next_result())
        {
            //dump($learning_object);
            $arr[Translation :: get($learning_object->get_learning_object()->get_type())][0]++;
        }

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of object types and their amount
     * @param array $params
     */
    public static function getNoOfObjectsPerType($params)
    {
        $rdm = RepositoryDataManager::get_instance();
        $list = $rdm->get_registered_types();
        foreach ($list as $key => $value) {
            $arr[Translation :: get($value)][0] = 0;
        }

        $list = $rdm->retrieve_learning_objects();
        while($learning_object = $list->next_result())
        {
            $arr[Translation :: get($learning_object->get_type())][0]++;
        }

        return Reporting :: getSerieArray($arr);
    }

    public static function getAverageLearningpathScore($params)
    {
        $course_id = $params[ReportingManager::PARAM_COURSE_ID];
        $wdm = WeblcmsDataManager::get_instance();
        //$lops = $wdm->retrieve_learning_object_publications($course_id);
        $condition = new EqualityCondition(LearningObjectPublication::PROPERTY_TOOL,'learning_path');
        $lops = $wdm->retrieve_learning_object_publications($course_id, null, null, null, $condition);

        while($lop = $lops->next_result())
        {
            $lpo = $lop->get_learning_object();
            $arr[$lpo->get_title()][0] = 0;
        }

        $datadescription[0] = Translation :: get('LearningPath');
        $datadescription[1] = Translation :: get('Average');

        return Reporting :: getSerieArray($arr,$datadescription);
    }

    public static function getAverageExcerciseScore($params)
    {
        $course_id = $params[ReportingManager::PARAM_COURSE_ID];
        $wdm = WeblcmsDataManager::get_instance();

        $condition = new EqualityCondition(LearningObjectPublication::PROPERTY_TOOL,'assessment');
        $lops = $wdm->retrieve_learning_object_publications($course_id, null, null, null, $condition);

        while($lop = $lops->next_result())
        {
            //dump($lop);
        }

        $arr[''][] = Translation :: get('NotAvailable');

        return Reporting::getSerieArray($arr);
    }

    public static function getWikiPageMostActiveUser($params)
    {
        require_once Path :: get_repository_path().'lib/repository_data_manager.class.php';
        require_once Path :: get_user_path().'/lib/user_data_manager.class.php';
        $wiki_page_id = $params['wiki_page_id'];
        $dm = RepositoryDataManager :: get_instance();
        $wiki_page = $dm->retrieve_learning_object($wiki_page_id);
        $versions = $dm->retrieve_learning_object_versions($wiki_page);
        $users = array();
        foreach($versions as $version)
        {
            $users[$version->get_default_property('owner')]++;
        }
        arsort($users);
        $keys=array_keys($users);
        $user = UserDataManager ::get_instance()->retrieve_user($keys[0]);
        $arr[Translation :: get('MostActiveUser')][] = $user->get_username();
        $arr[Translation :: get('NumberOfContributions')][] = $users[$user->get_id()];

        return Reporting::getSerieArray($arr);
    }

    public static function getWikiPageUsersContributions($params)
    {
        require_once Path :: get_repository_path().'lib/repository_data_manager.class.php';
        require_once Path :: get_user_path().'/lib/user_data_manager.class.php';
        $wiki_page_id = $params['wiki_page_id'];
        $dm = RepositoryDataManager :: get_instance();
        $wiki_page = $dm->retrieve_learning_object($wiki_page_id);
        $versions = $dm->retrieve_learning_object_versions($wiki_page);
        $users = array();
        foreach($versions as $version)
        {
            $users[$version->get_default_property('owner')]++;
        }
        arsort($users);
        foreach($users as $user => $number)
        {
            $user = UserDataManager ::get_instance()->retrieve_user($user);
            $arr[Translation :: get('Username')][] = $user->get_username();
            $arr[Translation :: get('NumberOfContributions')][] = $number;
        }        
        $description['Orientation'] = 'horizontal';
        return Reporting::getSerieArray($arr,$description);
    }
}
?>