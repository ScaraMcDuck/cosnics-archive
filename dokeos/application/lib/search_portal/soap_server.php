<?php
require_once dirname(__FILE__).'/../../../main/inc/global.inc.php';
require_once dirname(__FILE__).'/search_source/web_service/learningobjectsoapsearchserver.class.php';

$server = new LearningObjectSoapSearchServer();
$server->run();
?>