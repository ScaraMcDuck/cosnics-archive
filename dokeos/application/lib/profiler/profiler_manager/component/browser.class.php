<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profilercomponent.class.php';
require_once dirname(__FILE__).'/profilepublicationbrowser/profilepublicationbrowsertable.class.php';

class ProfilerBrowserComponent extends ProfilerComponent
{	
	private $folder;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if (isset($_GET[Profiler :: PARAM_FOLDER]))
		{
			$this->folder = $_GET[Profiler :: PARAM_FOLDER];
		}
		else
		{
			$this->folder = Profiler :: ACTION_FOLDER_INBOX;
		}
		
		$output = $this->get_publications_html();
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('MyProfiler'));
		
		$this->display_header($breadcrumbs);
		echo $output;
		$this->display_footer();
	}
	
	private function get_publications_html()
	{
		$parameters = $this->get_parameters(true);
		
		$table = new ProfilePublicationBrowserTable($this, null, $parameters, $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$conditions = array();
//		$folder = $this->folder;
//		if (isset($folder))
//		{
//			$folder_condition = null;
//			
//			switch ($folder)
//			{
//				case Profiler :: ACTION_FOLDER_INBOX :
//					$folder_condition = new EqualityCondition(ProfilePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
//					break;
//				case Profiler :: ACTION_FOLDER_OUTBOX :
//					$folder_condition = new EqualityCondition(ProfilePublication :: PROPERTY_SENDER, $this->get_user_id());
//					break;
//				default :
//					$folder_condition = new EqualityCondition(ProfilePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
//			}
//		}
//		else
//		{
//			$folder_condition = new EqualityCondition(ProfilePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
//		}
//		
//		$condition = $folder_condition;
//		
//		$user_condition = new EqualityCondition(ProfilePublication :: PROPERTY_USER, $this->get_user_id());
//		return new AndCondition($condition, $user_condition);
		return null;
	}
	
	function get_folder()
	{
		return $this->folder;
	}
}
?>