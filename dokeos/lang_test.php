<?php
$cidReset = true;
$this_section = 'myrepository';
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_repository_path(). 'lib/repository_manager/repositorymanager.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/usermanager.class.php';

$trans = Translation :: get_instance();

$trans->use_lang_files('test');

echo $trans->get_translation('ok', 'general') . '<br />';
echo $trans->get_translation('language', 'test');
?>