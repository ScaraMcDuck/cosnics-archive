<?php

// The root paths
define('WEB_PATH', 'WEB_PATH');
define('SYS_PATH', 'SYS_PATH');
define('REL_PATH', 'REL_PATH');

// Platform-level paths
define('WEB_LIB_PATH', 'WEB_LIB_PATH');
define('SYS_LIB_PATH', 'SYS_LIB_PATH');
define('WEB_PLUGIN_PATH', 'WEB_PLUGIN_PATH');
define('SYS_PLUGIN_PATH', 'SYS_PLUGIN_PATH');
define('WEB_FILE_PATH', 'WEB_FILE_PATH');
define('SYS_FILE_PATH', 'SYS_FILE_PATH');
define('REL_FILE_PATH', 'REL_FILE_PATH');
define('WEB_LAYOUT_PATH', 'WEB_LAYOUT_PATH');
define('SYS_LAYOUT_PATH', 'SYS_LAYOUT_PATH');
define('WEB_LANG_PATH', 'WEB_LANG_PATH');
define('SYS_LANG_PATH', 'SYS_LANG_PATH');

// Some paths for the LCMS-applications
define('SYS_APP_PATH', 'SYS_LANG_PATH');
define('SYS_APP_ADM_PATH', 'SYS_APP_ADM_PATH');
define('SYS_APP_CLSS_PATH', 'SYS_APP_CLSS_PATH');
define('SYS_APP_INST_PATH', 'SYS_APP_INST_PATH');
define('SYS_APP_MIGR_PATH', 'SYS_APP_MIGR_PATH');
define('SYS_APP_REPO_PATH', 'SYS_APP_REPO_PATH');
define('SYS_APP_USER_PATH', 'SYS_APP_USER_PATH');

// Files-paths
define('WEB_ARCHIVE_PATH', 'WEB_ARCHIVE_PATH');
define('SYS_ARCHIVE_PATH', 'SYS_ARCHIVE_PATH');
define('WEB_FCK_PATH', 'WEB_FCK_PATH');
define('SYS_FCK_PATH', 'SYS_FCK_PATH');
define('WEB_GARBAGE_PATH', 'WEB_GARBAGE_PATH');
define('SYS_GARBAGE_PATH', 'SYS_GARBAGE_PATH');
define('WEB_REPO_PATH', 'WEB_REPO_PATH');
define('SYS_REPO_PATH', 'SYS_REPO_PATH');
define('WEB_TEMP_PATH', 'WEB_TEMP_PATH');
define('SYS_TEMP_PATH', 'SYS_TEMP_PATH');
define('WEB_USER_PATH', 'WEB_USER_PATH');
define('SYS_USER_PATH', 'SYS_USER_PATH');
define('WEB_FCK_PATH', 'WEB_FCK_PATH');
define('SYS_FCK_PATH', 'SYS_FCK_PATH');

// Layout-paths
define('WEB_IMG_PATH', 'WEB_IMG_PATH');
define('SYS_IMG_PATH', 'SYS_IMG_PATH');
define('WEB_CSS_PATH', 'WEB_CSS_PATH');
define('SYS_CSS_PATH', 'SYS_CSS_PATH');

