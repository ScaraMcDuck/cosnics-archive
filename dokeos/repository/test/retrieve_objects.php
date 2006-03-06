<?php
require_once dirname(__FILE__) . '/../lib/datamanager.class.php';
$dataManager = DataManager::get_instance();
?>
<html>
<body>
<h1>Learning Object Retrieval Test</h1>
<h2>Single Type</h2>
<table border="1">
<tr><th>ID</th><th>Type</th><th>Title</th></tr>
<?php
$objects = $dataManager->retrieve_learning_objects(
	array('owner' => 1, 'type' => 'link'),        // WHERE $key=$value
	array('title' => 'xy', 'description' => 'a'), // WHERE $key LIKE %$value%
	array('title'),                               // ORDER BY $col
	array(true)                                   // DESC
);
foreach ($objects as $o) {
	echo '<tr><td>' . $o->get_id() . '</td><td>'
		. $o->get_type() . '</td><td>'
		. $o->get_title() . '</td></tr>';
}
?>
</table>
<h2>Any Type</h2>
<table border="1">
<tr><th>ID</th><th>Type</th><th>Title</th></tr>
<?php
$objects = $dataManager->retrieve_learning_objects(
	array('owner' => 1),                          // WHERE $key=$value
	array('title' => 'xy', 'description' => 'a'), // WHERE $key LIKE %$value%
	array('title'),                               // ORDER BY $col
	array(true)                                   // DESC
);
foreach ($objects as $o) {
	echo '<tr><td>' . $o->get_id() . '</td><td>'
		. $o->get_type() . '</td><td>'
		. $o->get_title() . '</td></tr>';
}
?>
</table>
</body>
</html>