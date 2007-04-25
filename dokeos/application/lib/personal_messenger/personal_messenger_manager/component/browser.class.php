<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personalmessengercomponent.class.php';
require_once dirname(__FILE__).'/pmpublicationbrowser/pmpublicationbrowsertable.class.php';
require_once dirname(__FILE__).'/../../personalmessengermenu.class.php';

class PersonalMessengerBrowserComponent extends PersonalMessengerComponent
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
		
		$menu = $this->get_menu_html();
		$output = $this->get_publications_html();
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('MyPersonalMessenger'));
		
		$this->display_header($breadcrumbs);
		echo $menu;
		echo $output;
		$this->display_footer();
	}
	
	private function get_publications_html()
	{
		$parameters = $this->get_parameters(true);
		
		$table = new PmPublicationBrowserTable($this, null, $parameters, $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_menu_html()
	{
		$extra_items = array ();
		$create = array ();
		$create['title'] = get_lang('Create');
		$create['url'] = $this->get_personal_message_creation_url();
		$create['class'] = 'create';
		$extra_items[] = & $create;
		
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
	
	function get_condition()
	{
		$search_conditions = $this->get_search_condition();
		$conditions = array();
		$folder = $this->folder;
		if (isset($folder))
		{
			$folder_condition = null;
			
			switch ($folder)
			{
				case PersonalMessenger :: ACTION_FOLDER_INBOX :
					$folder_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
					break;
				case PersonalMessenger :: ACTION_FOLDER_OUTBOX :
					$folder_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_SENDER, $this->get_user_id());
					break;
				default :
					$folder_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
			}
		}
		else
		{
			$folder_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
		}
		
		if (count($search_conditions))
		{
			$condition = new AndCondition($folder_condition, $search_conditions);
		}
		else
		{
			$condition = $folder_condition;
		}
		
		$user_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_USER, $this->get_user_id());
		return new AndCondition($condition, $user_condition);
	}
	
	function get_folder()
	{
		return $this->folder;
	}
}
?>