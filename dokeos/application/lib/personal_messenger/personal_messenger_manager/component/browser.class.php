<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../personal_messenger.class.php';
require_once dirname(__FILE__).'/../personal_messenger_component.class.php';
require_once dirname(__FILE__).'/pm_publication_browser/pm_publication_browser_table.class.php';

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
		
		$output = $this->get_publications_html();
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyPersonalMessenger')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get(ucfirst($this->folder))));
		
		$this->display_header($trail);
		echo $output;
		$this->display_footer();
	}
	
	private function get_publications_html()
	{
		$parameters = $this->get_parameters(true);
		
		$table = new PmPublicationBrowserTable($this, null, $parameters, $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
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
		
		$condition = $folder_condition;
		
		$user_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_USER, $this->get_user_id());
		return new AndCondition($condition, $user_condition);
	}
	
	function get_folder()
	{
		return $this->folder;
	}
}
?>