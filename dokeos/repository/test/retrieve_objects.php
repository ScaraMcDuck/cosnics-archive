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
<table border="1">
<tr><th>ID</th><th>Owner ID</th><th>Type</th><th>Title</th></tr>
<?php

$started = microtime(true);
$objects = $dataManager->retrieve_learning_objects(
	// WHERE $key=$value
	array ('owner' => array(1, 2), 'type' => 'link'),
	// WHERE $key LIKE %$value%
	array ('title' => 'xy', 'description' => 'a'),
	// ORDER BY $col
	array ('title'),
	// DESC
	array (SORT_DESC),
	// First
	0,
	// Max.
	5);
$completed = microtime(true);
$total_time = $completed - $started;

foreach ($objects as $o)
{
	echo '<tr><td>'.$o->get_id().'</td><td>'.$o->get_owner_id().'</td><td>'.$o->get_type().'</td><td>'.$o->get_title().'</td></tr>';
}
?>
</table>
<p><em>Completed in <strong><?php echo $total_time; ?></strong> seconds.</em></p>
<h2>Any Type</h2>
<table border="1">
<tr><th>ID</th><th>Owner ID</th><th>Type</th><th>Title</th></tr>
<?php
$started = microtime(true);
$objects = $dataManager->retrieve_learning_objects(
	// WHERE $key=$value
	array ('owner' => 1),
	// WHERE $key LIKE %$value%
	array ('title' => 'xy', 'description' => 'a'),
	// ORDER BY $col
	array ('title'),
	// ASC
	array (SORT_ASC),
	// First
	0,
	// Max.
	5);
$completed = microtime(true);
$total_time = $completed - $started;

foreach ($objects as $o)
{
	echo '<tr><td>'.$o->get_id().'</td><td>'.$o->get_owner_id().'</td><td>'.$o->get_type().'</td><td>'.$o->get_title().'</td></tr>';
}
?>
</table>
<p><em>Completed in <strong><?php echo $total_time; ?></strong> seconds.</em></p>
</body>
</html>