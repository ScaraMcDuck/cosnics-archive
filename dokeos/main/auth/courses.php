<?php // $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	@package dokeos.auth
*	@todo check if unsubscribing from a course WITH group memberships works as it should
*	@todo constants are in uppercase, variables aren't
==============================================================================
*/

/*
==============================================================================
		INIT SECTION
==============================================================================
*/
// including the relevant language file
api_use_lang_files("courses");

// including the global file
include('../inc/claro_init_global.inc.php');
$this_section=SECTION_COURSES;

api_block_anonymous_users();

/*
-----------------------------------------------------------
	Libraries
-----------------------------------------------------------
*/
include_once(api_get_library_path() . "/debug.lib.inc.php");
include_once(api_get_library_path() . "/course.lib.php");

/*
-----------------------------------------------------------
	Variables
-----------------------------------------------------------
*/
$tbl_course             = Database::get_main_table(MAIN_COURSE_TABLE);
$tbl_courses_nodes      = Database::get_main_table(MAIN_CATEGORY_TABLE);
$tbl_courseUser         = Database::get_main_table(MAIN_COURSE_USER_TABLE);
$tbl_user               = Database::get_main_table(MAIN_USER_TABLE);

/*
-----------------------------------------------------------
	Header
-----------------------------------------------------------
*/

// title of the page
$nameTools= get_lang("CourseManagement");

// the breadcrumbs
//$interbredcrump[]=array("url" => $_SERVER['PHP_SELF'],"name" => get_lang("CourseManagement"));

Display::display_header($nameTools);
api_display_tool_title($nameTools);

/*
==============================================================================
		COMMANDS SECTION
==============================================================================
*/
$userId = $_uid;
unset($message);

// we are moving a course of the user up/down the list (=Sort My Courses)
if (isset($_GET['move']))
{
	$message=move_course($_GET['move'], $_GET['course']);
}

// we are moving the course of the user to a different user defined course category (=Sort My Courses)
if (isset($_POST['submit_edit_course_category']))
{
	$message=store_editcoursecategory($_POST['course_2_edit_category'], $_POST['course_categories']);
}

// we are creating a new user defined course category (= Create Course Category)
if (isset($_POST['create_course_category']) AND isset($_POST['title_course_category']) && strlen(trim($_POST['title_course_category'])) > 0)
{
	$message=store_course_category();
}

// we are subcribing to a course (=Subscribe to course)
if (isset($_POST['subscribe']))
{
	$message = subscribe_user($_POST['subscribe']);
}

// we are unsubscribing from a course (=Unsubscribe from course)
if (isset($_POST['unsubscribe']))
{
	$message=remove_user_from_course($_uid, $_POST['unsubscribe']);
}

// we are displaying any result messages;
if (isset($message))
{
	Display::display_normal_message($message);
}

/*
==============================================================================
					DISPLAY SECTION
==============================================================================
*/
// The menu with the different options in the course management
echo "<div>\n";
echo "\t<ul>\n";
echo "\t\t<li><a href=\"".$_SERVER['PHP_SELF']."?action=sortmycourses\">".get_lang("SortMyCourses")."</a></li>\n";
echo "\t\t<li><a href=\"".$_SERVER['PHP_SELF']."?action=createcoursecategory\">".get_lang("CreateCourseCategory")."</a></li>\n";
echo "\t\t<li><a href=\"".$_SERVER['PHP_SELF']."?action=subscribe\">".get_lang("SubscribeToCourse")."</a></li>\n";
echo "\t\t<li><a href=\"".$_SERVER['PHP_SELF']."?action=unsubscribe\">".get_lang("UnsubscribeFromCourse")."</a></li>\n";
echo "\t</ul>\n";
echo "</div>";

