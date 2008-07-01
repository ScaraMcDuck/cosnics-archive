<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../personal_messenger_block.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalMessengerMostRecent extends PersonalMessengerBlock
{
	function run()
	{
		return $this->as_html();
	}
	
	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		
		$personal_messenger = $this->get_parent();
		
		$html[] = '<div class="block" id="block_'. $this->get_block_info()->get_id() .'" style="background-image: url('.Theme :: get_img_path().'block_'.strtolower(PersonalMessenger :: APPLICATION_NAME).'.png);">';
		$html[] = '<div class="title">'. $this->get_block_info()->get_title() .'<a href="#" class="closeEl">[-]</a></div>';
		$html[] = '<div class="description"'. ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') .'>';
		
		$publications = $personal_messenger->retrieve_personal_message_publications($this->get_condition(), array (), array (), 5);
		
		if ($publications->size() > 0)
		{
			$html[] = '<ul style="list-style: square inside;">';
			while ($publication = $publications->next_result())
			{
				$html[] = '<li>';
				$html[] = '<a href="'. $personal_messenger->get_publication_viewing_link($publication) .'">' . $publication->get_publication_object()->get_title() . '</a>';
				$html[] = '</li>';
			}
			$html[] = '</ul>';
		}
		else
		{
			$html[] = Translation :: get('NoNewMessages');
		}
		
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	function get_condition()
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
		$conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_USER, $this->get_user_id());
		return new AndCondition($conditions);
	}
}
?>