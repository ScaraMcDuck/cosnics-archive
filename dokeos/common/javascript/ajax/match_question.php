<?php
require_once dirname(__FILE__) . '/../../global.inc.php';

$value = Request :: post('value');
$action = Request :: post('action');

switch ($action)
{
    case 'skip_match' :
        $_SESSION['match_skip_options'][] = $value;
}
?>