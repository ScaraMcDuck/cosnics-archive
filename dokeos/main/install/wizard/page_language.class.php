<?php
/**
 * @package main
 * @subpackage install
 */
class Page_Language extends HTML_QuickForm_Page
{
	function get_title()
	{
		return 'Welcome to the Dokeos installer!';
	}
	function get_info()
	{
		return 'Please select the language you\'d like to use while installing:';
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('select', 'install_language', 'Language', get_language_folder_list());
		$buttons[0] = & HTML_QuickForm :: createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($buttons, 'buttons', '', '&nbsp', false);
		$this->setDefaultAction('next');
	}
}
?>