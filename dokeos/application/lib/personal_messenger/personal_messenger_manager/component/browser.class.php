<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';
/**
 * pm component which provides the user with a list
 * of all courses he or she has subscribed to.
 */
class PersonalMessengerBrowserComponent extends PersonalMessengerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('MyPersonalMessenger'));
		$this->display_header($breadcrumbs);
		echo 'test';
		$this->display_footer();
	}
}
?>