class Path
{
    public static function get_path($path_type)
    {
		switch ($path_type)
		{
			case WEB_PATH :
				return Configuration :: get_instance()->get_parameter('general', 'root_web');
			case SYS_PATH :
				return realpath(dirname(__FILE__) . '/../../') . '/';
			case REL_PATH :
				$url_append = Configuration :: get_instance()->get_parameter('general', 'url_append');
				return (substr($url_append, -1) === '/' ? $url_append : $url_append.'/');
				
			// Platform-level paths
			case WEB_LIB_PATH :
				return self :: get_path(WEB_PATH) . 'common/';
			case SYS_LIB_PATH :
				return self :: get_path(SYS_PATH) . 'common/';
			case WEB_PLUGIN_PATH :
				return self :: get_path(WEB_PATH) . 'plugin/';
			case SYS_PLUGIN_PATH :
				return self :: get_path(SYS_PATH) . 'plugin/';
			case WEB_FILE_PATH :
				return self :: get_path(WEB_PATH) . 'files/';
			case SYS_FILE_PATH :
				return self :: get_path(SYS_PATH) . 'files/';
			case REL_FILE_PATH :
				return self :: get_path(REL_PATH) . 'files/';
			case WEB_LAYOUT_PATH :
				return self :: get_path(WEB_PATH) . 'layout/';
			case SYS_LAYOUT_PATH :
				return self :: get_path(SYS_PATH) . 'layout/';
			case WEB_LANG_PATH :
				return self :: get_path(WEB_PATH) . 'languages/';
			case SYS_LANG_PATH :
				return self :: get_path(SYS_PATH) . 'languages/';
				
			// Some paths for the LCMS applications
			case SYS_APP_PATH :
				return self :: get_path(SYS_PATH) . 'application/';
			case SYS_APP_ADMIN_PATH :
				return self :: get_path(SYS_PATH) . 'admin/';
			case SYS_APP_CLASSGROUP_PATH :
				return self :: get_path(SYS_PATH) . 'classgroup/';
			case SYS_APP_INSTALL_PATH :
				return self :: get_path(SYS_PATH) . 'install/';
			case SYS_APP_MIGRATION_PATH :
				return self :: get_path(SYS_PATH) . 'migration/';
			case SYS_APP_REPOSITORY_PATH :
				return self :: get_path(SYS_PATH) . 'repository/';
			case SYS_APP_USER_PATH :
				return self :: get_path(SYS_PATH) . 'users/';
			
			// Files-paths
			case WEB_ARCHIVE_PATH :
				return self :: get_path(WEB_FILE_PATH) . 'archive/';
			case SYS_ARCHIVE_PATH :
				return self :: get_path(SYS_FILE_PATH) . 'archive/';
			case WEB_TEMP_PATH :
				return self :: get_path(WEB_FILE_PATH) . 'temp/';
			case SYS_TEMP_PATH :
				return self :: get_path(SYS_FILE_PATH) . 'temp/';
			case WEB_USER_PATH :
				return self :: get_path(WEB_FILE_PATH) . 'userpictures/';
			case SYS_USER_PATH :
				return self :: get_path(SYS_FILE_PATH) . 'userpictures/';
			case WEB_FCK_PATH :
				return self :: get_path(WEB_FILE_PATH) . 'fckeditor/';
			case SYS_FCK_PATH :
				return self :: get_path(SYS_FILE_PATH) . 'fckeditor/';
			case REL_FCK_PATH :
				return self :: get_path(REL_FILE_PATH) . 'fckeditor/';
			case WEB_REPO_PATH :
				return self :: get_path(WEB_FILE_PATH) . 'repository/';
			case SYS_REPO_PATH :
				return self :: get_path(SYS_FILE_PATH) . 'repository/';
				
			// Layout-paths
			case WEB_IMG_PATH :
				return self :: get_path(WEB_LAYOUT_PATH) . 'img/';
			case SYS_IMG_PATH :
				return self :: get_path(SYS_LAYOUT_PATH) . 'img/';
			case WEB_CSS_PATH :
				return self :: get_path(WEB_LAYOUT_PATH) . 'css/';
			case SYS_CSS_PATH :
				return self :: get_path(SYS_LAYOUT_PATH) . 'css/';
				
			default :
				return;
		}
    }
    
    public static function get_library_path()
    {
    	return self :: get_path(SYS_LIB_PATH);
    }
    
    public static function get_repository_path()
    {
    	return self :: get_path(SYS_APP_REPOSITORY_PATH);
    }
    
    public static function get_user_path()
    {
    	return self :: get_path(SYS_APP_USER_PATH);
    }
}
?>