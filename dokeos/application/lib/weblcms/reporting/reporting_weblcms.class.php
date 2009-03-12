<?php
/**
 * @author Michael Kyndt
 */
class ReportingWeblcms {

    function ReportingWeblcms() {
    }

    public static function getCourseInformation($params)
    {
        $array = array();

        $data[] = array("Name"=>"Course Name","Serie1"=>"Italiaans Breakthrough A + B 1e jaar woensddag");
        $data[] = array("Name"=>"Totaal aantal verbindingen met deze cursus","Serie1"=>76);
        $data[] = array("Name"=>"Begeleider (groepen)","Serie1"=>"Dominique De Guchtenaere");

        $datadescription["Position"] = "Name";
        $datadescription["Values"][] = "Serie1";

        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }

    public static function getCourseUserLearningpathInformation($params)
    {
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
//        $datadescription["Description"]["Serie1"] = "Learning Paths";
//        $datadescription["Description"]["Serie2"] = "Time";
//        $datadescription["Description"]["Serie3"] = "Score";
//        $datadescription["Description"]["Serie4"] = "Completed";
//        $datadescription["Description"]["Serie5"] = "Last Connection";
//        $datadescription["Description"]["Serie6"] = "Inactive";
//        $datadescription["Description"]["Serie7"] = "Active";
//        $datadescription["Description"]["Serie8"] = "Inactive";

        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }

    public static function CourseUserExerciseInformation($params)
    {
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
//        $datadescription["Description"]["Serie1"] = "Learning Paths";
//        $datadescription["Description"]["Serie2"] = "Time";
//        $datadescription["Description"]["Serie3"] = "Score";
//        $datadescription["Description"]["Serie4"] = "Completed";
//        $datadescription["Description"]["Serie5"] = "Last Connection";
//        $datadescription["Description"]["Serie6"] = "Inactive";
//        $datadescription["Description"]["Serie7"] = "Active";
//        $datadescription["Description"]["Serie8"] = "Inactive";

        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }
}
?>