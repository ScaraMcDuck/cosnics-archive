<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/build_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class RowsAmountBuildWizardPage extends BuildWizardPage
{
	public function RowsAmountBuildWizardPage($name,$parent)
	{
		parent :: BuildWizardPage($name,$parent);
	}
	
	function buildForm()
	{
		$this->addElement('static','','',Translation :: get('BuildRowsAmountMessage'));
		$this->addElement('text', 'rowsamount', Translation :: get('BuildRowsAmount'), array("size" => "50"));
		$this->addRule('rowsamount', Translation :: get('FieldMustBeNumeric'), 'numeric');
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');		
		$this->_formBuilt = true;
	}
}
?>