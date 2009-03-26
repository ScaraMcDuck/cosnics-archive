<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../lib/user_data_manager.class.php';
class ReportingUser {

    function ReportingUser() {
    }

    /**
     * Checks if a given start date is greater than a given end date
     * @param <type> $start_date
     * @param <type> $end_date
     * @return <type>
     */
    public static function greaterDate($start_date,$end_date)
    {
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        if ($start-$end > 0)
        return 1;
        else
        return 0;
    }

    /**
     * Returns all the active and inactive users
     * @param <type> $params
     * @return <type>
     */
    public static function getActiveInactive($params)
    {
        $udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $active[Translation :: get('Active')][0] = 0;
        $active[Translation :: get('Inactive')][0] = 0;
        while($user = $users->next_result())
        {
            if($user->get_active())
            {
                $active[Translation :: get('Active')][0]++;
            }
            else
            {
                $active[Translation :: get('Inactive')][0]++;
            }
        }
        return Reporting :: getSerieArray($active);
    }//getActiveInactive

    /**
     * Returns the number of users
     * @return <type>
     */
    public static function getNoOfUsers()
    {
        $udm = UserDataManager :: get_instance();

        $arr[Translation :: get('NumberOfUsers')][] = $udm->count_users();

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the number of logins
     * @return <type>
     */
    public static function getNoOfLogins()
    {
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $arr[Translation :: get('Logins')][] = sizeof($trackerdata);

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Splits given data into a given date format
     * @param <type> $data
     * @param <type> $format
     * @return <type>
     */
    public static function getDateArray($data,$format)
    {
        $arr = array();
        foreach($data as $key => $value)
        {
            $bla =  explode('-',$value->get_date());
            $bla2 = explode(' ',$bla[2]);
            $hoursarray = explode(':',$bla2[1]);
            $date = date($format,mktime($hoursarray[0],$hoursarray[1],$hoursarray[2],$bla[1],$bla2[0],$bla[0]));
            $date = (is_numeric($date))?$date:Translation :: get($date.'Long');
            //dump($date);
            if (array_key_exists($date, $arr))
            {
                $arr[$date][0]++;
            }else
            {
                $arr[$date][0] = 1;
            }
        }
        return $arr;
    }

    /**
     * Returns the number of logins per month
     * @return <type>
     */
    public static function getNoOfLoginsMonth()
    {
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $months = self :: getDateArray($trackerdata,'F');

        return Reporting :: getSerieArray($months);
    }

    /**
     * Returns the number of logins per day
     * @return <type>
     */
    public static function getNoOfLoginsDay()
    {
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $days = self :: getDateArray($trackerdata,'l');

        return Reporting :: getSerieArray($days);
    }

    /**
     * Returns the number of logins per hour
     * @return <type>
     */
    public static function getNoOfLoginsHour()
    {
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $hours = self :: getDateArray($trackerdata,'G');

        ksort($hours);

        return Reporting :: getSerieArray($hours);
    }

    /**
     * returns the number of users with and without picture
     * @return <type>
     */
    public static function getNoOfUsersPicture()
    {
        $udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $picturetext = Translation :: get('Picture');
        $nopicturetext = Translation :: get('NoPicture');
        $picture[$picturetext][0] = 0;
        $picture[$nopicturetext][0] = 0;
        while($user = $users->next_result())
        {
            if($user->get_picture_uri())
            {
                $picture[$picturetext][0]++;
            }
            else
            {
                $picture[$nopicturetext][0]++;
            }
        }
        return Reporting :: getSerieArray($picture);
    }

    /**
     * Returns the number of users subscribed to a course
     * @return <type>
     */
    public static function getNoOfUsersSubscribedCourse()
    {
        require_once Path :: get_application_path().'lib/weblcms/weblcms_data_manager.class.php';
        $udm = UserDataManager :: get_instance();
        $users = $udm->count_users();

        $wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->count_user_courses();

        $arr[Translation :: get('UsersSubscribedToCourse')][] = $courses;
        $arr[Translation :: get('UsersNotSubscribedToCourse')][] = $users-$courses;

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the user information about a specified user
     * @param <type> $params
     * @return <type>
     */
    public static function getUserInformation($params)
    {
        $uid = $params[ReportingManager :: PARAM_USER_ID];
        //$uid = 2;
        require_once Path :: get_admin_path().'/trackers/online_tracker.class.php';
        $udm = UserDataManager :: get_instance();
        $tracking = new OnlineTracker();

        $items = $tracking->retrieve_tracker_items();
        foreach($items as $item)
        {
            if($item->get_user_id()==$uid)
            {
                $online = 1;
            }
        }

        $user = $udm->retrieve_user($uid);

        $arr[Translation :: get('Name')][] = $user->get_fullname();
        $arr[Translation :: get('Email')][] = $user->get_email();
        $arr[Translation :: get('Phone')][] = $user->get_phone();
        //$arr[Translation :: get('Status')] = $user->get_status_name();
        $arr[Translation :: get('Online')][] = ($online)?Translation :: get('Online'):Translation :: get('Offline');

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the platform statistics from a specified user
     * @param <type> $params
     * @return <type>
     */
    public static function getUserPlatformStatistics($params)
    {
        $uid = $params[ReportingManager :: PARAM_USER_ID];
        //$uid = 2;
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $conditions[] = new EqualityCondition(LoginLogoutTracker::PROPERTY_USER_ID,$uid);
        $conditions[] = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $condition = new AndCondition($conditions);
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        //dump($condition);
        foreach($trackerdata as $key => $value)
        {
            if(!$firstconnection)
            {
                //$firstconnection = $value->get_date();
                $firstconnection = $value->get_date();
                $lastconnection = $value->get_date();
            }
            if(!self :: greaterDate($value->get_date(), $firstconnection))
            {
                $firstconnection = $value->get_date();
            }else if(self :: greaterDate($value->get_date(),$lastconnection))
            {
                $lastconnection = $value->get_date();
            }
        }

        $arr[Translation :: get('FirstConnection')][] = $firstconnection;
        $arr[Translation :: get('LastConnection')][] = $lastconnection;
        $arr[Translation :: get('TimeOnPlatform')][] = '00:00:00';

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of browsers and their amount
     * @return <type>
     */
    public static function getBrowsers()
    {
        require_once(dirname(__FILE__) . '/../trackers/browsers_tracker.class.php');
        $tracker = new BrowsersTracker();
        $condition = new EqualityCondition(BrowsersTracker::PROPERTY_TYPE,'browser');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Browsers'));
    }

    /**
     * Returns a list of countries logged in from and their amount
     * @return <type>
     */
    public static function getCountries()
    {
        require_once(dirname(__FILE__) . '/../trackers/countries_tracker.class.php');
        $tracker = new CountriesTracker();
        $condition = new EqualityCondition(CountriesTracker::PROPERTY_TYPE,'country');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Countries'));
    }

    /**
     * Returns a list of os logged in from and their amount
     * @return <type>
     */
    public static function getOs()
    {
        require_once(dirname(__FILE__) . '/../trackers/os_tracker.class.php');
        $tracker = new OSTracker();
        $condition = new EqualityCondition(OSTracker :: PROPERTY_TYPE,'os');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Os'));
    }

    /**
     * Returns a list of providers logged in from and their amount
     * @return <type>
     */
    public static function getProviders()
    {
        require_once(dirname(__FILE__) . '/../trackers/providers_tracker.class.php');
        $tracker = new ProvidersTracker();
        $condition = new EqualityCondition(ProvidersTracker :: PROPERTY_TYPE,'provider');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Providers'));
    }

    /**
     * Returns a list of referers logged in from and their amount
     * @return <type>
     */
    public static function getReferers()
    {
        require_once(dirname(__FILE__) . '/../trackers/referrers_tracker.class.php');
        $tracker = new ReferrersTracker();
        $condition = new EqualityCondition(ReferrersTracker :: PROPERTY_TYPE,'referer');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Referers'));
    }
}
?>