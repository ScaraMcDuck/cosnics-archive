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
require_once dirname(__FILE__).'/wizard/course/quizmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/studentpublicationsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/surveysmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/scormsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/assignmentsmigrationwizardpage.class.php';
require_once dirname(__FILE__).'/wizard/course/userinfosmigrationwizardpage.class.php';

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
		
		$this->addpages();
		
		$this->addAction('display', new MigrationWizardDisplay($this->parent));
	}
	
	function addpages()
	{
		$exports = $this->exportValues();
		$old_system = $exports['old_system'];
	
		if(!$old_system) return;
		
		$pages = $this->loadpages($old_system);
		foreach($pages as $name => $page)
		{
			$this->addPage(new $page($name,$this->parent));
		}
	}
	
	function loadpages($old_system)
	{
		$file = realpath(Path :: get_migration_path() . 'platform/' . $old_system . '/wizards.xml');
		$doc = new DOMDocument();
		$doc->load($file);
		$platform = $doc->getElementsByTagname('platform')->item(0);
		$name = $platform->getAttribute('name');
		$xml_wizards = $doc->getElementsByTagname('wizard');
		
		$wizardpages = array();
		
		foreach($xml_wizards as $wizard)
		{
			if($wizard->hasAttribute('name') && $wizard->hasAttribute('wizardpage'))
				$wizardpages[$wizard->getAttribute('name')] = $wizard->getAttribute('wizardpage');
		}
		
		return $wizardpages;
		
	}
}
?>