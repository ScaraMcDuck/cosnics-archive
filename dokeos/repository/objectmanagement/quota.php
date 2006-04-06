<?php
require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once api_get_library_path().'/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../lib/quotamanager.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../lib/categorymenu.class.php';
require_once api_get_library_path().'/fileDisplay.lib.php';
require_once api_get_library_path().'/text.lib.php';
if( !api_get_user_id())
{
	api_not_allowed();
}

function get_bar($percent)
{
	$html = '<blockquote>';
	//$html .= '<img src="'.api_get_path(WEB_CODE_PATH).'/img/bar_1m.gif" height="12"/>';
	for($i = 0; $i< 100; $i++)
	{
		if($i > $percent)
		{
			$color = '#EEEEEE';
		}
		elseif($i >= 90)
		{
			$color = '#FF0000';
		}
		elseif($i >= 80)
		{
			$color = '#FFBE0F';
		}
		else
		{
			$color =  '#51CF33';
		}
		$html .= '<span style="background-color:'.$color.';margin-right:1px;">&nbsp;</span>';
	}
	//$html .= '<img src="'.api_get_path(WEB_CODE_PATH).'/img/bar_1u.gif" width="'.(2*$percent).'" height="12"/>';
	//$html .= '<img src="'.api_get_path(WEB_CODE_PATH).'/img/bar_1r.gif" width="'.(2*(100-$percent)).'" height="12"/>';
	//$html .= '<img src="'.api_get_path(WEB_CODE_PATH).'/img/bar_1.gif" height="12"/>';
	$html .= ' '.$percent.' %';
	$html .= '</blockquote>';
	return $html;
}

// Load quotamanager
$quotamanager = new QuotaManager(api_get_user_id());
// Load datamanager
$datamanager = RepositoryDataManager::get_instance();

// Create a category-menu (for displaying correct breadcrumbs)
$root_category = $datamanager->retrieve_root_category(api_get_user_id());
$menu = new CategoryMenu(api_get_user_id(),$root_category->get_id(),'index.php?category=%s');
$interbredcrump = $menu->get_breadcrumbs();


// Display header
Display::display_header(get_lang('Quota'));
api_display_tool_title(get_lang('Quota'));
$percent = $quotamanager->get_used_disk_space_percent();
echo '<h3>'.get_lang('Disk').'</h3>';
echo format_file_size($quotamanager->get_used_disk_space()).' / '.format_file_size($quotamanager->get_max_disk_space());
echo get_bar($percent);
echo '<h3>'.get_lang('Database').'</h3>';
echo $quotamanager->get_used_database_space().' / '.$quotamanager->get_max_database_space();
$percent = $quotamanager->get_used_database_space_percent();
echo get_bar($percent);


// Display footer
Display::display_footer();
?>