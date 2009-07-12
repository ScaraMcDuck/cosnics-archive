<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/maintenance_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class PublicationSelectionMaintenanceWizardPage extends MaintenanceWizardPage
{
	function buildForm()
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_parent()->get_course_id());
		$publications_set = $datamanager->retrieve_learning_object_publications_new($condition, new ObjectTableOrder(Announcement :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
		while ($publication = $publications_set->next_result())
		{
			$publications[$publication->get_tool()][] = $publication;
		}
		foreach ($publications as $tool => $tool_publications)
		{
			foreach ($tool_publications as $index => $publication)
			{
				$label = $index == 0 ? Translation :: get(ucfirst($tool).'ToolTitle') : '';
				$learning_object = $publication->get_learning_object();
				$this->addElement('checkbox', 'publications['.$publication->get_id().']', $label, $learning_object->get_title());
			}
		}
		$this->addFormRule(array('PublicationSelectionMaintenanceWizardPage','count_selected_publications'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->_formBuilt = true;
	}
	/**
	 * Returns the number of selected publications
	 * @param array $values
	 */
	function count_selected_publications($values)
	{
		if(isset($values['publications']))
		{
			return true;
		}
		return array('buttons' => Translation :: get('SelectPublications'));
	}
}
?>