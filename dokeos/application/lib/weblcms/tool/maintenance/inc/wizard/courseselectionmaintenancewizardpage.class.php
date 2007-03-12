<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/maintenancewizardpage.class.php';
/**
 * This form can be used to let the user select a course.
 */
class CourseSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{
	function buildForm()
	{
		$courses = CourseManager::get_course_list_of_user_as_course_admin(api_get_user_id());
		$current_code = $this->get_parent()->get_course_id();
		foreach($courses as $index => $course)
		{
			if($course['code'] != $current_code)
			{
				$options[$course['code']] = $course['title'];
			}
		}
		$this->addElement('select','course',get_lang('Course'),$options,array('multiple'=>'multiple','size'=>'5'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->_formBuilt = true;
	}
}
?>