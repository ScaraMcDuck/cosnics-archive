<?php
require_once dirname(__FILE__).'/../../global.inc.php';

$variable = Request :: post('variable');
$value = Request :: post('value');
$action = Request :: post('action');

switch($action)
{
	case 'set': $_SESSION[$variable] = $value; break;
	case 'get': echo $_SESSION[$variable]; break;
	case 'clear': unset($_SESSION[$variable]); break;
	default:
		echo $_SESSION[$variable]; break;
}

?>