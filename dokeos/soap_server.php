<?php
require_once dirname(__FILE__).'/claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/application/lib/search_portal/search_source/web_service/learningobjectsoapsearchserver.class.php';

$server = new LearningObjectSoapSearchServer();
$server->run();
?>