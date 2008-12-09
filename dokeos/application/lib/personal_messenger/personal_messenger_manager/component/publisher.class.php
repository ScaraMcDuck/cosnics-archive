<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personal_messenger_component.class.php';
require_once dirname(__FILE__).'/../../publisher/personal_message_publisher.class.php';
require_once dirname(__FILE__).'/../../personal_message_repo_viewer.class.php';

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
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SendPersonalMessage')));
		
		$object = $_GET['object'];
		$pub = new PersonalMessageRepoViewer($this, 'personal_message', true);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url(array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'LearningObject: ';
			$publisher = new PersonalMessagePublisher($pub);
			$html[] = $publisher->get_publication_form($object);
		}
		
		$this->display_header($trail);
		echo implode("\n", $html);
		echo '<div style="clear: both;"></div>';
		$this->display_footer();
	}
}
?>