echo "<div>";
switch ($_GET["action"])
{
	case "sortmycourses":
		api_display_tool_title(get_lang("SortMyCourses"));
		$user_courses=get_courses_of_user($_uid);
		display_courses($_uid, "sorting", $user_courses);
		break;
	case "subscribe":
		api_display_tool_title(get_lang("SubscribeToCourse"));
		courses_subscribing();
		break;
	case "unsubscribe":
		api_display_tool_title(get_lang("UnsubscribeFromCourse"));
		$user_courses=get_courses_of_user($_uid);
		display_courses($_uid, "deleting", $user_courses);
		break;
	case "createcoursecategory":
		api_display_tool_title(get_lang("CreateCourseCategory"));
		display_create_course_category_form();
		break;
	case "deletecoursecategory";
		if (isset($_GET['id']))
			{ delete_course_category($_GET['id']);}
		break;
}
echo "</div>";
Display :: display_footer();

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/
 /** 	 
  * Subscribe the user to a given course 	 
  * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University 	 
  * @param string $course_code the code of the course the user wants to subscribe to 	 
  * @return string we return the message that is displayed when the action is succesfull 	 
 */ 	 
function subscribe_user($course_code) 	 
{ 	 
	global $_uid; 	 
	if (CourseManager::add_user_to_course($_uid, $course_code))
	{
		return get_lang('EnrollToCourseSuccessful'); 	 
	}
	else 
	{
		return get_lang('ErrorContactPlatformAdmin');
	}
}
/**
 * unsubscribe the user from a given course
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param int $user_id The user id of the user that is performing the unsubscribe action
 * @param string $course_code the course code of the course the user wants to unsubscribe from
 * @return string we return the message that is displayed when the action is succesfull
*/
function remove_user_from_course($user_id, $course_code)
{
	$tbl_course_user         = Database::get_main_table(MAIN_COURSE_USER_TABLE);

	// we check (once again) if the user is not course administrator
	// because the course administrator cannot unsubscribe himself
	// (s)he can only delete the course
	$sql_check="SELECT * FROM $tbl_course_user WHERE user_id='".$user_id."' AND course_code='".$course_code."' AND status='1'";
	$result_check=api_sql_query($sql_check);
	$number_of_rows=mysql_num_rows($result_check);

	if ($number_of_rows>0)
	{return false;}
	else
	{
		$sql_delete="DELETE FROM $tbl_course_user WHERE user_id='".$user_id."' AND course_code='".$course_code."'";
		$result_delete=api_sql_query($sql_delete);
		$removed_from_groups=remove_user_from_course_groups($user_id, $course_code);
		if ($removed_from_groups)
		{ return get_lang("YouAreNowUnsubscribed"); }

	}
}


/**
 * unsubscribe the user from all the groups in a given course
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param int $user_id The user id of the user that is performing the unsubscribe action
 * @param string $course_code the course code of the course the user wants to unsubscribe from
 * @return string we return the message that is displayed when the action is succesfull
*/
function remove_user_from_course_groups($user_id, $course_code)
{
	// step 1: we look up the database name of the course
	$tbl_course         = Database::get_main_table(MAIN_COURSE_TABLE);
	$sql_findcourse="SELECT * FROM $tbl_course WHERE code='".$course_code."'";
	$result_findcourse=api_sql_query($sql_findcourse);
	$course=mysql_fetch_array($result_findcourse);

	// step 2: we delete the user from the groups
	$tbl_coursegroups=Database::get_course_group_user_table($course['db_name']);
	$sql_delete="DELETE FROM $tbl_coursegroups WHERE user_id='".$user_id."'";
	api_sql_query($sql_delete,__FILE__,__LINE__);
	return true;
}

/**
 * handles the display of the courses to which the user can subscribe
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function courses_subscribing()
{
	browse_courses();
	display_search_courses();
}

/**
 * allows you to browse through the course categories (faculties) and subscribe to the courses of
 * this category (faculty)
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
*/
function browse_courses()
{
	browse_course_categories();
	if (!isset($_POST['search_course']))
	{
		display_courses_in_category();
	}
}

/**
 * counts the number of courses in a given course category
*/
function count_courses_in_category($category)
{
	$tbl_course         = Database::get_main_table(MAIN_COURSE_TABLE);
	$sql="SELECT * FROM $tbl_course WHERE category_code".(empty($category)?" IS NULL":"='".$category."'");
	$result=api_sql_query($sql);
	return mysql_num_rows($result);
}

