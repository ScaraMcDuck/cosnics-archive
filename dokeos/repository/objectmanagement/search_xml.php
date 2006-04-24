<?php
require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../lib/learningobject.class.php';
require_once dirname(__FILE__).'/../lib/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../lib/condition/notcondition.class.php';
require_once dirname(__FILE__).'/../lib/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../lib/condition/orcondition.class.php';

$conditions = array();

$query_condition = RepositoryUtilities :: query_to_condition($_GET['query'], LearningObject :: PROPERTY_TITLE);
if (isset($query_condition))
{
	$conditions[] = $query_condition;
}

$owner_condition = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, api_get_user_id());
$conditions[] = $owner_condition;

$category_type_condition = new EqualityCondition(LearningObject :: PROPERTY_TYPE, 'category');
$conditions[] = new NotCondition($category_type_condition);

if (is_array($_GET['exclude'])) {
	$c = array();
	foreach ($_GET['exclude'] as $id) {
		$c[] = new EqualityCondition(LearningObject :: PROPERTY_ID, $id);
	}
	$conditions[] = new NotCondition(new OrCondition($c));
}

$condition = new AndCondition($conditions);

$dm = RepositoryDataManager :: get_instance();
$objects = $dm->retrieve_learning_objects(null, $condition, array(LearningObject :: PROPERTY_TITLE), array(SORT_ASC));

foreach ($objects as $lo)
{
	$cat = $dm->retrieve_learning_object($lo->get_parent_id());
	while ($cat->get_type() != 'category') {
		$cat = $dm->retrieve_learning_object($cat->get_parent_id());
	}
	$cid = $cat->get_id();
	if (is_array($objects_by_cat[$cid]))
	{
		array_push($objects_by_cat[$cid], $lo);
	}
	else
	{
		$objects_by_cat[$cid] = array($lo);
	}
}

$condition = new AndCondition($owner_condition, $category_type_condition);

$categories = array();
foreach ($dm->retrieve_learning_objects(null, $condition) as $cat)
{
	$parent = $cat->get_parent_id();
	if (is_array($categories[$parent]))
	{
		array_push($categories[$parent], $cat);
	}
	else 
	{
		$categories[$parent] = array($cat);
	}
}

function get_tree ($index, & $flat_tree)
{
	$tree = array();
	foreach ($flat_tree[$index] as $child)
	{
		$tree[] = array(
			'obj' => $child,
			'sub' => get_tree($child->get_id(), & $flat_tree)
		);
	} 
	return $tree;
}

$tree = get_tree(0, & $categories);

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"?>', "\n", '<results>';

function dump_tree ($tree, & $objects)
{
	if (!count($tree))
	{
		return;
	}
	foreach ($tree as $node)
	{
		if (!contains_results($node, & $objects))
		{
			continue;
		}
		$id = $node['obj']->get_id();
		echo '<node id="', $id, '" title="',
			htmlentities($node['obj']->get_title()), '">';
		dump_tree($node['sub'], & $objects);
		foreach ($objects[$id] as $lo)
		{
			echo '<leaf id="', $lo->get_id(), '" title="',
				htmlentities($lo->get_title() . ' [' . $lo->get_type() . ']'),
				'"/>';				
		}
		echo '</node>';
	}
}

function contains_results ($node, & $objects)
{
	if (count($objects[$node['obj']->get_id()]))
	{
		return true;
	}
	foreach ($node['sub'] as $child)
	{
		if (contains_results($child, & $objects))
		{
			return true;
		}
	}
	return false;
}

dump_tree($tree, & $objects_by_cat);

echo '</results>';
?>