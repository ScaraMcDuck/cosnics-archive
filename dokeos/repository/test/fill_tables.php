<html>
<body>
<?php
require_once dirname(__FILE__) . '/../lib/datamanager.class.php';

$links = 1000;
$documents = 1000;

$dataManager = DataManager::get_instance();

for ($i = 0; $i < $links; $i++) {
	$link = new Link();
	$link->set_owner_id(1);
	$link->set_title(random_string(2));
	$link->set_description(random_string(8));
	$link->set_url('http://www.google.com/');
	$link->create();
}

for ($i = 0; $i < $documents; $i++) {
	$document = new Document();
	$document->set_owner_id(1);
	$document->set_title(random_string(2));
	$document->set_description(random_string(8));
	$document->set_path('/' . random_word() . '/' . random_word());
	$document->set_filename(random_word());
	$document->set_filesize(rand(1000, 10000));
	$document->create();
}

function random_string ($length) {
	$words = array();
	for ($i = 0; $i < $length; $i++) {
		$words[] = random_word();
	} 
	return implode(' ', $words);
}

function random_word () {
	$length = rand(4, 16);
	$str = '';
	for ($i = 0; $i < $length; $i++) {
		$str .= chr(rand(97, 122));
	}
	return $str;
}
?>
<p>Tables filled.</p>
</body>
</html>