/**
 * displays the browsing of the course categories (faculties)
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @return HTML code containing a list with all the categories and subcategories and the navigation to go one category up(if needed)
*/
function browse_course_categories()
{
	$tbl_courses_nodes      = Database::get_main_table(MAIN_CATEGORY_TABLE);

	echo "<p><b>".get_lang('CourseCategories')."</b>";

	$sql= "SELECT * FROM $tbl_courses_nodes WHERE parent_id ".(empty($_GET['category'])?"IS NULL":"='".$_GET['category']."'")." GROUP BY code, parent_id  ORDER BY tree_pos ASC";
	$result=mysql_query($sql);
	echo "<ul>";
	while ($row=mysql_fetch_array($result))
	{
		if ($row['children_count'] > 0 OR count_courses_in_category($row['code'])>0)
		{
			echo	"<li><a href=\"".$_SERVER['PHP_SELF']."?action=subscribe&amp;category=".$row['code']."&amp;up=".$_GET['category']."\">".$row['name']."</a>".
				" (".count_courses_in_category($row['code']).")</li>";
		}
		elseif ($row['nbChilds'] > 0)
		{
			echo	"<li><a href=\"".$_SERVER['PHP_SELF']."?action=subscribe&amp;category=".$row['code']."&amp;up=".$_GET['category']."\">".$row['name']."</a></li>";
		}
		else
		{
			echo "<li>".$row['name']."</li>";
		}

	}
	echo "</ul>";
	if ($_GET['category'])
	{
		echo "<a href=\"".$_SERVER['PHP_SELF']."?action=subscribe&amp;category=".$_GET['up']."\">&lt; ".get_lang('UpOneCategory')."</a>";
	}
}

/**
 * Display all the courses in the given course category. I could have used a parameter here
 * but instead I use the already available $_GET['category']
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @return HTML code: a table with all the courses in a given category (title, code, tutor) and a subscription icon if applicable)
*/
function display_courses_in_category()
{
	$category = $_GET['category'];
	$tbl_course         = Database::get_main_table(MAIN_COURSE_TABLE);

	echo "<p><b>".get_lang('CoursesInCategory')."</b>";

	$sql="SELECT * FROM $tbl_course WHERE category_code".(empty($category)?" IS NULL":"='".$_GET['category']."'");
	$result=api_sql_query($sql);
	while ($row=mysql_fetch_array($result))
		{
		$courses[]=array("code" => $row['code'], "directory" => $row['directory'], "db"=> $row['db_name'], "visual_code" => $row['visual_code'], "title" => $row['title'], "tutor" => $row['tutor_name'], "subscribe" => $row['subscribe'], "unsubscribe" => $row['unsubscribe']);
		}
	display_courses($_uid, "subscribing", $courses);
}

/**
 * displays the form for searching for a course and the results if a query has been submitted.
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @return HTML code: the form for searching for a course
*/
function display_search_courses()
{
	global $_uid;
	echo "<p><b>".get_lang("SearchCourse")."</b><br/>";
	echo "<form class=\"course_list\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?action=subscribe\">",
					"<input type=\"hidden\" name=\"search_course\" value=\"1\"/>",
					"<input type=\"text\" name=\"search_term\" />",
					"&nbsp;<input type=\"submit\" value=\"",get_lang("_search"),"\" />",
					"</form>";
	if (isset($_POST['search_course']))
	{
		echo "<p><b>".get_lang("SearchResultsFor")." ".$_POST['search_term']."</b><br/>";
		$result_search_courses_array=search_courses($_POST['search_term']);
		display_courses($_uid, "subscribing", $result_search_courses_array);
	}
}

/**
 * This function filters dangerous stuff out. It does addslashes when the php.ini setting
 * magic_quotes is set to off.
 * @author Olivier Cauberghe <olivier.cauberghe@UGent.be>, Ghent University
 * @param string something that possibly needs addslashing
 * @return string the addslahed version of the input
*/
function escape($s)
{
    if(!get_magic_quotes_gpc())
    return addslashes($s);
    else return $s;
}

