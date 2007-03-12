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
	const ACTION_EMPTY = 1;
	const ACTION_COPY = 2;
	const ACTION_BACKUP = 3;
	const ACTION_DELETE = 4;
	function buildForm()
	{
		$available = $this->is_available(self::ACTION_EMPTY) ? null : 'disabled';
		$this->addElement('radio', 'action', get_lang('EmptyThisCourse'), get_lang('EmptyThisCourseInformation'),self::ACTION_EMPTY,$available);
		$available = $this->is_available(self::ACTION_COPY) ? null : 'disabled';
		$this->addElement('radio', 'action', get_lang('CopyThisCourse'), get_lang('CopyThisCourseInformation'),self::ACTION_COPY,$available);
		$available = $this->is_available(self::ACTION_BACKUP) ? null : 'disabled';
		$this->addElement('radio', 'action', get_lang('BackupThisCourse'), get_lang('BackupThisCourseInformation'),self::ACTION_BACKUP,$available);
		$this->addElement('radio', 'action', get_lang('DeleteThisCourse'), get_lang('DeleteThisCourseInformation'),self::ACTION_DELETE);
		$this->addRule('action',get_lang('ThisFieldIsRequired'),'required');
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->_formBuilt = true;
	}
	private function is_available($action)
	{
		$dm = WeblcmsDatamanager::get_instance();
		switch($action)
		{
			case self::ACTION_BACKUP:
			case self::ACTION_EMPTY:
				if($dm->count_learning_object_publications($this->get_parent()->get_course_id()) == 0)
				{
					return false;
				}
				return true;
			case self::ACTION_COPY:
				if($dm->count_learning_object_publications($this->get_parent()->get_course_id()) == 0)
				{
					return false;
				}
				if(count(CourseManager::get_course_list_of_user_as_course_admin(api_get_user_id())) <= 1)
				{
					return false;
				}
				return true;
		}
	}
}
?>