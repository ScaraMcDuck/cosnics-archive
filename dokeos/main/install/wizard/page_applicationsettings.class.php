<?php
/**
 * @package main
 * @subpackage install
 */
/**
 * Class for application settings page
 * Displays a form where the user can enter the installation settings
 * regarding the applications
 */
class Page_ApplicationSettings extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('AppSetting');
	}
	
	function get_info()
	{
		return get_lang('AppSettingIntro');
	}
	
	public static function get_applications()
	{
		$applications = array();
		
		$path = dirname(__FILE__).'/../../../application/lib/';
		if ($handle = opendir($path))
		{
			while (false !== ($file = readdir($handle)))
			{
				$toolPath = $path.'/'. $file .'/install';
				if (is_dir($toolPath) && (preg_match('/^[a-z][a-z_]+$/', $file) > 0))
				{
					$class_name = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper(\1)', $file));
					$applications[$file] = $class_name;
				}
			}
			closedir($handle);
		}
		
		return $applications;
	}
	
	function buildForm()
	{
		$this->_formBuilt = true;

		$applications = self :: get_applications();
		foreach($applications as $application => $application_name)
		{
			//$application_label = '<div style="margin: 0px 0px 0px 20px;">' . get_lang($application_name . 'Description') . '</div>';
			$checkbox_name = 'install_' . $application;
			$this->addElement('checkbox', $checkbox_name, '', get_lang($application_name));
			//$this->addElement('static', null, null, $application_label);
			
			$appDefaults[$checkbox_name] = '1';
		}
		
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->setDefaults($appDefaults);
	}
}
?>