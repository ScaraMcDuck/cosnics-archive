<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profilercomponent.class.php';
require_once dirname(__FILE__).'/profilepublicationbrowser/profilepublicationbrowsertable.class.php';

class ProfilerBrowserComponent extends ProfilerComponent
{	
	private $firstletter;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->firstletter = $_GET[Profiler :: PARAM_FIRSTLETTER];
		
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
		//$search_conditions = $this->get_search_condition();
		$search_conditions = null;
		$condition = null;
		if (isset($this->firstletter))
		{
			$conditions = array();
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $this->firstletter. '%');
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+1). '%');
			$conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+2). '%');
			$condition = new OrCondition($conditions);
			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}
		return $condition;
	}
}
?>