<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';
require_once dirname(__FILE__).'/wizard/publicationselectionmaintenancewizardpage.class.php';
require_once dirname(__FILE__).'/wizard/actionselectionmaintenancewizardpage.class.php';
require_once dirname(__FILE__).'/wizard/courseselectionmaintenancewizardpage.class.php';
require_once dirname(__FILE__).'/wizard/confirmationmaintenancewizardpage.class.php';
require_once dirname(__FILE__).'/wizard/maintenancewizardprocess.class.php';
require_once dirname(__FILE__).'/wizard/maintenancewizarddisplay.class.php';
/**
 * A wizard which guides the user to several steps to complete a maintenance
 * action on a course.
 */
class MaintenanceWizard extends HTML_QuickForm_Controller
{
	/**
	 * The repository tool in which this wizard runs.
	 */
	private $parent;
	/**
	 * Creates a new MaintenanceWizard
	 * @param RepositoryTool $parent The repository tool in which this wizard
	 * runs.
	 */
	function MaintenanceWizard($parent)
	{
		$this->parent = $parent;
		parent :: HTML_QuickForm_Controller('MaintenanceWizard', true);
		$this->addPage(new ActionSelectionMaintenanceWizardPage('action_selection', $this->parent));
		$this->addAction('process', new MaintenanceWizardProcess($this->parent));
		$this->addAction('display', new MaintenanceWizardDisplay($this->parent));
		$values = $this->exportValues();
		$action = null;
		$action = isset($values['action']) ? $values['action'] : null;
		$action = is_null($action) ? $_POST['action']  : $action;
		switch($action)
		{
			case  ActionSelectionMaintenanceWizardPage::ACTION_EMPTY:
				$this->addPage(new PublicationSelectionMaintenanceWizardPage('publication_selection',$this->parent));
				$this->addPage(new ConfirmationMaintenanceWizardPage('confirmation',$this->parent,get_lang('EmptyConfirmationQuestion')));
				break;
			case  ActionSelectionMaintenanceWizardPage::ACTION_COPY:
				$this->addPage(new PublicationSelectionMaintenanceWizardPage('publication_selection',$this->parent));
				$this->addPage(new CourseSelectionMaintenanceWizardPage('course_selection',$this->parent));
				$this->addPage(new ConfirmationMaintenanceWizardPage('confirmation',$this->parent,get_lang('CopyConfirmationQuestion')));
				break;
			case  ActionSelectionMaintenanceWizardPage::ACTION_BACKUP:
				$this->addPage(new PublicationSelectionMaintenanceWizardPage('publication_selection',$this->parent));
				$this->addPage(new ConfirmationMaintenanceWizardPage('confirmation',$this->parent,get_lang('BackupConfirmationQuestion')));
				break;
			case  ActionSelectionMaintenanceWizardPage::ACTION_DELETE:
				$this->addPage(new ConfirmationMaintenanceWizardPage('confirmation',$this->parent,get_lang('DeleteConfirmationQuestion')));
				break;
		}
	}
}
?>