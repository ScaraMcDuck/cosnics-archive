<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_rights_path() . 'lib/role.class.php';
require_once Path :: get_library_path().'dokeos_utilities.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/not_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';

Translation :: set_application('rights');

if (Authentication :: is_valid())
{
	$conditions = array ();

	$query_condition = DokeosUtilities :: query_to_condition($_GET['query'], array(Role :: PROPERTY_NAME, Role :: PROPERTY_DESCRIPTION));
	if (isset ($query_condition))
	{
		$conditions[] = $query_condition;
	}

	if (is_array($_GET['exclude']))
	{
		$c = array ();
		foreach ($_GET['exclude'] as $id)
		{
			$c[] = new EqualityCondition(Role :: PROPERTY_ID, $id);
		}
		$conditions[] = new NotCondition(new OrCondition($c));
	}

	if(count($conditions) > 0)
	{
		$condition = new AndCondition($conditions);
	}
	else
	{
		$condition = null;
	}

	$rdm = RightsDataManager :: get_instance();
	$roles = $rdm->retrieve_roles($condition);
}
else
{
	$roles = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

if (isset($roles))
{
	dump_tree($roles);
}

echo '</tree>';

function dump_tree($roles)
{
	if (isset($roles) && $roles->size() == 0)
	{
		return;
	}

	echo '<node id="0" class="type_category unlinked" title="', Translation :: get('Roles'), '">', "\n";

	while ($role = $roles->next_result())
	{
		$value = RightsUtilities :: role_for_element_finder($role);
		echo '<leaf id="', $role->get_id(), '" class="', $value['class'], '" title="', htmlentities($value['title']), '" description="', htmlentities(isset($value['description']) && !empty($value['description']) ? $value['description'] : $value['title']), '"/>', "\n";
	}

	echo '</node>', "\n";
}

function contains_results($node, $objects)
{
	if (count($objects[$node['obj']->get_id()]))
	{
		return true;
	}
	foreach ($node['sub'] as $child)
	{
		if (contains_results($child, $objects))
		{
			return true;
		}
	}
	return false;
}
?>