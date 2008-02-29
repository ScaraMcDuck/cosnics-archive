<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositoryutilities.class.php';

class PersonalMessengerViewerComponent extends PersonalMessengerComponent
{	
	private $folder;
	private $publication;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get_lang('ViewPersonalMessage'));
		
		$id = $_GET[PersonalMessenger :: PARAM_PERSONAL_MESSAGE_ID];
		
		if ($id)
		{
			$this->publication = $this->retrieve_personal_message_publication($id);
			$publication = $this->publication;
			if ($this->get_user_id() != $publication->get_user())
			{
				$this->display_header($breadcrumbs);
				Display :: display_error_message(Translation :: get_lang("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			if ($publication->get_status() == 1)
			{
				$publication->set_status(0);
				$publication->update();
			}
			
			
			$this->display_header($breadcrumbs);
			echo $this->get_publication_modification_links();
			echo $this->get_publication_as_html();
			
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get_lang('NoPersonalMessageSelected')));
		}
	}
	
	function get_publication_as_html()
	{
		$publication = $this->publication;
		$message = $publication->get_publication_object(); 
		$html = array();
		
		$sender = $publication->get_publication_sender();
		$recipient = $publication->get_publication_recipient();

		$html[] = '<div class="learning_object" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'description.gif);">';
		$html[] = '<div class="title">'. Translation :: get_lang('Data') .'</div>';		
		$html[] = '<div class="description">';
		$html[] = '<b>'.Translation :: get_lang('MessageFrom'). '</b>:&nbsp;'. $sender->get_firstname(). '&nbsp;' .$sender->get_lastname() . '<br />';
		$html[] = '<b>'.Translation :: get_lang('MessageTo'). '</b>:&nbsp;'. $recipient->get_firstname(). '&nbsp;' .$recipient->get_lastname() . '<br />';
		$html[] = '<b>'.Translation :: get_lang('MessageDate'). '</b>:&nbsp;'. Text :: format_locale_date(Translation :: get_lang('dateFormatShort').', '.Translation :: get_lang('timeNoSecFormat'),$publication->get_published()) . '<br />';
		$html[] = '<b>'.Translation :: get_lang('MessageSubject'). '</b>:&nbsp;'. $message->get_title();
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = '<div class="learning_object" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'personal_message.gif);">';
		$html[] = '<div class="title">'. Translation :: get_lang('Message') .'</div>';
		$html[] = '<div class="description">'.$message->get_description().'</div>';
		$html[] = '</div>';
		
		
		if ($message->supports_attachments())
		{
			$attachments = $message->get_attached_learning_objects();
			if (count($attachments))
			{
				$html[] = RepositoryUtilities :: build_block_hider('script');
				$html[] = '<div class="attachments" style="margin-top: 1em;">';
				$html[] = '<div class="attachments_title">'.htmlentities(Translation :: get_lang('Attachments')).'</div>';
				$html[] = '<ul class="attachments_list">';
				RepositoryUtilities :: order_learning_objects_by_title($attachments);
				foreach ($attachments as $attachment)
				{
					$html[] = '<li class="personal_message_attachment"><div style="float: left;"><img src="'.$this->get_path(WEB_IMG_PATH).'treemenu_types/'.$attachment->get_type().'.gif" alt="'.htmlentities(Translation :: get_lang(LearningObject :: type_to_class($attachment->get_type()).'TypeName')).'"/></div><div style="float: left;">&nbsp;'.$attachment->get_title().'&nbsp;</div>';
					$html[] = RepositoryUtilities :: build_block_hider('begin', $attachment->get_id(), 'Attachment');
					
					$display = LearningObjectDisplay :: factory($attachment);
					$html[] = $display->get_full_html();
										
					$html[] = RepositoryUtilities :: build_block_hider('end', $attachment->get_id());
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
				'label' => Translation :: get_lang('Reply'),
				'img' => $this->get_path(WEB_IMG_PATH).'reply.gif',
				'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
		}
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>