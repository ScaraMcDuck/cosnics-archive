<?php
// Theme-paths
define('WEB_IMG_PATH', 'WEB_IMG_PATH');
define('SYS_IMG_PATH', 'SYS_IMG_PATH');
define('WEB_CSS_PATH', 'WEB_CSS_PATH');
define('SYS_CSS_PATH', 'SYS_CSS_PATH');

class Theme
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;
	
	/**
	 * The theme we're currently using
	 */
	private $theme;
	
	/**
	 * The application we're currently rendering
	 */
	private $application;

    function Theme()
    {
    	$this->theme = PlatformSetting :: get('theme');
    }
    
    function get_theme()
    {
		return self :: get_instance()->theme;
    }
    
    function set_theme($theme)
    {
		$instance = self :: get_instance();
		$instance->theme = $theme;
    }
    
	function get_application()
	{
		return self :: get_instance()->application;
	}	
	
	function set_application($application)
	{
		$instance = self :: get_instance();
		$instance->application = $application;
	}
    
    function get_path($path_type)
    {
		switch ($path_type)
		{
			case WEB_IMG_PATH :
				return Path :: get(WEB_LAYOUT_PATH) . $this->get_theme() . '/img/';
			case SYS_IMG_PATH :
				return Path :: get(SYS_LAYOUT_PATH) . $this->get_theme() . '/img/';
			case WEB_CSS_PATH :
				return Path :: get(WEB_LAYOUT_PATH) . $this->get_theme() . '/css/';
			case SYS_CSS_PATH :
				return Path :: get(SYS_LAYOUT_PATH) . $this->get_theme() . '/css/';
		}
    }
    
	/**
	 * Get the web path to the application's css file
	 */
    function get_css_path()
    {
    	$instance = self :: get_instance();
		return $instance->get_path(WEB_CSS_PATH) . $instance->get_application() . '.css';
    }
    
	/**
	 * Get the web path to the general css file
	 */
    function get_common_css_path()
    {
    	$instance = self :: get_instance();
		return $instance->get_path(WEB_CSS_PATH) . 'common.css';
    }
    
	/**
	 * Get the path to the application's image folder
	 */
    function get_img_path($application = null)
    {
    	$instance = self :: get_instance();
    	$application = (is_null($application) ? $instance->get_application() : $application);
		return $instance->get_path(WEB_IMG_PATH) . $application . '/';
    }
    
	/**
	 * Get the path to the general image folder
	 */
    function get_common_img_path()
    {
    	$instance = self :: get_instance();
		return $instance->get_path(WEB_IMG_PATH) . 'common/';
    }
    
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			self :: $instance = new self();
		}
		return self :: $instance;
	}
	
	function get_themes()
	{
		$options = array();
		
		$path = Path :: get(SYS_LAYOUT_PATH);
		$directories = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
		
		foreach($directories as $index => $directory)
		{
			if (substr($directory, 0 , 1) != '.')
			{
				$options[$directory] = DokeosUtilities :: underscores_to_camelcase($directory);
			}
		}
		
		return $options;
	}
}
?>