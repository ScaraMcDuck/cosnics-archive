<?php
require_once '../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once '../lib/quotamanager.class.php';
require_once api_get_library_path().'/text.lib.php';
if( !api_get_user_id())
{
	api_not_allowed();
}

$quotamanager = new QuotaManager(api_get_user_id());

// Display header
Display::display_header(get_lang('Quota'));
api_display_tool_title(get_lang('Quota'));

echo 'DISK: ';
echo $quotamanager->get_used_disk_space();

echo '<br />DB: ';
echo $quotamanager->get_used_database_space();


// Display footer
Display::display_footer();
?>