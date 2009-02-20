<?php
/*
 * Just a sample of the reporting within an application
 * 
 * @author: Michael Kyndt
 */
 
class TestApplication{
	
	/*
	 * 
	 */
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
 	}//getActiveInactive
 	
 	public static function testData()
 	{
 		$array = array();
 		$data[] = array("Name"=>"Januari","Serie1"=>10,"Serie2"=>8,"Serie3"=>12);
 		$data[] = array("Name"=>"Februari","Serie1"=>5,"Serie2"=>6,"Serie3"=>11);
 		$data[] = array("Name"=>"March","Serie1"=>15,"Serie2"=>7,"Serie3"=>10);
 		$data[] = array("Name"=>"April","Serie1"=>8,"Serie2"=>10,"Serie3"=>9);
 		$data[] = array("Name"=>"May","Serie1"=>6,"Serie2"=>15,"Serie3"=>8);
 		$data[] = array("Name"=>"June","Serie1"=>7,"Serie2"=>3,"Serie3"=>7);
 		$data[] = array("Name"=>"July","Serie1"=>12,"Serie2"=>5,"Serie3"=>6);
 		$data[] = array("Name"=>"August","Serie1"=>11,"Serie2"=>8,"Serie3"=>5);
 		$data[] = array("Name"=>"September","Serie1"=>3,"Serie2"=>10,"Serie3"=>4);
 		$data[] = array("Name"=>"October","Serie1"=>6,"Serie2"=>4,"Serie3"=>3);
 		$data[] = array("Name"=>"November","Serie1"=>1,"Serie2"=>1,"Serie3"=>2);
 		$data[] = array("Name"=>"December","Serie1"=>5,"Serie2"=>5,"Serie3"=>1);
 		
 		$datadescription["Position"] = "Name";
 		$datadescription["Values"] = array("Serie1","Serie2","Serie3");
 		$datadescription["Description"]["Serie1"] = "Stock Europe";
 		$datadescription["Description"]["Serie2"] = "Stock America";
 		$datadescription["Description"]["Serie3"] = "Shareholders";
 		
 		array_push($array,$data);
 		array_push($array,$datadescription);
 		return $array;
 	}
 	
 	public static function getPlatformStats()
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
 	
 	
 }//class application
 ?>
