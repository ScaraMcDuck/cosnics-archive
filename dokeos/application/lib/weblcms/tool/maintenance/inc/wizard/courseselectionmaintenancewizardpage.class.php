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
		$dm = WeblcmsDatamanager::get_instance();
		$course_user_relations = $dm->retrieve_course_list_of_user_as_course_admin($this->get_parent()->get_user_id());
		
		$current_code = $this->get_parent()->get_course_id();
		
		while ($course_user_relation = $course_user_relations->next_result())
		{
			if ($course_user_relation->get_course() != $current_code)
			{
				$options[$course_user_relation->get_course()] = $dm->retrieve_course($course_user_relation->get_course())->get_name();
			}
		}
		
		$this->addElement('select','course',get_lang('Course'),$options,array('multiple'=>'multiple','size'=>'5'));
		$this->addRule('course',get_lang('ThisFieldIsRequired'),'required');
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->_formBuilt = true;
	}
}
?>