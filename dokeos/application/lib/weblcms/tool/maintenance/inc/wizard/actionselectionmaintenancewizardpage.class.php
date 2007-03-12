<?php

/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/maintenancewizardpage.class.php';
/**
 * This form can be used to let the user select the action.
 */
class ActionSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{
	const ACTION_EMPTY = 0;
	const ACTION_COPY = 1;
	const ACTION_BACKUP = 2;
	const ACTION_DELETE = 3;
	function buildForm()
	{
		$this->addElement('radio', 'action', get_lang('EmptyThisCourse'), get_lang('EmptyThisCourseInformation'),self::ACTION_EMPTY);
		$this->addElement('radio', 'action', get_lang('CopyThisCourse'), get_lang('CopyThisCourseInformation'),self::ACTION_COPY);
		$this->addElement('radio', 'action', get_lang('BackupThisCourse'), get_lang('BackupThisCourseInformation'),self::ACTION_BACKUP);
		$this->addElement('radio', 'action', get_lang('DeleteThisCourse'), get_lang('DeleteThisCourseInformation'),self::ACTION_DELETE);
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
?>