/**
 * Search the courses database for a course that matches the search term.
 * The search is done on the code, title and tutor field of the course table.
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param string $search_term: the string that the user submitted, what we are looking for
 * @return array an array containing a list of all the courses (the code, directory, dabase, visual_code, title, ... )
 * 			matching the the search term.
*/
function search_courses($search_term)
{
	$TABLECOURS = Database::get_main_table(MAIN_COURSE_TABLE);

	$search_term_safe=escape($search_term);

	$sql_find="SELECT * FROM $TABLECOURS WHERE code LIKE '%".$search_term_safe."%' OR title LIKE '%".$search_term_safe."%' OR tutor_name LIKE '%".$search_term_safe."%'";
	$result_find=mysql_query($sql_find) or die(mysql_error());

	while ($row=mysql_fetch_array($result_find))
		{
		$courses[]=array("code" => $row['code'], "directory" => $row['directory'], "db"=> $row['db_name'], "visual_code" => $row['visual_code'], "title" => $row['title'], "tutor" => $row['tutor_name'], "subscribe" => $row['subscribe'], "unsubscribe" => $row['unsubscribe']);
		}
	return $courses;
}

/**
 * deletes a course category and moves all the courses that were in this category to main category
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param int $id: the id of the user_course_category
 * @return string a language variable saying that the deletion went OK.
*/
function delete_course_category($id)
{
	global $_uid;

	$DATABASE_USER_TOOLS = Database::get_user_personal_database();
	$TABLE_USER_COURSE_CATEGORY = $DATABASE_USER_TOOLS.".user_course_category";
	$TABLECOURSUSER=Database::get_main_table(MAIN_COURSE_USER_TABLE);

	$sql_delete="DELETE FROM $TABLE_USER_COURSE_CATEGORY WHERE id='".$id."' and user_id='".$_uid."'";
	$sql_update="UPDATE $TABLECOURSUSER SET user_course_cat='0' WHERE user_course_cat='".$id."' AND user_id='".$_uid."'";
	mysql_query($sql_delete) or die(mysql_error());
	mysql_query($sql_update) or die(mysql_error());

	return get_lang("CourseCategoryDeleted");
}

/**
 * stores the user course category in the dokeos_user database
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @return string a language variable saying that the user course category was stored
*/
function store_course_category()
{
	global $_uid;

	$DATABASE_USER_TOOLS = Database::get_user_personal_database();
	$TABLE_USER_COURSE_CATEGORY = $DATABASE_USER_TOOLS.".user_course_category";

	$sql_insert="INSERT INTO $TABLE_USER_COURSE_CATEGORY (user_id, title) VALUES ('".$_uid."', '".addslashes($_POST['title_course_category'])."')";
	api_sql_query($sql_insert);
	return get_lang("CourseCategoryStored");
}


/**
 * displays the form that is needed to create a course category.
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @return HTML the form (input field + submit button) to create a user course category
*/
function display_create_course_category_form()
{
	global $_uid;

	echo "<form name=\"create_course_category\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."?action=sortmycourses\">\n";
	echo "<input type=\"text\" name=\"title_course_category\"/>\n";
	echo "<input type=\"submit\" name=\"create_course_category\" value=\"".get_lang("Ok")."\"/>\n";
	echo "</form>\n";

	echo get_lang("ExistingCourseCategories");
	$DATABASE_USER_TOOLS = Database::get_user_personal_database();
	$TABLE_USER_COURSE_CATEGORY = $DATABASE_USER_TOOLS.".user_course_category";
	$sql="SELECT * FROM $TABLE_USER_COURSE_CATEGORY WHERE user_id='".$_uid."'";
	$result=api_sql_query($sql, __LINE__, __FILE__);
	echo "<ul>\n";
	while ($row=mysql_fetch_array($result))
	{
		echo "\t<li>".$row['title']."</li>\n";
	}
	echo "</ul>\n";
}

/**
 * stores the changes in a course category (moving a course to a different course category)
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param string $course_code : the course_code of the course we are moving
 *		  int $newcategory : the id of the user course category we are moving the course to.
 * @return string a language variable saying that the course was moved.
*/
function store_editcoursecategory($course_code, $newcategory)
{
	global $_uid;
	$TABLECOURSUSER = Database::get_main_table(MAIN_COURSE_USER_TABLE);

	$max_sort_value=api_max_sort_value($newcategory, $_uid);

	$sql="UPDATE $TABLECOURSUSER SET user_course_cat='".$newcategory."', sort='".($max_sort_value+1)."' WHERE course_code='".$course_code."' AND user_id='".$_uid."'";
	$result=api_sql_query($sql);
	return get_lang("EditCourseCategorySucces");
}

