<?php
require_once dirname(__FILE__).'/../../global.inc.php';

$type = $_POST['type'];
$output = array();

switch($type)
{
    case 'path' :
        $path = $_POST['path'];
        $output['path'] = Path :: get($path);
        break;

    case 'theme' :
        $output['theme'] = Theme :: get_theme();
        break;

    case 'translation' :
        $application = $_POST['application'];
        $string = $_POST['string'];
        Translation :: set_application($application);
        $output['translation'] = Translation :: get($string);
        break;
}

$output = (object) $output;

echo json_encode($output);
?>