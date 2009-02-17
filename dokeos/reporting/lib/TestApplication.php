<?php
/*
 * Just a sample of the reporting within an application
 */
 
class TestApplication{
	//Hoe ziet een array er standaard uit?
	// "bla" => "bla",
	//of
	//array( "bla", "bla", "bla")
	public static function getActiveInactive(){
		$array = array();
		$data[] = array("Name"=>"Active","Serie1"=>6);
		$data[] = array("Name"=>"Inactive","Serie2"=>10);
		
		$datadescription["Position"] = "Name";
		$datadescription["Values"][] = "Serie1";
		$datadescription["Values"][] = "Serie2";
		$datadescription["Description"]["Serie1"] = "Active";
		$datadescription["Description"]["Serie2"] = "Inactive";
		
		array_push($array,$data);
		array_push($array,$datadescription);
 		return $array;
 	}//getActiveInactive
 	
 	public static function getActiveInactivePerYearAndMonth(){
		$array = array();
		$data[] = array("Name"=>"Januari","Serie1"=>1500);
		$data[] = array("Name"=>"Februari","Serie1"=>1000);
		$data[] = array("Name"=>"March","Serie1"=>1200);
		$data[] = array("Name"=>"April","Serie1"=>1300);
		$data[] = array("Name"=>"May","Serie1"=>1500);
		$data[] = array("Name"=>"June","Serie1"=>1900);
		
		$data[] = array("Name"=>"Januari","Serie2"=>100);
		$data[] = array("Name"=>"Februari","Serie2"=>1000);
		$data[] = array("Name"=>"March","Serie2"=>500);
		$data[] = array("Name"=>"April","Serie2"=>1100);
		$data[] = array("Name"=>"May","Serie2"=>1500);
		$data[] = array("Name"=>"June","Serie2"=>1300);
		
		$datadescription["Position"] = "Name";
		$datadescription["Values"][] = "Serie1";
		$datadescription["Values"][] = "Serie2";
		$datadescription["Description"]["Serie1"] = "Active";
		$datadescription["Description"]["Serie2"] = "Inactive";
		
		array_push($array,$data);
		array_push($array,$datadescription);
 		return $array;
 	}//getActiveInactive
 	
 }//class application
 ?>
