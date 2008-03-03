<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once dirname(__FILE__).'/../lib/learningobject.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';

header('Content-Type: text/plain');

$dataManager = RepositoryDataManager :: get_instance();
$objects = $dataManager->retrieve_learning_objects();
while ($object = $objects->next_result()) {
	echo $object->get_id() . "\n";
}

?>