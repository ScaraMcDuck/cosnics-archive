<?php
$cidReset = true;
$this_section = 'myrepository';
require_once dirname(__FILE__).'/main/inc/global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/repository/lib/repository_manager/repositorymanager.class.php';
require_once dirname(__FILE__).'/users/lib/usermanager/usermanager.class.php';
require_once dirname(__FILE__).'/common/translation/translation.class.php';

$trans = Translation :: get_instance();

$trans->use_lang_files('test');

echo $trans->get_translation('ok', 'general') . '<br />';
echo $trans->get_translation('language', 'test');
?>