/**
 * moves the course one place up or down
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param string $direction : the direction we are moving the course to (up or down)
 *		  string $course2move : the course we are moving
 * @return string a language variable saying that the course was moved.
*/
function move_course($direction, $course2move)
{
	global $_uid;
	$TABLECOURSUSER = Database::get_main_table(MAIN_COURSE_USER_TABLE);

	$user_courses=get_courses_of_user($_uid);

	foreach ($user_courses as $key=>$course)
	{
		if ($course2move==$course['code'])
		{
			// source_course is the course where we clicked the up or down icon
			$source_course=$course;
			// target_course is the course before/after the source_course (depending on the up/down icon)
			if ($direction=="up")
				{$target_course=$user_courses[$key-1];}
			else
				{$target_course=$user_courses[$key+1];}
		} // if ($course2move==$course['code'])
	}

	$sql_update1="UPDATE $TABLECOURSUSER SET sort='".$target_course['sort']."' WHERE course_code='".$source_course['code']."' AND user_id='".$_uid."'";
	$sql_update2="UPDATE $TABLECOURSUSER SET sort='".$source_course['sort']."' WHERE course_code='".$target_course['code']."' AND user_id='".$_uid."'";
	mysql_query($sql_update2);
	mysql_query($sql_update1);
	return get_lang("CourseSortingDone");
}

