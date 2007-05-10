<?php
/**
 * @package application.lib.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
$langFile = 'personalmessenger';
require_once dirname(__FILE__).'/../../../main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/personalmessengerdatamanager.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../../../users/lib/user.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/condition/notcondition.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/condition/orcondition.class.php';
require_once dirname(__FILE__).'/../../../users/lib/usersdatamanager.class.php';

if (api_get_user_id())
{
	$conditions = array ();
	
	if (isset($_GET['query']))
	{
		$query_condition = new LikeCondition(User :: PROPERTY_USERNAME, '%'. $_GET['query'] .'%');
		
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

	$dm = UsersDataManager :: get_instance();
	$objects = $dm->retrieve_users($condition);

	while ($lo = $objects->next_result())
	{
		$objects_by_cat[] =$lo;
	}
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($objects_by_cat);

echo '</tree>';

function dump_tree($objects)
{
	if (contains_results($objects))
	{
		echo '<node id="user" class="type_category unlinked" title="Users">', "\n";
		foreach ($objects as $lo)
		{
			echo '<leaf id="'. $lo->get_user_id(). '" class="'. 'type type_user'. '" title="'. htmlentities($lo->get_username()). '" description="'. htmlentities($lo->get_firstname()) . ' ' . htmlentities($lo->get_lastname()) . '"/>'. "\n";
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