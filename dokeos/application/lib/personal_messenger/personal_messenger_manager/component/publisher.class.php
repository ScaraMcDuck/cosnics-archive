<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';
require_once dirname(__FILE__).'/../../personalmessagepublisher.class.php';

class PersonalMessengerPublisherComponent extends PersonalMessengerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('SendPersonalMessage'));
		
		$pub = new PersonalMessagePublisher($this, 'personal_message', true);
		$html[] =  $pub->as_html();
		
		$this->display_header($breadcrumbs);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>