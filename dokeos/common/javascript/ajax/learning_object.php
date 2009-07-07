<?php
require_once dirname(__FILE__) . '/../../global.inc.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

$object = Request :: post('object');
$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($object);

echo $object->get_title();

?>