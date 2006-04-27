<?php
require_once dirname(__FILE__).'/claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/application/lib/search_portal/soap/learningobjectsearchserver.class.php';

$server = new LearningObjectSearchServer();
$server->run();
?>