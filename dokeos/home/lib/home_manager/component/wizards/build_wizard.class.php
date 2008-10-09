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
require_once dirname(__FILE__).'/build/introduction_build_wizard_page.class.php';
require_once dirname(__FILE__).'/build/rows_amount_build_wizard_page.class.php';
require_once dirname(__FILE__).'/build/rows_config_build_wizard_page.class.php';
require_once dirname(__FILE__).'/build/columns_config_build_wizard_page.class.php';
require_once dirname(__FILE__).'/build/blocks_config_build_wizard_page.class.php';
require_once dirname(__FILE__).'/build/action_selection_build_wizard_page.class.php';
require_once dirname(__FILE__).'/build/confirmation_build_wizard_page.class.php';
require_once dirname(__FILE__).'/build/build_wizard_process.class.php';
require_once dirname(__FILE__).'/build/build_wizard_display.class.php';
/**
 * A wizard which guides the user to several steps to complete a maintenance
 * action on a course.
 */
class BuildWizard extends HTML_QuickForm_Controller
{
	/**
	 * The repository tool in which this wizard runs.
	 */
	private $parent;
	/**
	 * Creates a new MaintenanceWizard
	 * @param Tool $parent The repository tool in which this wizard
	 * runs.
	 */
	function BuildWizard($parent)
	{
		$this->parent = $parent;
		parent :: HTML_QuickForm_Controller('BuildWizard', true);
		
		$values = $this->exportValues();
		$this->addPage(new IntroductionBuildWizardPage('introduction', $this->parent, Translation :: get('BuildIntroductionMessage')));
		$this->addPage(new RowsAmountBuildWizardPage('rows_amount', $this->parent));
		$this->addPage(new RowsConfigBuildWizardPage('rows_config', $this->parent, $values));
		$this->addPage(new ColumnsConfigBuildWizardPage('columns_config', $this->parent, $values));
		$this->addPage(new BlocksConfigBuildWizardPage('blocks_config', $this->parent, $values));
		$this->addPage(new ConfirmationBuildWizardPage('confirmation', $this->parent, Translation :: get('BuildConfirmationQuestion'), $values));
		
		$this->addAction('process', new BuildWizardProcess($this->parent));
		$this->addAction('display', new BuildWizardDisplay($this->parent));
	}
}
?>