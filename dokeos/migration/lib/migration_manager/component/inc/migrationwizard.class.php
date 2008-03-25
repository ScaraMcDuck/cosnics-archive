<?php
/**
 * @package migration.lib.migration_manager.component.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__).'/wizard/systemmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/settingsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/migrationwizarddisplay.class.php';
require_once dirname(__FILE__).'/wizard/usersmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/classesmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/coursesmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/systemsettingsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/personalagendasmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/metadatamigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/groupsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/announcementsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/calendareventsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/documentsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/linksmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/dropboxesmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/forumsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/learningpathsmigrationwizardpage.class.php';

/**
 * A wizard which guides the user through several steps to perform the migration
 * 
 * @author Sven Vanpoucke
 */
class MigrationWizard extends HTML_QuickForm_Controller
{
	/** 
	 * The component in which the wizard runs
	 */
	private $parent;
	/**
	 * Creates a new MigrationWizard
	 * @param MigrationManagerComponent $parent The migrationmanager component 
	 * in which this wizard runs.
	 */
	function MigrationWizard($parent)
	{
		global $language_interface;
		$this->parent = $parent;
		parent :: HTML_QuickForm_Controller('MigrationWizard', true);
		$this->addPage(new SystemMigrationWizardPage('page_system',$this->parent));
		$this->addPage(new SettingsMigrationWizardPage('page_settings',$this->parent));
		$this->addPage(new UsersMigrationWizardPage('page_users',$this->parent));
		$this->addPage(new SystemSettingsMigrationWizardPage('page_systemsettings',$this->parent));
		$this->addPage(new ClassesMigrationWizardPage('page_classes', $this->parent));
		$this->addPage(new CoursesMigrationWizardPage('page_courses',$this->parent));
		$this->addPage(new PersonalAgendasMigrationWizardPage('page_pa',$this->parent));
		$this->addPage(new MetaDataMigrationWizardPage('page_metadata',$this->parent));
		$this->addPage(new GroupsMigrationWizardPage('page_groups',$this->parent));
		$this->addPage(new AnnouncementsMigrationWizardPage('page_announcements',$this->parent));
		$this->addPage(new CalendarEventsMigrationWizardPage('page_calendar_events',$this->parent));
		$this->addPage(new DocumentsMigrationWizardPage('page_documents',$this->parent));
		$this->addPage(new LinksMigrationWizardPage('page_links',$this->parent));
		//$this->addPage(new DropBoxesMigrationWizardPage('page_dropbox',$this->parent));
		$this->addPage(new ForumsMigrationWizardPage('page_forum',$this->parent));
		$this->addPage(new LearningPathsMigrationWizardPage('page_learning_path',$this->parent));
		$this->addAction('display', new MigrationWizardDisplay($this->parent));
	}
}
?>