<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';
require_once dirname(__FILE__).'/publicationbrowser/publicationbrowsertable.class.php';
require_once dirname(__FILE__).'/../../personalmessengermenu.class.php';

class PersonalMessengerViewerComponent extends PersonalMessengerComponent
{	
	private $folder;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('ViewPersonalMessage'));
		
		$id = $_GET[PersonalMessenger :: PARAM_PERSONAL_MESSAGE_ID];
		
		if ($id)
		{
			$publication = $this->retrieve_personal_message_publication($id);
			if ($this->get_user_id() != $publication->get_user())
			{
				$this->display_header($breadcrumbs);
				Display :: display_error_message(get_lang("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			$menu = $this->get_menu_html();
			$output = $this->get_publication_html($publication);
			
			$this->display_header($breadcrumbs);
			echo $menu;
			echo $output;
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoPersonalMessageSelected')));
		}
	}
	
	private function get_publication_html($publication)
	{
		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = print_r($publication);
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_menu_html()
	{
		$extra_items = array ();
//		if ($this->get_search_validate())
//		{
//			// $search_url = $this->get_url();
//			$search_url = '#';
//			$search = array ();
//			$search['title'] = get_lang('SearchResults');
//			$search['url'] = $search_url;
//			$search['class'] = 'search_results';
//			$extra_items[] = & $search;
//		}
//		else
//		{
//			$search_url = null;
//		}
		
		$temp_replacement = '__FOLDER__';
		$url_format = $this->get_url(array (PersonalMessenger :: PARAM_ACTION => PersonalMessenger :: ACTION_BROWSE_MESSAGES, PersonalMessenger :: PARAM_FOLDER => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$user_menu = new PersonalMessengerMenu($this->folder, $url_format, & $extra_items);
		
		if (isset ($search_url))
		{
			$user_menu->forceCurrentUrl($search_url, true);
		}
		
		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $user_menu->render_as_tree();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
}
?>