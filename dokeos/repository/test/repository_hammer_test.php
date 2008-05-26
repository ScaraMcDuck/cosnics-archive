<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once dirname(__FILE__).'/../lib/learning_object.class.php';
require_once dirname(__FILE__).'/../lib/repository_data_manager.class.php';

header('Content-Type: text/plain');

$dataManager = RepositoryDataManager :: get_instance();
$objects = $dataManager->retrieve_learning_objects();
while ($object = $objects->next_result()) {
	echo $object->get_id() . "\n";
}

?>