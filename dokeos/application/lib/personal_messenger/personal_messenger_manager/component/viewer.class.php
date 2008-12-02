<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personal_messenger_component.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

class PersonalMessengerViewerComponent extends PersonalMessengerComponent
{	
	private $folder;
	private $publication;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewPersonalMessage')));
		
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
			
			if ($publication->get_status() == 1)
			{
				$publication->set_status(0);
				$publication->update();
			}
			
			
			$this->display_header($trail);
			echo '<br />' . $this->get_publication_modification_links();
			echo '<div class="clear"></div><br />';
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
		
		$sender = $publication->get_publication_sender();
		$recipient = $publication->get_publication_recipient();

		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'learning_object/description.png);">';
		$html[] = '<div class="title">'. Translation :: get('Data') .'</div>';		
		$html[] = '<div class="description">';
		$html[] = '<b>'.Translation :: get('MessageFrom'). '</b>:&nbsp;'. $sender->get_firstname(). '&nbsp;' .$sender->get_lastname() . '<br />';
		$html[] = '<b>'.Translation :: get('MessageTo'). '</b>:&nbsp;'. $recipient->get_firstname(). '&nbsp;' .$recipient->get_lastname() . '<br />';
		$html[] = '<b>'.Translation :: get('MessageDate'). '</b>:&nbsp;'. Text :: format_locale_date(Translation :: get('dateFormatShort').', '.Translation :: get('timeNoSecFormat'),$publication->get_published()) . '<br />';
		$html[] = '<b>'.Translation :: get('MessageSubject'). '</b>:&nbsp;'. $message->get_title();
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path().'learning_object/personal_message.png);">';
		$html[] = '<div class="title">'. Translation :: get('Message') .'</div>';
		$html[] = '<div class="description">'.$message->get_description().'</div>';
		$html[] = '</div>';
		
		
		if ($message->supports_attachments())
		{
			$attachments = $message->get_attached_learning_objects();
			if (count($attachments))
			{
				$html[] = DokeosUtilities :: build_block_hider('script');
				$html[] = '<div class="attachments" style="margin-top: 1em;">';
				$html[] = '<div class="attachments_title">'.htmlentities(Translation :: get('Attachments')).'</div>';
				$html[] = '<ul class="attachments_list">';
				DokeosUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$html[] = '<li class="personal_message_attachment"><div style="float: left;"><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/></div><div style="float: left;">&nbsp;'.$attachment->get_title().'&nbsp;</div>';
					$html[] = DokeosUtilities :: build_block_hider('begin', $attachment->get_id(), 'Attachment');
					
					$display = LearningObjectDisplay :: factory($attachment);
					$html[] = $display->get_full_html();
										
					$html[] = DokeosUtilities :: build_block_hider('end', $attachment->get_id());
					//$html[] = '<div style="clear: both;">&nbsp;</div>';
					$html[] = '</li>';
				}
				$html[] = '</ul>';
				$html[] = '</div>';
			}
		}
		
		
		
		return implode("\n",$html);
	}
	
	function get_publication_modification_links()
	{
		$publication = $this->publication;
		
		$toolbar_data = array();
		
		if ($publication->get_recipient() == $this->get_user_id())
		{
			$toolbar_data[] = array(
				'href' => $this->get_publication_reply_url($publication),
				'label' => Translation :: get('Reply'),
				'img' => Theme :: get_common_image_path().'action_reply.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
		}
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>