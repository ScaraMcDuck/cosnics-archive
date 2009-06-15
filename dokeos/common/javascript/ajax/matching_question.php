<?php
require_once dirname(__FILE__).'/../../global.inc.php';

$value = Request :: post('value');
$action = Request :: post('action');

switch($action)
{
	case 'skip_option':
		$_SESSION['mq_skip_options'][] = $value; break;
	case 'skip_match':
		$_SESSION['mq_skip_matches'][] = $value; break;
}
?>