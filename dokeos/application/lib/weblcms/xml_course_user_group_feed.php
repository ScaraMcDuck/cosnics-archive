<?php
/**
 * @package application.lib.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../../../common/global.inc.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_library_path() . 'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'condition/not_condition.class.php';
require_once Path :: get_library_path() . 'condition/and_condition.class.php';
require_once Path :: get_library_path() . 'condition/or_condition.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course_group/course_group.class.php';

if (Authentication :: is_valid())
{
    $course = Request :: get('course');

    if ($course)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $course = $wdm->retrieve_course($course);

        $users = array();
        $course_users = $course->get_subscribed_users();
        foreach($course_users as $course_user)
        {
            $users[] = $course_user->get_user_object();
        }

        $query = Request :: get('query');
        $exclude = Request :: get('exclude');

//    	$user_conditions = array ();
    	$group_conditions = array ();
//
    	if ($query)
    	{
    		$q = '*' . $query . '*';
//
//    		$user_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, $q);
    		$group_conditions[] = new PatternMatchCondition(CourseGroup :: PROPERTY_NAME, $q);
    	}
//
//    	if ($exclude)
//    	{
//    	    if (!is_array($exclude))
//    	    {
//    	        $exclude = array($exclude);
//    	    }
//
//    		$exclude_conditions = array ();
//    		$exclude_conditions['user'] = array();
//    		$exclude_conditions['group'] = array();
//
//    		foreach ($exclude as $id)
//    		{
//    		    $id = explode('_', $id);
//
//    		    if($id[0] == 'user')
//    		    {
//    		        $condition = new EqualityCondition(User :: PROPERTY_USER_ID, $id[1]);
//    		    }
//    		    elseif($id[0] == 'group')
//    		    {
//    		        $condition = new EqualityCondition(Group :: PROPERTY_GROUP_ID, $id[1]);
//    		    }
//
//    		    $exclude_conditions[$id[0]] = $condition;
//    		}
//
//    		$user_conditions[] = new NotCondition(new OrCondition($exclude_conditions['user']));
//    		$group_conditions[] = new NotCondition(new OrCondition($exclude_conditions['group']));
//    	}
//
    	if ($query || $exclude)
    	{
//    		$user_condition = new AndCondition($user_conditions);
    		$group_condition = new AndCondition($group_conditions);
    	}
    	else
    	{
//    		$user_conditions = null;
    		$group_condition = null;
    	}
//
//    	$udm = UserDataManager :: get_instance();
//    	$objects = $udm->retrieve_users($user_condition);
//    	while ($lo = $objects->next_result())
//    	{
//    		$users[] =$lo;
//    	}
//
    	$wdm = WeblcmsDataManager :: get_instance();
    	$grs = $wdm->retrieve_course_groups($group_condition);
    	while($group = $grs->next_result())
    	{
    		$groups[] = $group;
    	}
    }
    else
    {
        $users = array();
        $groups = array();
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($users, $groups);

echo '</tree>';

function dump_tree($users, $groups)
{
	if (contains_results($users) || contains_results($groups))
	{
		echo '<node id="user" class="type_category unlinked" title="Users">', "\n";
		foreach ($users as $lo)
		{
			echo '<leaf id="user_'. $lo->get_id(). '" class="'. 'type type_user'. '" title="'. htmlentities($lo->get_username()). '" description="'. htmlentities($lo->get_firstname()) . ' ' . htmlentities($lo->get_lastname()) . '"/>'. "\n";
		}
		echo '</node>', "\n";

		echo '<node id="group" class="type_category unlinked" title="Groups">', "\n";
		foreach ($groups as $group)
		{
			echo '<leaf id="group_'. $group->get_id(). '" class="'. 'type type_group'. '" title="'. htmlentities($group->get_name()). '" description="'. htmlentities($group->get_name()) . '"/>'. "\n";
		}
		echo '</node>', "\n";
	}
}

function contains_results($objects)
{
	if (count($objects))
	{
		return true;
	}
	return false;
}
?>