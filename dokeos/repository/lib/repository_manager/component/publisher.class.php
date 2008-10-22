<?php
/**
 * 
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/publisher_wizard/publisher_wizard.class.php';
/**
 * 
 */
class RepositoryManagerPublisherComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$wizard = new PublisherWizard($this);
		$wizard->run();
	}
}
?>
