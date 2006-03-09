<?php
require_once '../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once '../lib/quotamanager.class.php';
require_once api_get_library_path().'/text.lib.php';
if( !api_get_user_id())
{
	api_not_allowed();
}

function get_bar($percent)
{
	$html = '<blockquote>';
	$html .= '<img src="'.api_get_path(WEB_CODE_PATH).'/img/bar_1m.gif" height="12"/>';
	$html .= '<img src="'.api_get_path(WEB_CODE_PATH).'/img/bar_1u.gif" width="'.(2*$percent).'" height="12"/>';
	$html .= '<img src="'.api_get_path(WEB_CODE_PATH).'/img/bar_1r.gif" width="'.(2*(100-$percent)).'" height="12"/>';
	$html .= '<img src="'.api_get_path(WEB_CODE_PATH).'/img/bar_1.gif" height="12"/>';
	$html .= ' '.$percent.' %';
	$html .= '</blockquote>';
	return $html;
}

$quotamanager = new QuotaManager(api_get_user_id());

// Display header
Display::display_header(get_lang('Quota'));
api_display_tool_title(get_lang('Quota'));
$percent = $quotamanager->get_used_disk_space_percent();
echo '<h3>'.get_lang('Disk').'</h3>';
echo $quotamanager->get_used_disk_space().' / '.$quotamanager->get_max_disk_space();
echo get_bar($percent);
echo '<h3>'.get_lang('Database').'</h3>';
echo $quotamanager->get_used_database_space().' / '.$quotamanager->get_max_database_space();
$percent = $quotamanager->get_used_database_space_percent();
echo get_bar($percent);


// Display footer
Display::display_footer();
?>