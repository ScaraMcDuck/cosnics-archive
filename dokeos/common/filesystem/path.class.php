<?php

require_once dirname(__FILE__).'/../configuration/configuration.class.php';

class Path
{
    public static function get_path($path_type)
    {
    	$conf = Configuration :: get_instance();
		switch ($path_type)
		{
			case 'WEB_PATH' :
				return $conf->get_parameter('general', 'root_web');
			case 'SYS_PATH' :
				return $conf->get_parameter('general', 'root_sys');
			case 'WEB_LIB_PATH' :
				return self :: get_path('WEB_PATH') . 'common/';
			case 'SYS_LIB_PATH' :
				return self :: get_path('SYS_PATH') . 'common/';
			case 'WEB_PLUGIN_PATH' :
				return self :: get_path('WEB_PATH') . 'plugin/';
			case 'SYS_PLUGIN_PATH' :
				return self :: get_path('SYS_PATH') . 'plugin/';
			case 'WEB_CODE_PATH' :
				return self :: get_path('WEB_PATH') . 'main/';
			case 'SYS_CODE_PATH' :
				return self :: get_path('SYS_PATH') . 'main/';
			case 'WEB_ARCHIVE_PATH' :
				return self :: get_path('WEB_PATH') . 'archive/';
			case 'SYS_ARCHIVE_PATH' :
				return self :: get_path('SYS_PATH') . 'archive/';
			case 'WEB_IMG_PATH' :
				return self :: get_path('WEB_PATH') . 'layout/img/';
			case 'SYS_IMG_PATH' :
				return self :: get_path('SYS_PATH') . 'layout/img/';
			case 'WEB_CSS_PATH' :
				return self :: get_path('WEB_PATH') . 'layout/css/';
			case 'SYS_CSS_PATH' :
				return self :: get_path('SYS_PATH') . 'layout/css/';
			default :
				return;
		}
    }
}
?>