<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';
require_once dirname(__FILE__).'/publicationbrowser/publicationbrowsertable.class.php';
require_once dirname(__FILE__).'/../../personalmessengermenu.class.php';
require_once Path :: get_repository_path(). 'lib/repository_utilities.class.php';

class PersonalMessengerAttachmentViewerComponent extends PersonalMessengerComponent
{	
	private $folder;
	private $publication;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewPersonalMessageAttachments')));
		
		$id = $_GET[PersonalMessenger :: PARAM_PERSONAL_MESSAGE_ID];
		
		if ($id)
		{
			$this->publication = $this->retrieve_personal_message_publication($id);
			$publication = $this->publication;
			if ($this->get_user_id() != $publication->get_user())
			{
				$this->display_header($trail);
				Display :: display_error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			
			$this->display_header($trail);
			echo $this->get_publication_as_html();
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPersonalMessageSelected')));
		}
	}
	
	function get_publication_as_html()
	{
		$publication = $this->publication;
		$message = $publication->get_publication_object(); 
		$html = array();
		
		if ($message->supports_attachments())
		{
			$attachments = $message->get_attached_learning_objects();
			if (count($attachments))
			{
				RepositoryUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$display = LearningObjectDisplay :: factory($attachment);
					$html[] = $display->get_full_html();
//					$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_img_path().'learning_object/'.$attachment->get_icon_name().'.png);">';
//					$html[] = '<div class="title">'. $attachment->get_title() .'</div>';
//					$html[] = $attachment->get_description();
//					$html[] = '</div>';
				}
			}
		}
		
		return implode("\n",$html);
	}
}
?>