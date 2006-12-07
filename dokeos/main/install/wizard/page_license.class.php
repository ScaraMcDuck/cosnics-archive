<?php
/**
 * @package main
 * @subpackage install
 */
/**
 * Class for license page
 * Displays the GNU GPL license that has to be accepted to install Dokeos.
 */
class Page_License extends HTML_QuickForm_Page
{
	function get_title()
	{
		return get_lang('Licence');
	}
	function get_info()
	{
		return "Dokeos is free software distributed under the GNU General Public licence (GPL).
																						Please read the license and click 'I accept'.<br /><a href=\"../license/gpl_print.txt\">".get_lang("PrintVers")."</a>";
	}
	function buildForm()
	{
		$this->_formBuilt = true;
		$this->addElement('textarea', 'license', get_lang('Licence'), array ('cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style'=>'background-color: white;'));
		$this->addElement('checkbox','license_accept','',get_lang('IAccept'));
		$this->addRule('license_accept',get_lang('ThisFieldIsRequired'),'required');
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
?>