<?php

/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/maintenancewizardpage.class.php';
/**
 * This form can be used to let the user confirm the selected action.
 */
class ConfirmationMaintenanceWizardPage extends MaintenanceWizardPage
{
	function buildForm()
	{
		switch($values['action'])
		{
			case ActionSelectionMaintenanceWizardPage::ACTION_EMPTY:
				$info = 'EMPTY';
				break;
			case ActionSelectionMaintenanceWizardPage::ACTION_COPY:
				$info = 'COPY';
				break;
			case ActionSelectionMaintenanceWizardPage::ACTION_BACKUP:
				$info = 'BACKUP';
				break;
			case ActionSelectionMaintenanceWizardPage::ACTION_DELETE:
				$info = 'DELETE';
				break;
		}
		$this->addElement('static','',$info);
		$this->addElement('checkbox', 'confirm',' ', get_lang('Confirm'));
		$this->addRule('confirm',get_lang('ThisFieldIsRequired'),'required');
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->_formBuilt = true;
	}
}
?>