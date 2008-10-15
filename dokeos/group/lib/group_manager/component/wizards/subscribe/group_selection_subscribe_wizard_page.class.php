<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/subscribe_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class GroupSelectionSubscribeWizardPage extends SubscribeWizardPage
{
	private $group;
	
	public function GroupSelectionSubscribeWizardPage($name,$parent,$group)
	{
		parent :: SubscribeWizardPage($name,$parent);
		$this->group = $group;
	}
	
	function buildForm()
	{
		$datamanager = UserDataManager :: get_instance();
		$groups = $this->get_parent()->retrieve_classgroups(null, null, null, array(Group :: PROPERTY_NAME), array(SORT_ASC));
		$group_options = array();
		
		while ($group = $groups->next_result())
		{
			$group_options[$group->get_id()] = $group->get_name();
		}
		
    	$this->addElement('select', 'Group', Translation :: get('Group'), $group_options);
		$this->addRule('Group', Translation :: get('ThisFieldIsRequired'), 'required');
		//$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');		
		$this->set_defaults();
		$this->_formBuilt = true;
	}
	
	function set_defaults()
	{
		$defaults = array();
		$defaults['Group'] = $_GET[GroupManager :: PARAM_GROUP_ID];
		$this->setDefaults($defaults);
	}
}
?>