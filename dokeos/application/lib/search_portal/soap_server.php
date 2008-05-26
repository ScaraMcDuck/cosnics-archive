<?php
require_once dirname(__FILE__).'/../../../common/global.inc.php';
require_once dirname(__FILE__).'/search_source/web_service/learning_object_soap_search_server.class.php';

$server = new LearningObjectSoapSearchServer();
$server->run();
?>