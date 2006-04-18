<?php
header('Content-Type: text/plain');
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
$dataManager = RepositoryDataManager :: get_instance();
$objects = $dataManager->retrieve_learning_objects();
foreach ($objects as $o) {
	echo $o->get_id() . "\n";
}
?>