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
require_once dirname(__FILE__).'/subscribe/groupselectionsubscribewizardpage.class.php';
require_once dirname(__FILE__).'/subscribe/userselectionsubscribewizardpage.class.php';
require_once dirname(__FILE__).'/subscribe/actionselectionsubscribewizardpage.class.php';
require_once dirname(__FILE__).'/subscribe/confirmationsubscribewizardpage.class.php';
require_once dirname(__FILE__).'/subscribe/subscribewizardprocess.class.php';
require_once dirname(__FILE__).'/subscribe/subscribewizarddisplay.class.php';
/**
 * A wizard which guides the user to several steps to complete a maintenance
 * action on a course.
 */
class SubscribeWizard extends HTML_QuickForm_Controller
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
	function SubscribeWizard($parent)
	{
		$this->parent = $parent;
		parent :: HTML_QuickForm_Controller('SubscribeWizard', true);
		$this->addPage(new ActionSelectionSubscribeWizardPage('action_selection', $this->parent));
		$this->addAction('process', new SubscribeWizardProcess($this->parent));
		$this->addAction('display', new SubscribeWizardDisplay($this->parent));
		$values = $this->exportValues();
		$action = null;
		$action = isset($values['action']) ? $values['action'] : null;
		$action = is_null($action) ? $_POST['action']  : $action;
		switch($action)
		{
//			case  ActionSelectionSubscribeWizardPage::ACTION_EMPTY:
//				$this->addPage(new PublicationSelectionSubscribeWizardPage('publication_selection',$this->parent));
//				$this->addPage(new ConfirmationSubscribeWizardPage('confirmation',$this->parent,Translation :: get('EmptyConfirmationQuestion')));
//				break;
//			case  ActionSelectionSubscribeWizardPage::ACTION_COPY:
//				$this->addPage(new PublicationSelectionSubscribeWizardPage('publication_selection',$this->parent));
//				$this->addPage(new CourseSelectionSubscribeWizardPage('course_selection',$this->parent));
//				$this->addPage(new ConfirmationSubscribeWizardPage('confirmation',$this->parent,Translation :: get('CopyConfirmationQuestion')));
//				break;
//			case  ActionSelectionSubscribeWizardPage::ACTION_BACKUP:
//				$this->addPage(new PublicationSelectionSubscribeWizardPage('publication_selection',$this->parent));
//				$this->addPage(new ConfirmationSubscribeWizardPage('confirmation',$this->parent,Translation :: get('BackupConfirmationQuestion')));
//				break;
//			case  ActionSelectionSubscribeWizardPage::ACTION_DELETE:
//				$this->addPage(new ConfirmationSubscribeWizardPage('confirmation',$this->parent,Translation :: get('DeleteConfirmationQuestion')));
//				break;
			case  ActionSelectionSubscribeWizardPage::ACTION_SUBSCRIBE:
				$this->addPage(new GroupSelectionSubscribeWizardPage('group_selection',$this->parent, $this->parent->get_classgroup()));
				$this->addPage(new UserSelectionSubscribeWizardPage('user_selection',$this->parent));
				$this->addPage(new ConfirmationSubscribeWizardPage('confirmation',$this->parent,Translation :: get('SubscribeConfirmationQuestion')));
				break;
		}
	}
}
?>