<?php

/**
==============================================================================
 *	This is a simple test script that demonstrates the use of the LCMS data
 *	manager.
 * 
 *	@author Tim De Pauw
==============================================================================
 */

require_once dirname(__FILE__).'/../lib/datamanager.class.php';
$dataManager = DataManager :: get_instance();
?>
<html>
<body>
<h1>Learning Object Retrieval Test</h1>
<h2>Single Type</h2>
<?php
$type = 'link';
$condition = new AndCondition(array (
	new OrCondition(array (
		new ExactMatchCondition('owner', 1),
		new ExactMatchCondition('owner', 2)
	)), 
	new PatternMatchCondition('title', '*x?'),
	new PatternMatchCondition('description', '*yv*')
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
	array ('title'),
	// DESC
	array (SORT_DESC),
	// First
	0,
	// Max.
	10);
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
	new ExactMatchCondition('owner', 1),
	new PatternMatchCondition('title', '*xy*'),
	new PatternMatchCondition('description', '*a*')
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
	array ('title'),
	array (SORT_ASC),
	0,
	10);
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