<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';
require_once dirname(__FILE__).'/../../personalmessagepublisher.class.php';

class PersonalMessengerPublisherComponent extends PersonalMessengerComponent
{	
	private $folder;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if (isset($_GET[PersonalMessenger :: PARAM_FOLDER]))
		{
			$this->folder = $_GET[PersonalMessenger :: PARAM_FOLDER];
		}
		else
		{
			$this->folder = PersonalMessenger :: ACTION_FOLDER_INBOX;
		}
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang('SendPersonalMessage'));
		
		$publisher = $this->get_publisher_html();
		
		$this->display_header($breadcrumbs);
		echo $publisher;
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
	
	private function get_publisher_html()
	{
		$pub = new PersonalMessagePublisher($this, 'personal_message', true);
		$html[] =  $pub->as_html();
		
		return implode($html, "\n");
	}
}
?>