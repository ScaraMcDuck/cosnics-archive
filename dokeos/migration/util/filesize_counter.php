<?php
require_once(dirname(__FILE__) . '/../common/configuration/configuration.class.php');
ini_set('include_path',realpath(dirname(__FILE__).'/../plugin/pear'));
require_once dirname(__FILE__).'/../common/global.inc.php';

$conf = Configuration :: get_instance();

$path = '/var/www/html/bron/courses2/';
$files = Filesystem :: get_directory_content($path, Filesystem :: LIST_FILES, true);

foreach($files as $file)
{
	echo($file);
}

?>
