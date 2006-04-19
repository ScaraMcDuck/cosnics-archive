<?php
header('Content-Type: text/plain');
require_once dirname(__FILE__).'/../lib/learningobject.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
$dataManager = RepositoryDataManager :: get_instance();
$connection = $dataManager->get_connection();
$query = 'SELECT '.$dataManager->escape_column_name(LearningObject :: PROPERTY_ID).' FROM '.$dataManager->escape_table_name('learning_object').' WHERE '.$dataManager->escape_column_name(LearningObject :: PROPERTY_OWNER_ID).'='.$_GET['owner'];
$sth = $connection->prepare($query);
$res = & $connection->execute($sth, $params);
while ($record = $res->fetchRow(DB_FETCHMODE_ORDERED)) {
	echo $record[0] . "\n";
}
?>