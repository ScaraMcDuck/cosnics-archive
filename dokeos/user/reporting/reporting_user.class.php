<?php
require_once '../trackers/browsers_tracker.class.php';
class ReportingUser {

    function ReportingUser() {
    }
    
    public static function getBrowers()
    {
    	$browerstracker = new BrowsersTracker();
    	//dump($browerstracker->export('01/01/1990','01/01/2010',))
    }
    
	public static function getActiveInactive()
	{
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