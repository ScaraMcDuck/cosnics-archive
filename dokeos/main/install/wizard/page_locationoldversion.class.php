<?php
/**
 * @package main
 * @subpackage install
 */
/**
 * Class  for location old Dokeos installation
 */
class Page_LocationOldVersion extends HTML_QuickForm_Page
{
	function get_title()
	{
		return 'Old version root path';
	}
	function get_info()
	{
		return 'Give location of your old Dokeos installation ';
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('text', 'old_version_path', 'Old version root path');
		$this->applyFilter('old_version_path', 'trim');
		$this->addRule('old_version_path', get_lang('ThisFieldIsRequired'), 'required');
		$this->addRule('old_version_path', get_lang('BadUpdatePath'), 'callback', 'check_update_path');
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
?>