<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../lib/user_data_manager.class.php';
class ReportingUser {

    function ReportingUser() {
    }

    public static function getBrowsers()
    {
        require_once(dirname(__FILE__) . '/../trackers/browsers_tracker.class.php');
        $tracker = new BrowsersTracker();

        return Reporting :: array_from_tracker($tracker);
    }

    public static function getCountries()
    {
        require_once(dirname(__FILE__) . '/../trackers/countries_tracker.class.php');
        $tracker = new CountriesTracker();

        return Reporting :: array_from_tracker($tracker);
    }

    public static function getOs()
    {
        require_once(dirname(__FILE__) . '/../trackers/os_tracker.class.php');
        $tracker = new OSTracker();

        return Reporting :: array_from_tracker($tracker);
    }

    public static function getProviders()
    {
        require_once(dirname(__FILE__) . '/../trackers/providers_tracker.class.php');
        $tracker = new ProvidersTracker();

        return Reporting :: array_from_tracker($tracker);
    }

    public static function getReferers()
    {
        require_once(dirname(__FILE__) . '/../trackers/referrers_tracker.class.php');
        $tracker = new ReferrersTracker();

        return Reporting :: array_from_tracker($tracker);
    }

    public static function getUserInformation($params)
    {
        $array = array();
        $data[] = array("Name"=>"Eerste Login","Serie1"=>"12 september 2007");
        $data[] = array("Name"=>"Laatste verbinding","Serie1"=>"09 januari 2009");
        $data[] = array("Name"=>"Tijd doorgebracht op het platform","Serie1"=>"20:01:19");
        $data[] = array("Name"=>"Voortgang","Serie1"=>"0%");
        $data[] = array("Name"=>"Score","Serie1"=>"0%");

        $datadescription["Position"] = "Name";
        $datadescription["Values"][] = "Serie1";

        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }

    public static function getUserPlatformStatistics()
    {
        $array = array();
        $data[] = array("Name"=>"Bus","Serie1"=>"12 september 2007");
        $data[] = array("Name"=>"Laatste verbinding","Serie1"=>"09 januari 2009");
        $data[] = array("Name"=>"Tijd doorgebracht op het platform","Serie1"=>"20:01:19");
        $data[] = array("Name"=>"Voortgang","Serie1"=>"0%");
        $data[] = array("Name"=>"Score","Serie1"=>"0%");

        $datadescription["Position"] = "Name";
        $datadescription["Values"][] = "Serie1";

        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }

    public static function getActiveInactive()
    {
        //UserDataManager :: get_instance()->retrieve_users();
        $array = array();

        $data[] = array("Name"=>"Active","Serie1"=>1500);
        $data[] = array("Name"=>"Inactive","Serie1"=>200);

        $datadescription["Position"] = "Name";
        $datadescription["Values"][] = "Serie1";

        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }//getActiveInactive

    public static function getActiveInactivePerYearAndMonth()
    {
        $array = array();
        $data[] = array("Name"=>"Januari","Serie1"=>1500,"Serie2"=>100);
        $data[] = array("Name"=>"Februari","Serie1"=>1000,"Serie2"=>200);
        $data[] = array("Name"=>"March","Serie1"=>1200,"Serie2"=>500);
        $data[] = array("Name"=>"April","Serie1"=>1300,"Serie2"=>300);
        $data[] = array("Name"=>"May","Serie1"=>1500,"Serie2"=>150);
        $data[] = array("Name"=>"June","Serie1"=>1900,"Serie2"=>200);

        $datadescription["Position"] = "Name";
        $datadescription["Values"][] = "Serie1";
        $datadescription["Values"][] = "Serie2";
        $datadescription["Description"]["Serie1"] = "Active";
        $datadescription["Description"]["Serie2"] = "Inactive";

        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }//getActiveInactivePerYerAndMonth
}
?>