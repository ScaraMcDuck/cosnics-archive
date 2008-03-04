<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool: Publication selection form
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once dirname(__FILE__).'/subscribewizardpage.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class UserSelectionSubscribeWizardPage extends SubscribeWizardPage
{
	function buildForm()
	{
		$url = Path :: get_classgroup_path().'lib/xml_user_feed.php';
		$locale = array ();
		$locale['Display'] = Translation :: get_lang('SelectUsers');
		$locale['Searching'] = Translation :: get_lang('Searching');
		$locale['NoResults'] = Translation :: get_lang('NoResults');
		$locale['Error'] = Translation :: get_lang('Error');
		$hidden = false;
		$elem = $this->addElement('element_finder', 'users', Translation :: get_lang('Users'), $url, $locale, null);
		//$elem->excludeElements(array($this->form_user->get_user_id()));
		//$elem->setDefaultCollapsed(false);
		//$this->addFormRule(array('UserSelectionSubscribeWizardPage','count_selected_users'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get_lang('Previous'));
		$prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
		$this->_formBuilt = true;
	}
	/**
	 * Returns the number of selected publications
	 * @param array $values
	 */
	function count_selected_users($values)
	{
		if(isset($values['publications']))
		{
			return true;
		}
		return array('buttons' => Translation :: get_lang('SelectPublications'));
	}
}
?>