<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	@author Thomas Depraetere
*	@author Hugues Peeters
*	@author Christophe Gesche
*	@author Sebastien Piraux
*	@author Toon Keppens (Vi-Host.net)
*
*	@package dokeos.tracking
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/
$pathopen = isset($_REQUEST['pathopen']) ? $_REQUEST['pathopen'] : null;
api_use_lang_files("tracking");

include('../inc/claro_init_global.inc.php');

// charset determination
if ($_GET['scormcontopen'])
	{
	include_once(api_get_library_path().'/database.lib.php');
	include('../scorm/XMLencode.php');
	$TBL_SCORM_MAIN     = Database :: get_scorm_table(SCORM_MAIN_TABLE);
	$result = api_sql_query("SELECT contentTitle FROM $TBL_SCORM_MAIN where (contentId=".$_GET['scormcontopen'].")");
	$ar = mysql_fetch_array($result);
	$contentTitle = $ar['contentTitle'];
	$path=api_get_path('SYS_COURSE_PATH');
	$file=$path.$_cid.'/scorm'.$contentTitle.'/imsmanifest.xml';
	$charset = GetXMLEncode($file);
	header('Content-Type: text/html; charset='. $charset);
	}

$nameTools = get_lang('ToolName');

$htmlHeadXtra[] = "<style type='text/css'>
/*<![CDATA[*/
.secLine {background-color : #E6E6E6;}
.content {padding-left : 15px;padding-right : 15px; }
.specialLink{color : #0000FF;}
/*]]>*/
</style>
<style media='print' type='text/css'>
/*<![CDATA[*/
td {border-bottom: thin dashed gray;}
/*]]>*/
</style>";


/*
-----------------------------------------------------------
	Constants and variables
-----------------------------------------------------------
*/
// regroup table names for maintenance purpose
$TABLETRACK_ACCESS      = Database::get_statistic_table(STATISTIC_TRACK_E_LASTACCESS_TABLE);
$TABLETRACK_LINKS       = Database::get_statistic_table(STATISTIC_TRACK_E_LINKS_TABLE);
$TABLETRACK_DOWNLOADS   = Database::get_statistic_table(STATISTIC_TRACK_E_DOWNLOADS_TABLE);
$TABLETRACK_ACCESS_2    = Database::get_statistic_table("track_e_access");
$TABLECOURSUSER	        = Database::get_main_table(MAIN_COURSE_USER_TABLE);
$TABLECOURSE	        = Database::get_main_table(MAIN_COURSE_TABLE);
$TABLECOURSE_LINKS      = Database::get_course_table(LINK_TABLE);
$table_user = Database::get_main_table(MAIN_USER_TABLE);
$table_scormdata = Database::get_scorm_table(SCORM_SCO_DATA_TABLE);
$table_scormmain = Database::get_scorm_table(SCORM_MAIN_TABLE);
$tbl_learnpath_main = Database::get_course_table(LEARNPATH_MAIN_TABLE);
$tbl_learnpath_item = Database::get_course_table(LEARNPATH_ITEM_TABLE);
$tbl_learnpath_chapter = Database::get_course_table(LEARNPATH_CHAPTER_TABLE);
$tbl_learnpath_user = Database::get_course_table(LEARNPATH_USER_TABLE);

$view = $_REQUEST['view'];
Display::display_header($nameTools, "Tracking");
include(api_get_library_path()."/statsUtils.lib.inc.php");
include("../resourcelinker/resourcelinker.inc.php");

$is_allowedToTrack = $is_courseAdmin;

/*
==============================================================================
		MAIN CODE
==============================================================================
*/
?>
<br>
<h3><?php echo get_lang('StatsOfCourse')." : ".$_course['official_code']; ?></h3>
<p><?php echo get_lang('SeeIndividualTracking'); ?></p>
<br>
<table width="100%" cellpadding="2" cellspacing="3" border="0">
<?php
// check if uid is prof of this group

if($is_allowedToTrack && $is_trackingEnabled)
{
    // show all : view must be equal to the sum of all view values (1024+512+...+64)
    // show none : less than the tiniest value
    echo "<tr>
            <td>
            [<a href='".$_SERVER['PHP_SELF']."?view=1111111'>".get_lang('ShowAll')."</a>]
            [<a href='".$_SERVER['PHP_SELF']."?view=0000000'>".get_lang('ShowNone')."</a>]
            </td>
        </tr>
    ";

    if(!isset($view)) $view ="0000000";




    /***************************************************************************
     *
     *		Reporting
     *
     ***************************************************************************/
    $tempView = $view;
    if($view[6] == '1')
    {
        $tempView[6] = '0';
        echo "
            <tr>
                    <td valign='top'>
                    <font color='#0000FF'>-&nbsp;&nbsp;&nbsp;</font><b>".get_lang('SynthesisView')."</b>&nbsp;&nbsp;&nbsp;[<a href='".$_SERVER['PHP_SELF']."?view=".$tempView."'>".get_lang('Close')."</a>]
                    </td>
            </tr>
            <tr>
                    <td valign='top' style='padding-left: 40px'>
            			 <table width='550' border='0' cellspacing='1' cellpadding='3'>
							<tr>
								<td class='secLine' width='200'>".get_lang('Name')."</td>
								<td class='secLine' width='125'>".get_lang('FirstAccess')."</td>
								<td class='secLine' width='125'>".get_lang('LastAccess')."</td>
								<td class='secLine' width='100'>%&nbsp;".get_lang('Visited')."</td>
							</tr>
        ";


        //--------------------------------BEGIN users in this course
        $sql = "SELECT $TABLECOURSUSER.`user_id`, $table_user.`lastname`, $table_user.`firstname`
                    FROM $TABLECOURSUSER, $table_user
                    WHERE $TABLECOURSUSER.course_code = '".$_cid."' AND $TABLECOURSUSER.`user_id` = $table_user.`user_id`
                    ORDER BY $table_user.`lastname`";
        $results = getManyResults3Col($sql);

        //BUGFIX: get visual code instead of real course code. Scormpaths use the visual code... (should be fixed in future versions)
        $sql = "SELECT visual_code FROM $TABLECOURSE WHERE code = '".$_cid."'";
        $_course['visual_code'] = getOneResult($sql);

        if (is_array($results))
        {
            for($j = 0 ; $j < count($results) ; $j++)
            {


            	//--------------------------------BEGIN % visited
            	// sum of all items (= multiple learningpaths + SCORM imported paths)
            	$sql = "SELECT COUNT(*) FROM $tbl_learnpath_user WHERE `user_id` = '".$results[$j][0]."'";
            	$total_lpath_items = getOneResult($sql);
            	//$sql = "SELECT count( a.scoId ) FROM $table_scormdata a INNER JOIN $table_scormmain b ON a.contentId = b.contentId WHERE (b.dokeosCourse = '".$_course['official_code']."' OR b.dokeosCourse = '".$_course['visual_code']."') AND `studentId` = '".$results[$j][0]."'";
            	$sql = "SELECT count( a.scoId ) FROM $table_scormdata a INNER JOIN $table_scormmain b ON a.contentId = b.contentId WHERE (b.dokeosCourse = '".$_course['visual_code']."') AND `studentId` = '".$results[$j][0]."'";
            	$total_lpath_items += getOneResult($sql);


            	// sum of all completed items (= multiple learningpaths + SCORM imported paths)
            	$sql = "SELECT COUNT(*) FROM $tbl_learnpath_user WHERE `user_id` = '".$results[$j][0]."' AND `status` = 'completed'";
            	$total_lpath_items_completed = getOneResult($sql);
            	//$sql = "SELECT count( a.scoId ) FROM $table_scormdata a INNER JOIN $table_scormmain b ON a.contentId = b.contentId WHERE (b.dokeosCourse = '".$_course['official_code']."' OR b.dokeosCourse = '".$_course['visual_code']."') AND `studentId` = '".$results[$j][0]."' AND `status` = 'completed'";
            	$sql = "SELECT count( a.scoId ) FROM $table_scormdata a INNER JOIN $table_scormmain b ON a.contentId = b.contentId WHERE (b.dokeosCourse = '".$_course['visual_code']."') AND `studentId` = '".$results[$j][0]."' AND `status` = 'completed'";
            	$total_lpath_items_completed += getOneResult($sql);

            	// calculation & bgcolor setting
            	$lpath_pct_completed = empty($total_lpath_items) ? "-" : round(($total_lpath_items_completed / $total_lpath_items) * 100);
            	$lpath_pct_completed_color = $lpath_pct_completed < 75 ? " bgcolor='#CC3333'" : " bgcolor='#99CC66'";
            	//--------------------------------END % visited



            	//--------------------------------BEGIN first/last access
            	// first access
            	$sql = "SELECT access_date FROM $TABLETRACK_ACCESS_2 WHERE `access_user_id` = '".$results[$j][0]."' AND `access_cours_code` = '".$_course['official_code']."' AND `access_tool` = 'learnpath' ORDER BY access_id ASC LIMIT 1";
            	$first_access = getOneResult($sql);
            	$first_access = empty($first_access) ? "-" : date('d.m.y',strtotime($first_access));

            	// last access
            	$sql = "SELECT access_date FROM $TABLETRACK_ACCESS WHERE `access_user_id` = '".$results[$j][0]."' AND `access_cours_code` = '".$_course['official_code']."' AND `access_tool` = 'learnpath'";
            	$last_access = getOneResult($sql);
            	$last_access = empty($last_access) ? "-" : date('d.m.y',strtotime($last_access));
            	//--------------------------------END first/last access



            	//--------------------------------BEGIN presentation of data
				echo "		<tr>";
				echo "			<td width='200'>".$results[$j][1]." ".$results[$j][2]."</td>";
				echo "			<td width='125'>".$first_access."</td>";
				echo "			<td width='125'>".$last_access."</td>";
				echo "			<td width='100' align='center'".$lpath_pct_completed_color.">".$lpath_pct_completed."</td>";
				echo "		</tr>";
				//--------------------------------END presentation of data
            }

        }
        else
        {
            echo "<tr>";
            echo "<td colspan='4'><center>".get_lang('NoResult')."</center></td>";
            echo"</tr>";
        }

		echo "			</table>
					</td>
				</tr>
		";
		//--------------------------------END users in this course

    }
    else
    {
        $tempView[6] = '1';
        echo "
            <tr>
                    <td valign='top'>
                    +<font color='#0000FF'>&nbsp;&nbsp;</font><a href='".$_SERVER['PHP_SELF']."?view=".$tempView."' class='specialLink'>".get_lang('SynthesisView')."</a>
                    </td>
            </tr>
        ";

    }

    /***************************************************************************
     *
     *		Main
     *
     ***************************************************************************/

    $tempView = $view;
    if($view[0] == '1')
    {
        $tempView[0] = '0';
        echo "
            <tr>
                    <td valign='top'>
                    <font color='#0000FF'>-&nbsp;&nbsp;&nbsp;</font><b>".get_lang('CourseStats')."</b>&nbsp;&nbsp;&nbsp;[<a href='".$_SERVER['PHP_SELF']."?view=".$tempView."'>".get_lang('Close')."</a>]
                    </td>
            </tr>
        ";

        $sql = "SELECT count(*)
                    FROM $TABLECOURSUSER
                    WHERE course_code = '".$_cid."'";
        $count = getOneResult($sql);
        echo "
            <tr>
                <td style='padding-left : 40px;' valign='top'>
                ".get_lang('CountUsers')." : ".$count."
                </td>
            </tr>
        ";

    }
    else
    {
        $tempView[0] = '1';
        echo "
            <tr>
                    <td valign='top'>
                    +<font color='#0000FF'>&nbsp;&nbsp;</font><a href='".$_SERVER['PHP_SELF']."?view=".$tempView."' class='specialLink'>".get_lang('CourseStats')."</a>
                    </td>
            </tr>
        ";
    }

    /***************************************************************************
     *
     *		Access to this course
     *
     ***************************************************************************/
    $tempView = $view;
    if($view[1] == '1')
    {
        $tempView[1] = '0';
        echo "
            <tr>
                    <td valign='top'>
                    <font color='#0000FF'>-&nbsp;&nbsp;&nbsp;</font><b>".get_lang('CourseAccess')."</b>&nbsp;&nbsp;&nbsp;[<a href='".$_SERVER['PHP_SELF']."?view=".$tempView."'>".get_lang('Close')."</a>]
                    </td>
            </tr>
        ";
        $sql = "SELECT count(*)
                    FROM $TABLETRACK_ACCESS
                    WHERE access_cours_code = '".$_cid."'
                        AND access_tool IS NULL";
        $count = getOneResult($sql);
        echo "
            <tr>
                <td style='padding-left : 40px;' valign='top'>"
                .get_lang('CountToolAccess')." : ".$count."
                </td>
            </tr>
        ";
        // last 31 days
        $sql = "SELECT count(*)
                    FROM $TABLETRACK_ACCESS
                    WHERE `access_cours_code` = '$_cid'
                        AND (access_date > DATE_ADD(CURDATE(), INTERVAL -31 DAY))
                        AND access_tool IS NULL";
        $count = getOneResult($sql);
        echo "
            <tr>
                <td style='padding-left : 40px;' valign='top'>
                ".get_lang('Last31days')." : ".$count."
                </td>
            </tr>
        ";
        // last 7 days
        $sql = "SELECT count(*)
                    FROM $TABLETRACK_ACCESS
                    WHERE `access_cours_code` = '$_cid'
                        AND (access_date > DATE_ADD(CURDATE(), INTERVAL -7 DAY))
                        AND access_tool IS NULL";
        $count = getOneResult($sql);
        echo "
            <tr>
                <td style='padding-left : 40px;' valign='top'>
                ".get_lang('Last7days')." : ".$count."
                </td>
            </tr>
        ";
        // today
        $sql = "SELECT count(*)
                    FROM $TABLETRACK_ACCESS
                    WHERE `access_cours_code` = '$_cid'
                        AND ( access_date > CURDATE() )
                        AND access_tool IS NULL";
        $count = getOneResult($sql);
        echo "
            <tr>
                <td style='padding-left : 40px;' valign='top'>
                ".get_lang('Thisday')." : ".$count."
                </td>
            </tr>
        ";
        //-- view details of traffic
        echo "
            <tr>
                <td style='padding-left : 40px;' valign='top'>
                <a href='course_access_details.php'>".get_lang('TrafficDetails')."</a>
                </td>
            </tr>
        ";

    }
    else
    {
        $tempView[1] = '1';
        echo "
            <tr>
                    <td valign='top'>
                    +<font color='#0000FF'>&nbsp;&nbsp;</font><a href='".$_SERVER['PHP_SELF']."?view=".$tempView."' class='specialLink'>".get_lang('CourseAccess')."</a>
                    </td>
            </tr>
        ";

    }


    /***************************************************************************
     *
     *		Tools
     *
     ***************************************************************************/
    $tempView = $view;
    if($view[2] == '1')
    {
        $tempView[2] = '0';
        echo "
            <tr>
                    <td valign='top'>
                    <font color='#0000FF'>-&nbsp;&nbsp;&nbsp;</font><b>".get_lang('ToolsAccess')."</b>&nbsp;&nbsp;&nbsp;[<a href='".$_SERVER['PHP_SELF']."?view=".$tempView."'>".get_lang('Close')."</a>]
                    </td>
            </tr>
        ";


        $sql = "SELECT `access_tool`, COUNT(DISTINCT `access_user_id`),count( `access_tool` )
                    FROM $TABLETRACK_ACCESS
                    WHERE `access_tool` IS NOT NULL
                        AND `access_cours_code` = '$_cid'
                    GROUP BY `access_tool`";

        echo "<tr><td style='padding-left : 40px;padding-right : 40px;'>";
        $results = getManyResults3Col($sql);
        echo "<table  cellpadding='2' cellspacing='1' border='0'>";
        echo "<tr>
                <td class='secLine'>
                &nbsp;".get_lang('ToolTitleToolnameColumn')."&nbsp;
                </td>
                <td class='secLine'>
                &nbsp;".get_lang('ToolTitleUsersColumn')."&nbsp;
                </td>
                <td class='secLine'>
                &nbsp;".get_lang('ToolTitleCountColumn')."&nbsp;
                </td>
            </tr>";
        if (is_array($results))
        {
            for($j = 0 ; $j < count($results) ; $j++)
            {
                echo "<tr>";
                echo "<td class='content'><a href='toolaccess_details.php?tool=".$results[$j][0]."'>".get_lang($results[$j][0])."</a></td>";
                echo "<td align='right' class='content'>".$results[$j][1]."</td>";
                echo "<td align='right' class='content'>".$results[$j][2]."</td>";
                echo"</tr>";
            }

        }
        else
        {
            echo "<tr>";
            echo "<td colspan='3'><center>".get_lang('NoResult')."</center></td>";
            echo"</tr>";
        }
        echo "</table>";
        echo "</td></tr>";
    }
    else
    {
        $tempView[2] = '1';
        echo "
            <tr>
                    <td valign='top'>
                    +<font color='#0000FF'>&nbsp;&nbsp;</font><a href='".$_SERVER['PHP_SELF']."?view=".$tempView."' class='specialLink'>".get_lang('ToolsAccess')."</a>
                    </td>
            </tr>
        ";
    }

    /***************************************************************************
     *
     *		Links
     *
     ***************************************************************************/
    $tempView = $view;
    if($view[3] == '1')
    {
        $tempView[3] = '0';
        echo "
            <tr>
                    <td valign='top'>
                    <font color='#0000FF'>-&nbsp;&nbsp;&nbsp;</font><b>".get_lang('LinksAccess')."</b>&nbsp;&nbsp;&nbsp;[<a href='".$_SERVER['PHP_SELF']."?view=".$tempView."'>".get_lang('Close')."</a>]
                    </td>
            </tr>
        ";

        $sql = "SELECT `cl`.`title`, `cl`.`url`,count(DISTINCT `sl`.`links_user_id`), count(`cl`.`title`)
                    FROM $TABLETRACK_LINKS AS sl, $TABLECOURSE_LINKS AS cl
                    WHERE `sl`.`links_link_id` = `cl`.`id`
                        AND `sl`.`links_cours_id` = '$_cid'
                    GROUP BY `cl`.`title`, `cl`.`url`";
        echo "<tr><td style='padding-left : 40px;padding-right : 40px;'>";
        $results = getManyResultsXCol($sql,4);
        echo "<table cellpadding='2' cellspacing='1' border='0'>";
        echo "<tr>
                <td class='secLine'>
                &nbsp;".get_lang('LinksTitleLinkColumn')."&nbsp;
                </td>
                <td class='secLine'>
                &nbsp;".get_lang('LinksTitleUsersColumn')."&nbsp;
                </td>
                <td class='secLine'>
                &nbsp;".get_lang('LinksTitleCountColumn')."&nbsp;
                </td>
            </tr>";
        if (is_array($results))
        {
            for($j = 0 ; $j < count($results) ; $j++)
            {
                    echo "<tr>";
                    echo "<td class='content'><a href='".$results[$j][1]."'>".$results[$j][0]."</a></td>";
                    echo "<td align='right' class='content'>".$results[$j][2]."</td>";
                    echo "<td align='right' class='content'>".$results[$j][3]."</td>";
                    echo"</tr>";
            }

        }
        else
        {
            echo "<tr>";
            echo "<td colspan='3'><center>".get_lang('NoResult')."</center></td>";
            echo"</tr>";
        }
        echo "</table>";
        echo "</td></tr>";
    }
    else
    {
        $tempView[3] = '1';
        echo "
            <tr>
                    <td valign='top'>
                    +<font color='#0000FF'>&nbsp;&nbsp;</font><a href='".$_SERVER['PHP_SELF']."?view=".$tempView."' class='specialLink'>".get_lang('LinksAccess')."</a>
                    </td>
            </tr>
        ";
    }

    /***************************************************************************
     *
     *		Documents
     *
     ***************************************************************************/
    $tempView = $view;
    if($view[4] == '1')
    {
        $tempView[4] = '0';
        echo "
            <tr>
                    <td valign='top'>
                    <font color='#0000FF'>-&nbsp;&nbsp;&nbsp;</font><b>".get_lang('DocumentsAccess')."</b>&nbsp;&nbsp;&nbsp;[<a href='".$_SERVER['PHP_SELF']."?view=".$tempView."'>".get_lang('Close')."</a>]
                    </td>
            </tr>
        ";

        $sql = "SELECT `down_doc_path`, COUNT(DISTINCT `down_user_id`), COUNT(`down_doc_path`)
                    FROM $TABLETRACK_DOWNLOADS
                    WHERE `down_cours_id` = '$_cid'
                    GROUP BY `down_doc_path`";

        echo "<tr><td style='padding-left : 40px;padding-right : 40px;'>";
        $results = getManyResults3Col($sql);
        echo "<table cellpadding='2' cellspacing='1' border='0'>";
        echo "<tr>
                <td class='secLine'>
                &nbsp;".get_lang('DocumentsTitleDocumentColumn')."&nbsp;
                </td>
                <td class='secLine'>
                &nbsp;".get_lang('DocumentsTitleUsersColumn')."&nbsp;
                </td>
                <td class='secLine'>
                &nbsp;".get_lang('DocumentsTitleCountColumn')."&nbsp;
                </td>
            </tr>";
        if (is_array($results))
        {
            for($j = 0 ; $j < count($results) ; $j++)
            {
                    echo "<tr>";
                    echo "<td class='content'>".$results[$j][0]."</td>";
                    echo "<td align='right' class='content'>".$results[$j][1]."</td>";
                    echo "<td align='right' class='content'>".$results[$j][2]."</td>";
                    echo"</tr>";
            }

        }
        else
        {
            echo "<tr>";
            echo "<td colspan='3'><center>".get_lang('NoResult')."</center></td>";
            echo"</tr>";
        }
        echo "</table>";
        echo "</td></tr>";
    }
    else
    {
        $tempView[4] = '1';
        echo "
            <tr>
                    <td valign='top'>
                    +<font color='#0000FF'>&nbsp;&nbsp;</font><a href='".$_SERVER['PHP_SELF']."?view=".$tempView."' class='specialLink'>".get_lang('DocumentsAccess')."</a>
                    </td>
            </tr>
        ";
    }
    /***************************************************************************
     *
     *		Scorm contents and Learning Path
     *
     ***************************************************************************/
    $tempView = $view;
    if($view[5] == '1')
    {
        $tempView[5] = '0';
        echo "
            <tr>
                    <td valign='top'>
                    <font color='#0000FF'>-&nbsp;&nbsp;&nbsp;</font><b>".get_lang('ScormAccess')."</b>&nbsp;&nbsp;&nbsp;[<a href='".$_SERVER['PHP_SELF']."?view=".$tempView."'>".get_lang('Close')."</a>]
                    </td>
            </tr>
        ";

        $sql = "SELECT contentTitle, contentId
					FROM $table_scormmain
                    WHERE dokeosCourse='$_cid'";
		$result=api_sql_query($sql,__FILE__,__LINE__);
	    $ar=mysql_fetch_array($result);

		echo "<tr><td style='padding-left : 40px;padding-right : 40px;'>";
        echo "<table cellpadding='2' cellspacing='1' border='0'><tr>
				                <td class='secLine'>
								&nbsp;".get_lang('ScormContentColumn')."&nbsp;
				                </td>
				                <td class='secLine'>
				                &nbsp;".get_lang('ScormStudentColumn')."&nbsp;
				                </td>
		</tr>";
        if (is_array($ar))
        {
			while ($ar != '') {
				echo "<tr><td>";
				echo "<a href='".$_SERVER['PHP_SELF']."?view=".$view."&scormcontopen=".$ar['contentId']."' class='specialLink'>{$ar['contentTitle']}</a>";
				echo "</td></tr>";
				if ($ar['contentId']==$scormcontopen) { //have to list the students here
					$contentId=$ar['contentId'];
					$sql2 = "SELECT studentId, lastname, firstname
							FROM $table_scormdata sd, $table_user u
		                    WHERE (contentId='$contentId' and u.user_id=sd.studentId) group by studentId";
					$result2=api_sql_query($sql2);
				    $ar2=mysql_fetch_array($result2);
					while ($ar2 != '') {
						echo "<tr><td>&nbsp;&nbsp;&nbsp;</td><td>";
						echo "<a href='".$_SERVER['PHP_SELF']."?view=".$view."&scormcontopen=".$ar['contentId']."&scormstudentopen=".$ar2['studentId']."' class='specialLink'>{$ar2['lastname']} {$ar2['firstname']}</a>";
						echo "</td></tr>";

						if ($ar2['studentId']==$scormstudentopen) { //have to list the student's results
							$studentId=$ar2['studentId'];
							$sql3 = "SELECT status, score, scoTitle, time
									FROM $table_scormdata
				                    WHERE (studentId='$studentId' and contentId='$contentId') ORDER BY scoId";
							$result3=api_sql_query($sql3);
						    $ar3=mysql_fetch_array($result3);
					        echo "<tr><td>&nbsp;&nbsp;&nbsp;</td>
				                <td class='secLine'>
				                &nbsp;".get_lang('ScormTitleColumn')."&nbsp;
				                </td>
				                <td class='secLine'>
				                &nbsp;".get_lang('ScormStatusColumn')."&nbsp;
				                </td>
				                <td class='secLine'>
				                &nbsp;".get_lang('ScormScoreColumn')."&nbsp;
				                </td>
				                <td class='secLine'>
				                &nbsp;".get_lang('ScormTimeColumn')."&nbsp;
				                </td>
					            </tr>";
							while ($ar3 != '') {
								echo "<tr><td>&nbsp;&nbsp;&nbsp;</td><td>";
								echo "{$ar3['scoTitle']}</td><td align=right>{$ar3['status']}</td><td align=right>{$ar3['score']}</td><td align=right>{$ar3['time']}</td>";
								echo "</tr>";
								$ar3=mysql_fetch_array($result3);
							}
						}


						$ar2=mysql_fetch_array($result2);
					}

				}

				$ar=mysql_fetch_array($result);
			}

        }
        else
        {
			$noscorm=true;
        }

		$sql = "SELECT * FROM $tbl_learnpath_main";
    	$result=api_sql_query($sql,__FILE__,__LINE__);
        $ar=mysql_fetch_array($result);
		$table_user = Database::get_main_table(MAIN_USER_TABLE);
        if (is_array($ar))
        {
    			while ($ar != '') {
    				echo "<tr><td>";
    				echo "<a href='".$_SERVER['PHP_SELF']."?view=".$view."&pathopen=".$ar['learnpath_id']."' class='specialLink'>{$ar['learnpath_name']}</a>";
    				echo "</td></tr>";
    				if ($ar['learnpath_id']==$pathopen) { //have to list the students here

						$learnpath_id=$ar['learnpath_id'];
						$sql2 = "SELECT lu.user_id, lastname, firstname
							FROM $tbl_learnpath_user lu, $table_user u
		                    WHERE (lu.learnpath_id='$learnpath_id' and u.user_id=learnpath_user.user_id) group by lu.user_id";
						$result2=api_sql_query($sql2);
					    $ar2=mysql_fetch_array($result2);
                        if (!is_array($ar2)) {
			                        echo "<tr>";
				                    echo "<td colspan='3'><center>".get_lang('PathNeverOpenedByAnybody')."</center></td>";
					                echo"</tr>";
						}
						while ($ar2 != '') {
							echo "<tr><td>&nbsp;&nbsp;&nbsp;</td><td>";
							echo "<a 	href='".$_SERVER['PHP_SELF']."?view=".$view."&pathopen=".$ar['learnpath_id']."&pathstudentopen=".$ar2['user_id']."' class='specialLink'>{$ar2['lastname']} {$ar2['firstname']}</a>";
							echo "</td></tr>";

							if ($ar2['user_id']==$pathstudentopen) { //have to list the student's results

	 							$sql3 = "SELECT status
    									FROM $tbl_learnpath_user
    				                    WHERE (user_id='$pathstudentopen' and learnpath_id='$learnpath_id')";
	   							$result3=api_sql_query($sql3);
	   						    $ar3=mysql_fetch_array($result3);
	                            if (is_array($ar3)) {
	                                echo "<tr><td>&nbsp;&nbsp;&nbsp;</td>
	       				                <td class='secLine'>
	       				                &nbsp;".get_lang('ScormTitleColumn')."&nbsp;
	       				                </td>
	       				                <td class='secLine'>
	       				                &nbsp;".get_lang('ScormStatusColumn')."&nbsp;
	       				                </td>
	       				                <td class='secLine'>
	       				                &nbsp;".get_lang('ScormScoreColumn')."&nbsp;
	       				                </td>
	       					            </tr>";

									$sql2="SELECT * FROM $tbl_learnpath_chapter WHERE (learnpath_id=$learnpath_id) ORDER BY display_order";
									$result2=api_sql_query($sql2);
									while ($row2=mysql_fetch_array($result2)) {
										$id=$row2['id'];
										$sql3="SELECT * FROM $tbl_learnpath_item WHERE (chapter_id=$id) ORDER BY display_order";
										$result3=api_sql_query($sql3);

										echo "<tr><td></td><td colspan=3>&nbsp;{$row2['chapter_name']}</td></tr>";


										while ($row3=mysql_fetch_array($result3)) {
											$sql0 = "SELECT * FROM $tbl_learnpath_user WHERE (user_id='".$pathstudentopen."' and 		learnpath_item_id='".$row3['id']."' and learnpath_id='".$learnpath_id."')";
											$result0=api_sql_query($sql0);
											$row0=mysql_fetch_array($result0);

											echo "<tr><td></td><td>&nbsp;&nbsp;&nbsp;";
											display_addedresource_link_in_learnpath($row3['item_type'], $row3['item_id'], '', 	$row3['id'],'builder','');
											echo "</td><td>{$row0['status']}</td><td align=right>{$row0['score']}</td></tr>";

										}
									}

		                        } else {
			                        echo "<tr>";
				                    echo "<td colspan='3'><center>".get_lang('ScormNeverOpened')."</center></td>";
					                echo"</tr>";
						        }
							}
							$ar2=mysql_fetch_array($result2);
						}
   					}
		    		$ar=mysql_fetch_array($result);
    			}

        }
        else
        {

				$nopath=true;
        }

		if (($noscorm) and ($nopath)) {
               echo "<tr>";
               echo "<td colspan='3'><center>".get_lang('NoResult')."</center></td>";
               echo"</tr>";
		}


		echo "</table>";
        echo "</td></tr>";
    }
    else
    {
        $tempView[5] = '1';
        echo "
            <tr>
                    <td valign='top'>
                    +<font color='#0000FF'>&nbsp;&nbsp;</font><a href='".$_SERVER['PHP_SELF']."?view=".$tempView."' class='specialLink'>".get_lang('ScormAccess')."</a>
                    </td>
            </tr>
        ";
    }
}
// not allowed
else
{
    if(!$is_trackingEnabled)
    {
        echo get_lang('TrackingDisabled');
    }
    else
    {
        api_not_allowed();
    }
}



?>
</table>

<?php
Display::display_footer();
?>
