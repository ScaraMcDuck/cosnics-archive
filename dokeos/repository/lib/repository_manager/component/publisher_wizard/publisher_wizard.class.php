<?php
/**
 * @package install.lib.install_manager.component.inc
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';

require_once dirname(__FILE__).'/pages/location_selection_publisher_wizard_page.class.php';
require_once dirname(__FILE__).'/pages/publication_form_publisher_wizard_page.class.php';

require_once dirname(__FILE__).'/pages/publisher_wizard_process.class.php';
require_once dirname(__FILE__).'/pages/publisher_wizard_display.class.php';
/**
 *
 */
class PublisherWizard extends HTML_QuickForm_Controller
{
	/**
	 * The repository tool in which this wizard runs.
	 */
	private $parent;
	/**
	 *	 */
	function PublisherWizard($parent)
	{
		global $language_interface;
		$this->parent = $parent;
		parent :: HTML_QuickForm_Controller('PublisherWizard', true);
		$this->addPage(new LocationSelectionPublisherWizardPage('page_locations',$this->parent));
		$values = $this->exportValues();
		
		$apps = array();
		foreach($values as $key => $value)
		{
			if($value == 1)
			{
				$split = split('_', $key);
				$app = $split[0];
				if(!in_array($app, $apps))
					$apps[] = $app;
			}
		}
		
		foreach($apps as $app)
			$this->addPage(new PublicationFormPublisherWizardPage('page_pubform_' . $app,$this->parent, $app));
		
	//	$this->addAction('process', new PublisherWizardProcess($this->parent));
		$this->addAction('display', new PublisherWizardDisplay($this->parent));
	}
}
?>