/**
 * displays everything that is needed when the user wants to manage his current courses (sorting, subscribing, unsubscribing, ...)
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param int $user_id: the user_id of the current user
 *		  string $parameter: determines weither we are displaying for the sorting, subscribing or unsubscribin
 		  array $user_courses:  the courses to which the user is subscribed
 * @return html a table containing courses and the appropriate icons (sub/unsub/move)
*/
function display_courses($user_id, $parameter, $user_courses)
{
	$number_of_courses=count($user_courses);
	if ($number_of_courses==0)
		{
			return false;
		}
	$user_course_category=0;
	echo "<table cellpadding=\"4\">\n";

	foreach ($user_courses as $key=>$course)
	{
		// displaying the course group title if needed
		$user_course_category=$course['user_course_category'];
		if ($user_course_category<>0 and $user_course_category<>$prev_user_course_category)
		{
			echo "<tr><td colspan=\"2\"  class=\"user_course_category\">".get_user_course_category($user_course_category);
			echo " <a href=\"courses.php?action=deletecoursecategory&id=".$user_course_category."\">";
			if ($_GET['action']=="sortmycourses")
				{ echo "<img src=\"../img/delete.gif\" onclick=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("CourseCategoryAbout2bedeleted")))."')) return false;\"/>"; }
			echo "</a></td></tr>";

		}
		// displaying the course title, visual code and teacher/teaching staff
		echo "\t<tr>\n";
		echo "\t\t<td>\n";
		echo "<b>".$course['title']."</b><br/>";
		if (get_setting("display_coursecode_in_courselist") == "true")
		{
			echo $course['visual_code'];
		}
		if (get_setting("display_coursecode_in_courselist") == "true" && api_get_setting("display_teacher_in_courselist") == "true")
		{
			echo " - ";
		}
		if (get_setting("display_teacher_in_courselist") == "true")
		{
			echo $course['tutor'];
		}
		echo "\t\t</td>\n";
		// displaying the up/down/edit icons when we are sorting courses
		echo "\t\t<td valign=\"top\">\n";
		if ($parameter=="sorting")
		{
			display_sorting_icons($key, $number_of_courses, $course, $user_courses);
		}
		// displaying the delete icon when we are unsubscribing from courses
		if($parameter=="deleting")
		{
			display_unsubscribe_icons($course);
		}
		// display the subscribing icon when we are to courses.
		if ($parameter=="subscribing")
		{
			display_subscribe_icon($course);
		}
		echo "\t\t</td>\n";
		echo "\t</tr>\n";

		$prev_user_course_category=$user_course_category;
	}
	echo "</table>\n";
}

/**
 * gets the title of the user course category
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param int id: the id of the user defined course category
 * @return string: the name of the user defined course category
*/
function get_user_course_category($id)
{
	global $_uid;

	$DATABASE_USER_TOOLS = Database::get_user_personal_database();
	$TABLE_USER_COURSE_CATEGORY = $DATABASE_USER_TOOLS.".user_course_category";

	$sql="SELECT * FROM $TABLE_USER_COURSE_CATEGORY WHERE user_id='$_uid' AND id='$id'";
	$result=mysql_query($sql) or die(mysql_error());
	$row=mysql_fetch_array($result);
	return $row['title'];
}

/**
 * displays the subscribe icon if the subscribing is allowed and if the user is not yet
 * subscribe to this course
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param string $current_course: the course code of the course we need to display the subscribe icon for
 * @return string a subscribe icon or the text that subscribing is not allowed or the user is already subscribed
*/
function display_subscribe_icon($current_course)
{
	global $_uid;
	// getting all the courses to which the user is subscribed to
	$user_courses=get_courses_of_user($_uid);
	// we need only the course codes as these will be used to match against the courses of the category
	if ($user_courses<>"")
	{
		foreach ($user_courses as $key=>$value)
		{
			$user_coursecodes[]=$value['code'];
		}
	}
	else
	{
		$user_coursecodes=array();
	}
		// we display the icon to subscribe or the text already subscribed

		if (in_array($current_course['code'],$user_coursecodes))
		{
			echo get_lang("AlreadySubscribed");
		}
		else
		{
			if ($current_course['subscribe'] == SUBSCRIBE_ALLOWED)
			{
				echo"<form action=\"".$_SERVER["REQUEST_URI"]."\" method=\"post\"><input type=\"hidden\" name=\"subscribe\" value=\"".$current_course['code']."\" /><input type=\"image\" name=\"unsub\" src=\"../img/enroll.gif\" alt=\"".get_lang("Subscribe")."\" />".get_lang("Subscribe")."</form>";
			}
			else
			{
				echo get_lang("SubscribingNotAllowed");
			}
		}
}

/**
 * displays the subscribe icon if the subscribing is allowed and if the user is not yet
 * subscribe to this course
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param  $key:
 *		   $number_of_courses
 *		   $course
 *		   $user_courses
 * @return html a small table containing the up/down icons and the edit icon (for moving to a different user course category)
 * @todo complete the comments on this function: the parameter section
*/
function display_sorting_icons($key, $number_of_courses, $course, $user_courses)
{
	echo "<table><tr><td>";
	// the up icon
	if ($key>0 AND $user_courses[$key-1]['user_course_category']==$course['user_course_category'])
		{echo "<a href=\"courses.php?action=".$_GET['action']."&amp;move=up&amp;course=".$course['code']."\"><img src=\"../img/up.gif\" alt=\"".htmlentities(get_lang("Up"))."\" /></a>"; }
	echo "</td>";
	// the edit icon OR the edit dropdown list
	if (isset($_GET['edit']) and $course['code']==$_GET['edit'])
		{ echo "<td rowspan=\"2\" valign=\"middle\">".display_edit_course_categery_form($_GET['edit'])."</td>"; }
	else
		{ echo "<td rowspan=\"2\" valign=\"middle\"><a href=\"courses.php?action=".$_GET['action']."&amp;edit=".$course['code']."\"><img src=\"../img/edit.gif\"/></a></td>"; }

	echo "</tr><tr><td>";
	if ($key<$number_of_courses-1 AND $user_courses[$key+1]['user_course_category']==$course['user_course_category'])
		{ echo "<a href=\"courses.php?action=".$_GET['action']."&amp;move=down&amp;course=".$course['code']."\"><img src=\"../img/down.gif\" alt=\"".htmlentities(get_lang("Down"))."\"/></a>"; }
	echo "</td></tr></table>";
}

/**
 * This function displays the form (dropdown list) to move a course to a
 * different course_category (after the edit icon has been changed)
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param string $edit_course:
 * @return html a dropdown list containing all the user defined course categories and a submit button
 * @todo when editing (moving) a course inside a user defined course category to a different user defined category
 *			the dropdown list should have the current course category selected.
*/
function display_edit_course_categery_form($edit_course)
{
	global $_uid;

	$DATABASE_USER_TOOLS = Database::get_user_personal_database();
	$TABLE_USER_COURSE_CATEGORY = $DATABASE_USER_TOOLS.".user_course_category";
	$sql="SELECT * FROM $TABLE_USER_COURSE_CATEGORY WHERE user_id='".$_uid."'";
	$result=api_sql_query($sql);


	$output="<form name=\edit_course_category\" method=\"post\" action=\"courses.php?action=".$_GET['action']."\">\n";
	$output.="<input type=\"hidden\" name=\"course_2_edit_category\" value=\"".$edit_course."\"/>";
	$output.="\t<select name=\"course_categories\">\n";
	$output.="\t\t<option value=\"0\">".get_lang("NoCourseCategory")."</option>";
	while ($row=mysql_fetch_array($result))
		{$output.="\t\t<option value=\"".$row['id']."\">".$row['title']."</option>";}
	$output.="\t</select>\n";
	$output.="\t<input type=\"submit\" name=\"submit_edit_course_category\" value=\"".get_lang("Ok")."\"/>\n";
	$output.="</form>";
	return $output;
}

/**
 * This function displays the unsubscribe part which can be
 * 1. the unsubscribe link
 * 2. text: you are course admin of this course (=> unsubscription is not possible
 * 3. text: you are not allowed to unsubscribe from this course
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param array $course: the array with all the course & course_rel_user information
 * @return html a delete icon or a text that unsubscribing is not possible (course admin) or not allowed.
*/
function display_unsubscribe_icons($course)
{
	if ($course['status'] != 1)
	{
		if ($course['unsubscribe'] == 1)
			{	// changed link to submit to avoid action by the search tool indexer
				echo	"<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\" onsubmit=\"javascript:if(!confirm('".addslashes(htmlentities(get_lang("ConfirmUnsubscribeFromCourse")))."')) return false;\"><input type=\"hidden\" name=\"unsubscribe\" value=\"".$course['code']."\" /><input type=\"image\" name=\"unsub\" src=\"../img/delete.gif\" alt=\"".get_lang("_unsubscribe")."\" /></form>";
			}
		else
			{display_info_text(get_lang("UnsubscribeNotAllowed"));}
	}
	else
	{
		display_info_text(get_lang("CourseAdminUnsubscribeNotAllowed"));
	}
}

/**
 * retrieves all the courses that the user has already subscribed to
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @param int $user_id: the id of the user
 * @return array an array containing all the information of the courses of the given user
*/
function get_courses_of_user($user_id)
{
	$TABLECOURS=Database::get_main_table(MAIN_COURSE_TABLE);
	$TABLECOURSUSER=Database::get_main_table(MAIN_COURSE_USER_TABLE);

	$sql_select_courses="SELECT course.code k, course.visual_code  vc, course.subscribe subscr, course.unsubscribe unsubscr,
								course.title i, course.tutor_name t, course.db_name db, course.directory dir, course_rel_user.status status,
								course_rel_user.sort sort, course_rel_user.user_course_cat user_course_cat
		                        FROM    $TABLECOURS       course,
										$TABLECOURSUSER  course_rel_user
		                        WHERE course.code = course_rel_user.course_code
		                        AND   course_rel_user.user_id = '".$user_id."' ORDER BY course_rel_user.user_course_cat, course_rel_user.sort ASC";
	$result = api_sql_query($sql_select_courses) or die(mysql_error());
	while ($row=mysql_fetch_array($result))
	{
		// we only need the database name of the course
		$courses[]=array("db"=> $row['db'], "code" => $row['k'], "visual_code" => $row['vc'], "title" => $row['i'], "directory" => $row['dir'], "status" => $row['status'], "tutor" => $row['t'], "subscribe" => $row['subscr'], "unsubscribe" => $row['unsubscr'], "sort" => $row['sort'], "user_course_category" => $row['user_course_cat']);
	}
	return $courses;
}

/**
 * @author unknown
 * @param string $text: the text that has to be written in grey
 * @return string: the text with the grey formatting
 * @todo move this to a stylesheet
*/
function display_info_text($text)
{
	echo "<font color=\"#808080\">" . $text . "</font>\n";
}

?>