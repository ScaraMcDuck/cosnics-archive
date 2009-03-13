<?php

/**
 * Class that is responsible for generating diagnostic information about the system
 * @author spou595
 */

require_once Path :: get_library_path() . 'html/table/simple_table.class.php';
require_once dirname(__FILE__) . '/diagnoser_cellrenderer.class.php';

class Diagnoser
{	
	/**
	 * The manager where this diagnoser runs on
	 */
	private $manager;
	
	/**
	 * The status's
	 */
	const STATUS_OK = 1;
	const STATUS_WARNING = 2;
	const STATUS_ERROR = 3;
	const STATUS_INFORMATION = 4;
	
	function Diagnoser($manager)
	{
		$this->manager = $manager;
	}
	
	function to_html()
	{
		$sections = array('dokeos', 'php', 'mysql', 'webserver');
		
		$current_section = Request :: get('section');
		$current_section = $current_section?$current_section:'dokeos';
		$html[] = '<br /><div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		
		foreach ($sections as $section)
		{
			$html[] = '<li><a';
			if ($current_section == $section)
			{
				$html[] = ' class="current"';
			}
			$params = $this->manager->get_parameters();
			$params['section'] = $section;
			$html[] = ' href="'. $this->manager->get_url($params, true).'">'.htmlentities(Translation :: get(ucfirst($section).'Title')).'</a></li>';
		}
		
		$html[] = '</ul><div class="tabbed-pane-content">';
		
		$data = call_user_func(array($this, 'get_' . $current_section . '_data'));
		
		$table = new SimpleTable($data, new DiagnoserCellRenderer(), null, 'diagnoser');
		$html[] = $table->toHTML();
		
		$html[] = '</div></div>';
		
		return implode("\n", $html);
	}
	
	/**
	 * Functions to get the data for the dokeos diagnostics
	 * @return array of data
	 */
	function get_dokeos_data()
	{
		$array = array();
		
		$array[] = $this->build_setting(self :: STATUS_OK, '[Files]', 'Configuration', 'http://www.dokeosplanet.org', 1, 1, 'yes_no');
		
		return $array;
	}
	
	/**
	 * Functions to get the data for the php diagnostics
	 * @return array of data
	 */
	function get_php_data()
	{
		
	}
	
	/**
	 * Functions to get the data for the mysql diagnostics
	 * @return array of data
	 */
	function get_mysql_data()
	{
		
	}
	
	/**
	 * Functions to get the data for the webserver diagnostics
	 * @return array of data
	 */
	function get_webserver_data()
	{
		
	}
	
	/**
	 * Additional functions needed for fast integration
	 */

	
	function build_setting($status, $section, $title, $url, $current_value, $expected_value, $formatter)
	{
		switch($status)
		{
			case self :: STATUS_OK: 
				$img = 'status_confirmation_mini.png';
				break;
			case self :: STATUS_WARNING: 
				$img = 'status_warning_mini.png';
				break;
			case self :: STATUS_ERROR: 
				$img = 'status_error_mini.png';
				break;
			case self :: STATUS_OK: 
				$img = 'status_confirmation_mini.png';
				break;
		}
		
		$image = '<img src="' . Theme :: get_common_image_path() . $img . '" alt="' . $status . '" />';
		$url = $this->get_link(Translation :: get($title . 'Title'), $url);
		
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

		
		return array($image, $section, $url, $formatted_current_value, $formatted_expected_value, Translation :: get($title . 'Comment'));
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
	
	function format_on_of($value)
	{
		return $value ? Translation :: get('Yes') : Translation :: get('No');
	}
}
?>