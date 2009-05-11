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
		
		$reply = Request :: get('reply');
		$user = Request :: get(PersonalMessenger :: PARAM_USER_ID);
		
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PersonalMessenger::PARAM_ACTION=>PersonalMessenger::ACTION_BROWSE_MESSAGES,PersonalMessenger::PARAM_FOLDER => PersonalMessenger::ACTION_FOLDER_INBOX)),Translation :: get('MyPersonalMessenger')));
        if(isset($reply))
        {
            $trail->add(new Breadcrumb($this->get_url(array(PersonalMessenger::PARAM_ACTION=>PersonalMessenger::ACTION_BROWSE_MESSAGES,PersonalMessenger::PARAM_FOLDER => PersonalMessenger::ACTION_FOLDER_INBOX)),Translation :: get(ucfirst(PersonalMessenger::ACTION_FOLDER_INBOX))));
            $trail->add(new Breadcrumb($this->get_url(array(PersonalMessenger::PARAM_ACTION=>PersonalMessenger::ACTION_VIEW_PUBLICATION,PersonalMessenger::PARAM_PERSONAL_MESSAGE_ID=>$reply,PersonalMessenger::PARAM_FOLDER=>PersonalMessenger::ACTION_FOLDER_INBOX)), Translation :: get('ViewPersonalMessage')));
        }
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SendPersonalMessage')));
		
		$object = $_GET['object'];
		//$edit = $_GET['edit'];
		$pub = new PersonalMessageRepoViewer($this, 'personal_message', true);
		$pub->set_parameter('reply', $reply);
		$pub->set_parameter(PersonalMessenger :: PARAM_USER_ID, $user);
		
		if(!isset($object))// || $edit == 1)
		{	
			if($reply)
			{
				$publication = PersonalMessengerDataManager :: get_instance()->retrieve_personal_message_publication($reply);
				$lo_id = $publication->get_personal_message();
				$lo = RepositoryDataManager :: get_instance()->retrieve_learning_object($lo_id, 'personal_message');
				$title = $lo->get_title();
				$defaults['title'] = (substr($title, 0, 3) == 'RE:') ? $title : 'RE: ' . $title;
				$pub->set_creation_defaults($defaults);
			}	
			
			$html[] = '<p><a href="' . $this->get_url(array('go' => null), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
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