<?php
/**
 * $Id: announcement_display.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage announcement
 */
/**
 * This class can be used to display announcements
 */



class RdpublicationDisplay extends LearningObjectDisplay
{

    function get_full_html($userid)
	{
		$html = array();
		//$html[] = parent :: get_full_html();

		$object = $this->get_learning_object();
        
           

        $offcode=$userid;
        
        $publication_id=$object->get_ref_id();
        
		$query = "select * from r2d2_cur.publication p INNER JOIN r2d2_cur.publication_member pm on p.pub_id = pm.pub_id where pm.person_id='".$offcode."'and p.pub_id = '".$publication_id."' order by p.pub_date DESC, pub_type ASC";

		$result=mysql_query($query);
		if($result && (mysql_num_rows($result) != 0))
		{
			$prevyear=0;
            
			$prevcat=null;
			while($row=mysql_fetch_row($result))
                
                {

                	$year=substr($row[2],0,4);
                    
                     $html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'/learning_object/portfolio_item.png);">';
                	if(!($row[3] === $prevcat))
                	{
                		$html[] = '<div class="title">'.$row[3].": ".$row[4].'  [ '.$year.' ]</div>';
                		$prevcat=$row[3];
                	}
                	$html[] = '<div class="description">';
                	$query = "select title from r2d2_cur.publication_abstract where pub_id='".$row[0]."'";
                	$res2=mysql_query($query);
                	$title= mysql_fetch_row($res2);
                   	$html[]= "&nbsp;&nbsp;&nbsp;&nbsp;".$title[0]."<br /><br />";
                	$html[]= "&nbsp;&nbsp;&nbsp;&nbsp;<b>Authors: </b>";

                	$query = "select * from r2d2_cur.publication_member where pub_id='".$row[0]."' order by sequence";
                	$res3=mysql_query($query);
                	$start=true;
                	while($mem=mysql_fetch_row($res3))
                	{
                		if(!$start) $html[]= ", ";
                		$html[]= $mem[5]." ".$mem[6];
                		$start=false;
                	}
                	$html[]= "<br />";
                	$html[]= "&nbsp;&nbsp;&nbsp;&nbsp;<b>Reference: </b>".$row[1]."<br />";
                	$html[]= "<br />";
                    $html[]="</div>";
                }
                
            $html[]= "</table>";
             $html[] = '</div>';
		}
            
           
            //$html[] = '</div>';

		return implode("\n", $html);
	}



}
?>