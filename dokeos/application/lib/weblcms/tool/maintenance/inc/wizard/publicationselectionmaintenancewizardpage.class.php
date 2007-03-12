<?php

/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/maintenancewizardpage.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class PublicationSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{
	function buildForm()
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$publications_set = $datamanager->retrieve_learning_object_publications($this->get_parent()->get_course_id());
		while ($publication = $publications_set->next_result())
		{
			$publications[$publication->get_tool()][] = $publication;
		}
		foreach ($publications as $tool => $tool_publications)
		{
			foreach ($tool_publications as $index => $publication)
			{
				$label = $index == 0 ? get_lang(ucfirst($tool).'ToolTitle') : '';
				$learning_object = $publication->get_learning_object();
				$this->addElement('checkbox', 'publications['.$publication->get_id().']', $label, $learning_object->get_title());
			}
		}
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
?>