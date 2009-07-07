<?php
require_once dirname(__FILE__) . '/../../global.inc.php';

$application = $_POST['application'];
$string = $_POST['string'];

Translation :: set_application($application);
echo Translation :: get($string);
?>
