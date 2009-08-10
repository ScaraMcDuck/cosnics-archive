<?php
/**
 * @package application.lib.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../../../common/global.inc.php';
require_once dirname(__FILE__).'/../laika_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/not_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';

if (Authentication :: is_valid())
{
	$conditions = array ();

	if (isset($_GET['query']))
	{
		$q = '*'.$_GET['query'].'*';
		$query_condition = new PatternMatchCondition(User :: PROPERTY_USERNAME, $q);

		if (isset ($query_condition))
		{
			$conditions[] = $query_condition;
		}
	}

	if (is_array($_GET['exclude']))
	{
		$c = array ();
		foreach ($_GET['exclude'] as $id)
		{
			$c[] = new EqualityCondition(User :: PROPERTY_USER_ID, $id);
		}
		$conditions[] = new NotCondition(new OrCondition($c));
	}

	if (isset($_GET['query']) || is_array($_GET['exclude']))
	{
		$condition = new AndCondition($conditions);
	}
	else
	{
		$condition = null;
	}

	$dm = UserDataManager :: get_instance();
	$objects = $dm->retrieve_users($condition);

	while ($lo = $objects->next_result())
	{
		$users[] =$lo;
	}
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($users);

echo '</tree>';

function dump_tree($users)
{
	if (contains_results($users))
	{
		echo '<nodes classes="type_category unlinked" id="recipients" title="'. Translation :: get('User') .'">';
		foreach ($users as $lo)
		{
			echo '<leaf id="user|'. $lo->get_id(). '" classes="'. 'type type_user'. '" title="'. htmlentities($lo->get_fullname()). '" description="'. htmlentities($lo->get_username()) . '"/>'. "\n";
		}
		echo '</nodes>';
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