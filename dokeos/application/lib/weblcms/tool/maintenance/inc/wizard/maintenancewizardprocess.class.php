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
				$_SESSION['maintenance_error_message'] = 'COPY: TODO';
				break;
			case ActionSelectionMaintenanceWizardPage :: ACTION_BACKUP :
				$_SESSION['maintenance_error_message'] = 'BACKUP: TODO';
				break;
			case ActionSelectionMaintenanceWizardPage :: ACTION_DELETE :
				$_SESSION['maintenance_error_message'] = 'DELETE: TODO';
				break;
		}
		$page->controller->container(true);
		$page->controller->run();
	}
}
?>