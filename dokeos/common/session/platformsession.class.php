<?php

class PlatformSession
{
	public static function platform_session_start($already_installed = true)
	{
		// TODO: This is not configurable during install, so why include it ?
		//global $storeSessionInDb;
		//if (is_null($storeSessionInDb))
		//{
		//	$storeSessionInDb = false;
		//}
		//if ($storeSessionInDb && function_exists('session_set_save_handler'))
		//{
		//	include_once (api_get_library_path().'/session_handler.class.php');
		//	$session_handler = new session_handler();
		//	@ session_set_save_handler(array ($session_handler, 'open'), array ($session_handler, 'close'), array ($session_handler, 'read'), array ($session_handler, 'write'), array ($session_handler, 'destroy'), array ($session_handler, 'garbage'));
		//}
		session_name('dk_sid');
		session_start();
		if ($already_installed)
		{
			if (empty ($_SESSION['checkDokeosURL']))
			{
				$_SESSION['checkDokeosURL'] = Path :: get_path(WEB_PATH);
			}
			elseif ($_SESSION['checkDokeosURL'] != Path :: get_path(WEB_PATH))
			{
				self :: platform_session_clear();
			}
		}
	}
	
	public static function platform_session_register($variable, $value)
	{
		session_register($variable);
		$_SESSION[$variable] = $value;
	}
	
	public static function platform_session_unregister($variable)
	{
		session_unregister($variable);
		$_SESSION[$variable] = null;
		unset ($GLOBALS[$variable]);
	}
	
	public static function platform_session_clear()
	{
		session_regenerate_id();
		session_unset();
		$_SESSION = array ();
	}
	
	public static function platform_session_destroy()
	{
		session_unset();
		$_SESSION = array ();
		session_destroy();
	}
	
	public static function platform_session_retrieve($variable)
	{
		return $_SESSION[$variable];
	}
	
	public static function get_user_id()
	{
		return self :: platform_session_retrieve('_uid');
	}
}
?>