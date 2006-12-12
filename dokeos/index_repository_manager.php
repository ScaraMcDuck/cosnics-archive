<?php
$cidReset = true;
$langFile = array('repository','agenda');
$this_section = 'myrepository';
require_once dirname(__FILE__).'/main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/main/inc/lib/text.lib.php';
require_once dirname(__FILE__).'/repository/lib/repository_manager/repositorymanager.class.php';

if (!api_get_user_id())
{
	api_not_allowed();
}

$repmgr = new RepositoryManager(api_get_user_id());
$repmgr->run();
?>