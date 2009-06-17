<?php
require_once dirname(__FILE__).'/../../global.inc.php';

$value = Request :: post('value');
$action = Request :: post('action');

switch($action)
{
	case 'skip_option':
		$_SESSION['ordering_skip_options'][] = $value;
}
?>