<?php

/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
class MaintenanceWizardProcess extends HTML_QuickForm_Action
{
	private $parent;
	public function MaintenanceWizardProcess($parent)
	{
		$this->parent = $parent;
	}
	function perform(& $page, $actionName)
	{
		$values = $page->controller->exportValues();
		//Todo: Split this up in several form-processing classes depending on selected action
		switch ($values['action'])
		{
			case ActionSelectionMaintenanceWizardPage :: ACTION_EMPTY :
				$publication_ids = array_keys($values['publications']);
				$dm = WeblcmsDataManager :: get_instance();
				$number_of_publications_to_delete = count($publication_ids);
				$number_of_deleted_publications = 0;
				foreach ($publication_ids as $index => $id)
				{
					$publication = $dm->retrieve_learning_object_publication($id);
					if ($dm->delete_learning_object_publication($publication))
					{
						$number_of_deleted_publications ++;
					}
				}
				if ($number_of_deleted_publications == $number_of_publications_to_delete)
				{
					$_SESSION['maintenance_message'] = get_lang('AllSelectedPublicationsRemoved');
				}
				else
				{
					$_SESSION['maintenance_error_message'] = get_lang('NotAllSelectedPublicationsRemoved');
				}
				break;
			case ActionSelectionMaintenanceWizardPage :: ACTION_COPY :
				$publication_ids = array_keys($values['publications']);
				$dm = WeblcmsDataManager :: get_instance();
				$number_of_publications_to_copy = count($publication_ids);
				$number_of_copied_publications = 0;
				foreach ($publication_ids as $index => $id)
				{
					$publication = $dm->retrieve_learning_object_publication($id);
					$courses = $values['course'];
					foreach($courses as $index => $course_code)
					{
						$pub = new LearningObjectPublication(
							null,
							$publication->get_learning_object(),
							$course_code,
							$publication->get_tool(),
							0,
							null,
							null,
							$publication->get_from_date(),
							$publication->get_to_date(),
							$publication->get_publisher_id(),
							time(),
							$publication->is_hidden(),
							$dm->get_next_learning_object_publication_display_order_index($course_code,$publication->get_tool(),0),
							false
						);
						$pub->create();
					}
				}
				$_SESSION['maintenance_message'] = get_lang('CopyFinished');
				break;
			case ActionSelectionMaintenanceWizardPage :: ACTION_BACKUP :
				$_SESSION['maintenance_error_message'] = 'BACKUP: TODO';
				break;
			case ActionSelectionMaintenanceWizardPage :: ACTION_DELETE :
				$dm = WeblcmsDatamanager::get_instance();
				$dm->delete_course($this->parent->get_course_id());
				header('Location: '.api_get_path(WEB_PATH).'/user_portal.php');
				exit;
				break;
		}
		$page->controller->container(true);
		$page->controller->run();
	}
}
?>