<?php
require_once dirname(__FILE__).'/../../global.inc.php';

$path = $_POST['path'];

echo Path :: get($path);
?>
