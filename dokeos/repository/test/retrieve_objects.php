<?php

/**
==============================================================================
 *	This is a simple test script that demonstrates the use of the LCMS data
 *	manager.
 * 
 *	@author Tim De Pauw
==============================================================================
 */

require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
$dataManager = RepositoryDataManager :: get_instance();
?>
<html>
<body>
<h1>Learning Object Retrieval Test</h1>
<h2>Single Type</h2>
<?php
$type = 'link';
$condition = new AndCondition(array (
	new OrCondition(array (
		new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, 1),
		new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, 2)
	)), 
	new PatternMatchCondition(LearningObject :: PROPERTY_TITLE, '*x?'),
	new PatternMatchCondition(LearningObject :: PROPERTY_DESCRIPTION, '*yv*')
));
$count = $dataManager->count_learning_objects($type, $condition);
?>
<p><em>Matching records: <strong><?php echo $count; ?></strong>.</em></p>
<table border="1">
<tr><th>ID</th><th>Owner ID</th><th>Type</th><th>Title</th></tr>
<?php
$started = microtime(true);
$objects = $dataManager->retrieve_learning_objects(
	$type,
	$condition,
	// ORDER BY $col
	array (LearningObject :: PROPERTY_TITLE),
	// DESC
	array (SORT_DESC),
	// First
	0,
	// Max.
	10)->as_array();
$completed = microtime(true);
$total_time = ($completed - $started) * 1000;

foreach ($objects as $o)
{
	echo '<tr><td>'.$o->get_id().'</td><td>'.$o->get_owner_id().'</td><td>'.$o->get_type().'</td><td>'.$o->get_title().'</td></tr>'."\n";
}
?>
</table>
<p><em>Completed in <strong><?php echo $total_time; ?></strong> milliseconds.</em></p>
<h2>Any Type</h2>
<?php
$type = null;
$condition = new AndCondition(array (
	new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, 1),
	new PatternMatchCondition(LearningObject :: PROPERTY_TITLE, '*xy*'),
	new PatternMatchCondition(LearningObject :: PROPERTY_DESCRIPTION, '*a*')
));
$count = $dataManager->count_learning_objects($type, $condition);
?>
<p><em>Matching records: <strong><?php echo $count; ?></strong>.</em></p>
<table border="1">
<tr><th>ID</th><th>Owner ID</th><th>Type</th><th>Title</th></tr>
<?php
$started = microtime(true);
$objects = $dataManager->retrieve_learning_objects(
	$type,
	$condition,
	array (LearningObject :: PROPERTY_TITLE),
	array (SORT_ASC),
	0,
	10)->as_array();
$completed = microtime(true);
$total_time = ($completed - $started) * 1000;

foreach ($objects as $o)
{
	echo '<tr><td>'.$o->get_id().'</td><td>'.$o->get_owner_id().'</td><td>'.$o->get_type().'</td><td>'.$o->get_title().'</td></tr>'."\n";
}
?>
</table>
<p><em>Completed in <strong><?php echo $total_time; ?></strong> milliseconds.</em></p>
</body>
</html>