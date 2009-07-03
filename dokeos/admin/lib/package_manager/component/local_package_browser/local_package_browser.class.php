<?php

/**
 * Class that is responsible for generating diagnostic information about the system
 * @author spou595
 */

require_once Path :: get_library_path() . 'html/table/simple_table.class.php';
require_once dirname(__FILE__) . '/local_package_browser_cell_renderer.class.php';

class LocalPackageBrowser
{	
	/**
	 * The manager where this browser runs on
	 */
	private $manager;
	
	/**
	 * The status's
	 */
	const STATUS_OK = 1;
	const STATUS_WARNING = 2;
	const STATUS_ERROR = 3;
	const STATUS_INFORMATION = 4;
	
	function LocalPackageBrowser($manager)
	{
		$this->manager = $manager;
	}
	
	function to_html()
	{
		$sections = array('learning_object', 'application');
		
		$current_section = Request :: get(PackageManager :: PARAM_SECTION);
		$current_section = $current_section ? $current_section : 'learning_object';
		$html[] = '<br /><div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		
		foreach ($sections as $section)
		{
			$html[] = '<li><a';
			if ($current_section == $section)
			{
				$html[] = ' class="current"';
			}
			$params = $this->manager->get_parameters();
			$params[PackageManager :: PARAM_SECTION] = $section;
			$html[] = ' href="'. $this->manager->get_url($params).'">'.htmlentities(Translation :: get(DokeosUtilities :: underscores_to_camelcase($section).'Title')).'</a></li>';
		}
		
		$html[] = '</ul><div class="tabbed-pane-content">';
		
		$data = call_user_func(array($this, 'get_' . $current_section . '_data'));
		
		$table = new SimpleTable($data, new LocalPackageBrowserCellRenderer(), null, 'diagnoser');
		$html[] = $table->toHTML();
		
		$html[] = '</div></div>';
		
		return implode("\n", $html);
	}
	
	/**
	 * Functions to get the data for the dokeos diagnostics
	 * @return array of data
	 */
	function get_learning_object_data()
	{
		$objects = array();
		
		$object_path = Path :: get_repository_path() . 'lib/learning_object/';
		$object_folders = Filesystem :: get_directory_content($object_path, Filesystem :: LIST_DIRECTORIES, false);
		
		$condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_LEARNING_OBJECT);
		$registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition);
		$installed_objects = array();
		
		while($registration = $registrations->next_result())
		{
			$installed_objects[] = $registration->get_name();
		}
		
		$installable_objects = array_diff($object_folders, $installed_objects);
		sort($installable_objects, SORT_STRING);
		
		foreach($installable_objects as $installable_object)
		{
			if ($installable_object !== '.svn')
			{
				$data = array();
				$data[] = DokeosUtilities :: underscores_to_camelcase_with_spaces($installable_object);
				
				$toolbar_data = array();
	    		$toolbar_data[] = array(
	    			'href' => $this->manager->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE, PackageManager :: PARAM_SECTION => 'learning_object', PackageManager :: PARAM_PACKAGE => $installable_object)),
	    			'label' => Translation :: get('Install'),
	    			'img' => Theme :: get_image_path().'action_install.png'
	    		);
	    		
				$data[] = DokeosUtilities :: build_toolbar($toolbar_data);
				
				$objects[] = $data;
			}
		}
		
		return $objects;
	}
	
	/**
	 * Functions to get the data for the mysql diagnostics
	 * @return array of data
	 */
	function get_application_data()
	{
		$applications = array();
		
		$application_path = Path :: get_application_path() . 'lib/';
		$application_folders = Filesystem :: get_directory_content($application_path, Filesystem :: LIST_DIRECTORIES, false);
		
		$condition = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
		$registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition);
		$installed_applications = array();
		
		while($registration = $registrations->next_result())
		{
			$installed_applications[] = $registration->get_name();
		}
		
		$installable_applications = array_diff($application_folders, $installed_applications);
		sort($installable_applications, SORT_STRING);
		
		foreach($installable_applications as $installable_application)
		{
			if ($installable_application !== '.svn')
			{
				$data = array();
				$data[] = DokeosUtilities :: underscores_to_camelcase_with_spaces($installable_application);
				
				$toolbar_data = array();
	    		$toolbar_data[] = array(
	    			'href' => $this->manager->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE, PackageManager :: PARAM_INSTALL_TYPE => PackageManager :: INSTALL_LOCAL, PackageManager :: PARAM_SECTION => 'application', PackageManager :: PARAM_PACKAGE => $installable_application)),
	    			'label' => Translation :: get('Install'),
	    			'img' => Theme :: get_image_path().'action_install.png'
	    		);
	    		
				$data[] = DokeosUtilities :: build_toolbar($toolbar_data);
				
				$applications[] = $data;
			}
		}
		
		return $applications;
	}
	
	/**
	 * Additional functions needed for fast integration
	 */
	function build_setting($status, $section, $title, $url, $current_value, $expected_value, $formatter, $comment)
	{
		switch($status)
		{
			case self :: STATUS_OK: 
				$img = 'status_ok_mini.png';
				break;
			case self :: STATUS_WARNING: 
				$img = 'status_warning_mini.png';
				break;
			case self :: STATUS_ERROR: 
				$img = 'status_error_mini.png';
				break;
			case self :: STATUS_INFORMATION: 
				$img = 'status_confirmation_mini.png';
				break;
		}
		
		$image = '<img src="' . Theme :: get_common_image_path() . $img . '" alt="' . $status . '" />';
		$url = $this->get_link($title, $url);
		
		$formatted_current_value = $current_value;
		$formatted_expected_value = $expected_value;
		
		if($formatter)
		{
			if(method_exists($this, 'format_' . $formatter))
			{
				$formatted_current_value = call_user_func(array($this, 'format_' . $formatter), $current_value);
				$formatted_expected_value = call_user_func(array($this, 'format_' . $formatter), $expected_value);
			}	
		}
		
		return array($image, $section, $url, $formatted_current_value, $formatted_expected_value, $comment);
	}
	
	/**
	 * Create a link with a url and a title
	 * @param $title
	 * @param $url
	 * @return string the url
	 */
	function get_link($title, $url)
	{
		return '<a href="' . $url . '" target="about:bank">' . $title . '</a>';	
	}
	
	function format_yes_no($value)
	{
		return $value ? Translation :: get('Yes') : Translation :: get('No');
	}
	
	function format_on_off($value)
	{
		return $value ? Translation :: get('On') : Translation :: get('Off');
	}
}
?>