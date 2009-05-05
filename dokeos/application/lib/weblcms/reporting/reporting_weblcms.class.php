<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';

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
    }

    /**
     * Returns excercise information from a course / user information
     * @param <type> $params
     * @return <type>
     */
    public static function getCourseUserExcerciseInformation($params)
    {
        return Reporting :: getSerieArray($arr);
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
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $tools = $wdm->get_course_modules($course_id);

        foreach($tools as $key => $value)
        {
            $name = $value->name;
            //$link = '<img src="'.Theme :: get_image_path('weblcms').'tool_'.$name.'.png" style="vertical-align: middle;" />';// <a href="run.php?go=courseviewer&course='.$course_id.'&tool='.$name.'&application=weblcms">'.Translation :: get(DokeosUtilities::underscores_to_camelcase($name)).'</a>';
            $link = ' <a href="run.php?go=courseviewer&course='.$course_id.'&tool='.$name.'&application=weblcms">'.Translation :: get(DokeosUtilities::underscores_to_camelcase($name)).'</a>';
            $date = $wdm->get_last_visit_date_per_course($course_id,$name);
            if($date)
            {
                $date = date('d F Y (G:i:s)',$date);
            }else
            {
                $date = Translation :: get('NeverAccessed');
            }
            $conditions = array();
            $conditions2 = array();
            $conditions3 = array();
            $conditions[] = new PatternMatchCondition(VisitTracker::PROPERTY_LOCATION,'*course='.$course_id.'*tool='.$name.'*');
            if(isset($user_id))
                $conditions[] = new EqualityCondition(VisitTracker::PROPERTY_USER_ID,$user_id);
            $conditions2[] = new AndCondition($conditions);

            if($name == 'reporting')
            {
                $conditions3[] = new PatternMatchCondition(VisitTracker::PROPERTY_LOCATION,'*course_id???='.$course_id.'*');
                if(isset($user_id))
                    $conditions3[] = new EqualityCondition(VisitTracker::PROPERTY_USER_ID,$user_id);
                $conditions2[] = new AndCondition($conditions3);
            }
            $condition = new OrCondition($conditions2);

            $trackerdata = $tracker->retrieve_tracker_items($condition);

            $arr[$link][] = $date;
            $arr[$link][] = count($trackerdata);
            $params['tool'] = $name;
            $url = $params['url'].'&'.ReportingManager :: PARAM_TEMPLATE_NAME .'=ToolPublicationsDetailReportingTemplate&';
            $parameters[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS] = $params;
            $url .= http_build_query($parameters);
            //$url = ReportingManager::get_reporting_template_registration_url_content($_SESSION[ReportingManager::PARAM_REPORTING_PARENT],'ToolPublicationsDetailReportingTemplate',$params);
            //$url = ReportingManager :: get_reporting_template_registration_url_content($params[ReportingManager::PARAM_REPORTING_PARENT],'ToolPublicationsDetailReportingTemplate',$params);
            $arr[$link][] = '<a href="'.$url.'">'.Translation :: get('ViewPublications').'</a>';
        }

        $description[0] = Translation :: get('Tool');
        $description[1] = Translation :: get('LastAccess');
        $description[2] = Translation :: get('Clicks');
        $description[3] = Translation :: get('Publications');
        $description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_VERTICAL;
        return Reporting :: getSerieArray($arr,$description);
    }

    public static function getLastAccessToToolsPlatform($params)
    {
        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';

        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();

        $tools = $wdm->get_all_course_modules();

        foreach($tools as $key => $value)
        {
            $name = $value->name;
            $link = '<img src="'.Theme :: get_image_path('weblcms').'tool_'.$name.'.png" style="vertical-align: middle;" /> '.Translation :: get(DokeosUtilities::underscores_to_camelcase($name));
            $condition = new PatternMatchCondition(VisitTracker::PROPERTY_LOCATION,'*tool='.$name.'*');

            $trackerdata = $tracker->retrieve_tracker_items($condition);

            $arr[$link][] = count($trackerdata);
            $params['tool'] = $name;
            $url = $params['url'].'&'.ReportingManager :: PARAM_TEMPLATE_NAME .'=ToolPublicationsDetailReportingTemplate&';
            $parameters[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS] = $params;
            $url .= http_build_query($parameters);
            //$url = ReportingManager :: get_reporting_template_registration_url('ToolPublicationsDetailReportingTemplate',$params);
            $arr[$link][] = '<a href="'.$url.'">'.Translation :: get('ViewPublications').'</a>';
        }

        $description[0] = Translation :: get('Tool');
        $description[1] = Translation :: get('Clicks');
        $description[2] = Translation :: get('Publications');
        $description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_VERTICAL;
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

        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';
        $tracker = new VisitTracker();
        $udm = UserDataManager::get_instance();

        if(isset($user_id))
        {
            $conditions[] = new PatternMatchCondition(VisitTracker::PROPERTY_LOCATION,'*course='.$course_id.'*');
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

        Reporting :: sort_array($arr,Translation::get('LastAccess'));
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
        $description[0] = Translation :: get('Time');
        $description[1] = Translation :: get('TimesAccessed');
        return Reporting :: getSerieArray($arr,$description);
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
            $arr[$value][0] = 0;
        }

        $wdm = WeblcmsDataManager :: get_instance();
        $learning_objects = $wdm->retrieve_learning_object_publications();
        while($learning_object = $learning_objects->next_result())
        {
            //dump($learning_object);
            $arr[$learning_object->get_learning_object()->get_type()][0]++;
        }

        foreach ($arr as $key => $value)
        {
            $arr[Translation :: get(DokeosUtilities::underscores_to_camelcase($key))] = $arr[$key];
            unset($arr[$key]);
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
            $arr[$value][0] = 0;
        }

        $list = $rdm->retrieve_learning_objects();
        while($learning_object = $list->next_result())
        {
            $arr[$learning_object->get_type()][0]++;
        }

        foreach ($arr as $key => $value)
        {
            $arr[Translation :: get(DokeosUtilities::underscores_to_camelcase($key))] = $arr[$key];
            unset($arr[$key]);
        }

        $description[0] = Translation :: get('Object');
        return Reporting :: getSerieArray($arr,$description);
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
        return Reporting::getSerieArray($arr);
    }

    public static function getWikiPageMostActiveUser($params)
    {
        require_once Path :: get_repository_path().'lib/repository_data_manager.class.php';
        require_once Path :: get_user_path().'/lib/user_data_manager.class.php';
        $dm = RepositoryDataManager :: get_instance();
        $cloi = $dm->retrieve_complex_learning_object_item($params['cid']);
        $wiki_page = $dm->retrieve_learning_object($cloi->get_ref());
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
        $dm = RepositoryDataManager :: get_instance();
        $cloi = $dm->retrieve_complex_learning_object_item($params['cid']);
        $wiki_page = $dm->retrieve_learning_object($cloi->get_ref());
        $versions = $dm->retrieve_learning_object_versions($wiki_page);
        $users = array();
        foreach($versions as $version)
        {
            $users[$version->get_default_property('owner')]++;
        }
        arsort($users);
        foreach($users as $user => $number)
        {
            if($count<5)
            {
                $user = UserDataManager ::get_instance()->retrieve_user($user);
                $arr[Translation :: get('Username')][] = $user->get_username();
                $arr[Translation :: get('NumberOfContributions')][] = $number;
                $count++;
            }
        }
        $description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;
        return Reporting::getSerieArray($arr,$description);
    }

    public static function getWikiMostVisitedPage($params)
    {
        require_once Path :: get_tracking_path().'lib/tracking_data_manager.class.php';
        require_once Path :: get_repository_path().'lib/repository_data_manager.class.php';
        $tdm = TrackingDataManager :: get_instance();
        $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*tool_action=view_item*&pid='.$params['pid'].'*');
        $items = $tdm->retrieve_tracker_items('visit', 'VisitTracker', $condition);
        if(empty($items))
        return Reporting::getSerieArray($arr);
        foreach($items as $item)
        {
            $var[] = explode('&',$item->get_location());
        }
        foreach($var as $piece)
        {
            foreach($piece as &$entry)
            {
                $entry = (explode('=',$entry));
                if(strcmp($entry[0],'cid')===0)
                $cids[] = $entry[1];
            }
        }
        foreach($cids as &$cid)
        {
            $visits[$cid] = $visits[$cid]+1;
            $cloi = RepositoryDataManager ::get_instance()->retrieve_complex_learning_object_item($cid);
            $cloi_refs[$cid] = $cloi->get_ref();
        }
        arsort($visits);
        $keys=array_keys($visits);
        $page = RepositoryDataManager :: get_instance()->retrieve_learning_object($cloi_refs[$keys[0]]);
        $link = '<a href='. 'http://' . $_SERVER['HTTP_HOST'] ."/run.php?go=courseviewer&course={$params['course_id']}&tool=wiki&application=weblcms&tool_action=view_item&cid={$keys[0]}&pid={$params['pid']}" . '>' . htmlspecialchars($page->get_title()) . '</a>';
        $arr[Translation :: get('MostVisitedPage')][] = $link;
        $arr[Translation :: get('NumberOfVisits')][] = $visits[$keys[0]];

        return Reporting::getSerieArray($arr);
    }

    public static function getWikiMostEditedPage($params)
    {
        require_once Path :: get_repository_path().'lib/repository_data_manager.class.php';
        require_once Path :: get_application_path().'lib/weblcms/data_manager/database.class.php';
        $wiki = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($params['pid'])->get_learning_object();
        
        $clois = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_items(new EqualityCondition(ComplexlearningObjectItem :: PROPERTY_PARENT, $wiki->get_id()))->as_array();

        if(empty($clois))
        return Reporting::getSerieArray($arr);

        foreach($clois as $cloi)
        {
            $pages[$cloi->get_id()] = RepositoryDataManager :: get_instance()->retrieve_learning_object($cloi->get_ref());
        }

        foreach($pages as $cid => $page)
        {
            $edits[$page->get_title()] = RepositoryDataManager :: get_instance()->count_learning_object_versions($page);
            $page_ids[$page->get_title()] = $cid;
        }
        arsort($edits);
        $keys=array_keys($edits);
        $link = '<a href='.'http://' . $_SERVER['HTTP_HOST']."/run.php?go=courseviewer&course={$params['course_id']}&tool=wiki&application=weblcms&tool_action=view_item&cid={$page_ids[$keys[0]]}&pid={$wiki->get_id()}". '>' . htmlspecialchars($keys[0]) . '</a>';
        $arr[Translation :: get('MostEditedPage')][] = $link;
        $arr[Translation :: get('NumberOfEdits')][] = $edits[$keys[0]];
        return Reporting::getSerieArray($arr);
    }

    public static function getToolPublicationsDetail($params)
    {
        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';

        $course_id = $params[ReportingManager::PARAM_COURSE_ID];
        $user_id = $params[ReportingManager::PARAM_USER_ID];
        $tool = $params['tool'];

        $tracker = new VisitTracker();
        $wdm = WeblcmsDataManager::get_instance();
        $condition = new EqualityCondition(Weblcms::PARAM_TOOL,$tool);
        $lops = $wdm->retrieve_learning_object_publications($course_id, null, $user_id, null, $condition);

        while($lop = $lops->next_result())
        {
            $condition = new PatternMatchCondition(VisitTracker::PROPERTY_LOCATION,'*course='.$course_id.'*pid='.$lop->get_id().'*');
            $trackerdata = $tracker->retrieve_tracker_items($condition);

            foreach($trackerdata as $key => $value)
            {
                if($value->get_leave_date() > $lastaccess)
                $lastaccess = $value->get_leave_date();
            }
            $url = 'run.php?go=courseviewer&course='.$course_id.'&tool='.$tool.'&application=weblcms&tool_action=view&pid='.$lop->get_id();
            $arr[Translation :: get('Title')][] = '<a href="'.$url.'">'.$lop->get_learning_object()->get_title().'</a>';


            $des = $lop->get_learning_object()->get_description();
            $arr[Translation :: get('Description')][] = DokeosUtilities::truncate_string($des, 50);
            $arr[Translation :: get('LastAccess')][] = $lastaccess;
            $arr[Translation :: get('TotalTimesAccessed')][] = count($trackerdata);
            $params['pid'] = $lop->get_id();
            $url = $params['url'].'&'.ReportingManager :: PARAM_TEMPLATE_NAME .'=PublicationDetailReportingTemplate&';
            $parameters[ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS] = $params;
            $url .= http_build_query($parameters);
            //$url = ReportingManager :: get_reporting_template_registration_url_content($_SESSION[ReportingManager::PARAM_REPORTING_PARENT],'PublicationDetailReportingTemplate',$params);
            $arr[Translation :: get('PublicationDetails')][] = '<a href="'.$url.'">'.Translation :: get('AccessDetails').'</a>';
        }

        $description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;

        return Reporting :: getSerieArray($arr,$description);
    }

    public static function getPublicationDetail($params)
    {
        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';


        $course_id = $params[ReportingManager::PARAM_COURSE_ID];
        $user_id = $params[ReportingManager::PARAM_USER_ID];
        $tool = $params['tool'];
        $pid = $params['pid'];

        $tracker = new VisitTracker();
        $wdm = WeblcmsDataManager::get_instance();
        $condition = new EqualityCondition(Weblcms::PARAM_TOOL,$tool);
        $lop = $wdm->retrieve_learning_object_publication($pid);
        //$lops = $wdm->retrieve_learning_object_publications($course_id, null, $user_id, null, $condition);

        $condition = new PatternMatchCondition(VisitTracker::PROPERTY_LOCATION,'*pid='.$pid.'*');
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        foreach($trackerdata as $key => $value)
        {
            if($value->get_leave_date() > $lastaccess)
            $lastaccess = $value->get_leave_date();
        }
        //      run.php?go=courseviewer&course=1&tool=announcement&application=weblcms&pid=1&tool_action=view
        $url = 'run.php?go=courseviewer&course='.$course_id.'&tool='.$tool.'&application=weblcms&pid='.$lop->get_id().'&tool_action=view';
        $arr[Translation :: get('Title')][] = '<a href="'.$url.'">'.$lop->get_learning_object()->get_title().'</a>';

        $des = $lop->get_learning_object()->get_description();
        $arr[Translation :: get('Description')][] = DokeosUtilities::truncate_string($des, 50);
        $arr[Translation :: get('LastAccess')][] = $lastaccess;
        $arr[Translation :: get('TotalTimesAccessed')][] = count($trackerdata);

        //$description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;
      
        return Reporting :: getSerieArray($arr,$description);
    }

    public static function getPublicationAccess($params)
    {
        $course_id = $params[ReportingManager::PARAM_COURSE_ID];
        $user_id = $params[ReportingManager::PARAM_USER_ID];
        $pid = $params['pid'];
        $tool = $params['tool'];

        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';
        $tracker = new VisitTracker();
        $udm = UserDataManager::get_instance();

        if(isset($user_id))
        {
            $conditions[] = new PatternMatchCondition(VisitTracker::PROPERTY_LOCATION,'*pid='.$pid.'*');
            $conditions[] = new EqualityCondition(VisitTracker::PROPERTY_USER_ID,$user_id);
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new LikeCondition(VisitTracker :: PROPERTY_LOCATION,'&pid='.$pid);
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

        Reporting :: sort_array($arr,Translation :: get('LastAccess'));

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of all users which have accessed this publication
     * + last access date & how many times
     * @param <type> $params
     */
    public static function getPublicationUserAccess($params)
    {
        require_once Path :: get_user_path().'trackers/visit_tracker.class.php';

        $tracker = new VisitTracker();
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $tool = $params['tool'];
        $pid = $params['pid'];

        $udm = UserDataManager::get_instance();

        $condition = new PatternMatchCondition(VisitTracker::PROPERTY_LOCATION,'*pid='.$pid.'*');
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        foreach ($trackerdata as $key => $value) {
            if($search[$value->get_user_id()]['lastaccess'] < $value->get_enter_date())
                $search[$value->get_user_id()]['lastaccess'] = $value->get_enter_date();
            $search[$value->get_user_id()]['clicks']++;
            $search[$value->get_user_id()]['totaltime'] += strtotime($value->get_leave_date())-strtotime($value->get_enter_date());
        }

        foreach($search as $key => $value)
        {
            $arr[Translation :: get('User')][] = $udm->retrieve_user($key)->get_fullname();
            $arr[Translation :: get('LastAccess')][] = $search[$key]['lastaccess'];
            $time = mktime(0,0,$search[$key]['totaltime'],0,0,0);
            $time = date('G:i:s',$time);
            $arr[Translation :: get('TotalTime')][] = $time;
            $arr[Translation :: get('Clicks')][] = $search[$key]['clicks'];
        }

        Reporting :: sort_array($arr,Translation :: get('LastAccess'));


        $description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($arr,$description);
    }

    public static function getCoursesPerCategory($params)
    {
        $wdm = WeblcmsDataManager::get_instance();

        $categories = $wdm->retrieve_course_categories();

        while($category = $categories->next_result())
        {
            $arr[$category->get_name()][0] = 0;
            $condition = new EqualityCondition(Weblcms :: PARAM_COURSE_CATEGORY_ID,$category->get_id());
            $courses = $wdm->retrieve_courses(null, $condition);
            while($course = $courses->next_result())
            {
                $arr[$category->get_name()][0]++;
            }
        }

        return Reporting::getSerieArray($arr);
    }//getCoursesPerCategory

    public static function getLearningPathProgress($params)
    {
    	$data = array();
    	$objects = $params['objects'];
	    $attempt_data = $params['attempt_data'];
	    $cid = $params['cid'];
	    $url = $params['url'];
	    $total = 0;
	    
    	if($cid)
    	{
    		$object = $objects[$cid];
    		$tracker_datas = $attempt_data[$cid];
    		
    		foreach($tracker_datas['trackers'] as $tracker)
    		{
    			$data[Translation :: get('LastStartTime')][] = DokeosUtilities :: to_db_date($tracker->get_start_time());
    			$data[Translation :: get('Status')][] = Translation :: get($tracker->get_status() == 'completed'?'Completed':'Incomplete');
	    		$data[Translation :: get('Score')][] = $tracker->get_score() . '%';
	    		$data[Translation :: get('Time')][] = DokeosUtilities :: format_seconds_to_hours($tracker->get_total_time());
	    		$total += $tracker->get_total_time();
	    		
	    		if($params['delete'])
	    			$data[''][] = Text :: create_link($params['url'] . '&stats_action=delete_lpi_attempt&delete_id=' . $tracker->get_id(), Theme :: get_common_image('action_delete'));
    		}
    	}
    	else 
    	{
	    	foreach($objects as $wrapper_id => $object)
	    	{
	    		$tracker_data = $attempt_data[$wrapper_id];
	    		
	    		$data[''][] = $object->get_icon();
	    		$data[Translation :: get('Title')][] = '<a href="' . $url . '&cid=' . $wrapper_id . '">' . $object->get_title() . '</a>';
	    		
	    		if($tracker_data)
	    		{
	    			$data[Translation :: get('Status')][] = Translation :: get($tracker_data['completed']?'Completed':'Incomplete');
	    			$data[Translation :: get('Score')][] = round($tracker_data['score'] / $tracker_data['size']) . '%';
	    			$data[Translation :: get('Time')][] = DokeosUtilities :: format_seconds_to_hours($tracker_data['time']);
	    			$total += $tracker_data['time'];
	    		}
	    		else
	    		{
	    			$data[Translation :: get('Status')][] = 'incomplete';
	    			$data[Translation :: get('Score')][] = '0%';
	    			$data[Translation :: get('Time')][] = '0:00:00';
	    		}
	    		
	    		if($params['delete'])
	    			$data[' '][] = Text :: create_link($params['url'] . '&stats_action=delete_lpi_attempts&item_id=' . $wrapper_id, Theme :: get_common_image('action_delete'));
	    	}
    	}

    	$data[Translation :: get('Status')][] = '<span style="font-weight: bold;">' . Translation :: get('TotalTime') . '</span>';
    	$data[Translation :: get('Time')][] = '<span style="font-weight: bold;">' . DokeosUtilities :: format_seconds_to_hours($total) . '</span>';
    	
        $description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;

        return Reporting :: getSerieArray($data, $description);
    }
    
    public static function getLearningPathAttempts($params)
    {
    	$data = array();
    	
    	$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $params['course']);
    	$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $params['publication']->get_id());
		$condition = new AndCondition($conditions);
		
		$udm = UserDataManager :: get_instance();
		
		$dummy = new WeblcmsLpAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		foreach($trackers as $tracker)
		{
			$url = $params['url'] . '&attempt_id=' . $tracker->get_id();
			$delete_url = $url . '&stats_action=delete_lp_attempt';
			
			$user = $udm->retrieve_user($tracker->get_user_id());
			$data[Translation :: get('User')][] = $user->get_fullname();
			$data[Translation :: get('Progress')][] = $tracker->get_progress() . '%';
			//$data[Translation :: get('Details')][] = '<a href="' . $url . '">' . Theme :: get_common_image('action_reporting') . '</a>';
			$data[''][] = Text :: create_link($url, Theme :: get_common_image('action_reporting')) . ' ' . 
						  Text :: create_link($delete_url, Theme :: get_common_image('action_delete'));
		}
		
		$description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($data, $description);
    }
}
?>
