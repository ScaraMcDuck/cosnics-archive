<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profiler_component.class.php';
require_once dirname(__FILE__).'/profile_publication_browser/profile_publication_browser_table.class.php';

class ProfilerBrowserComponent extends ProfilerComponent
{	
	private $firstletter;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();		
		$this->firstletter = $_GET[Profiler :: PARAM_FIRSTLETTER];
		
		$output = $this->get_publications_html();
		
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyProfiler')));
		
		$this->display_header($trail, true);
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
		$search_conditions = $this->get_search_condition();
		//$search_conditions = null;
		$condition = null;
		if (isset($this->firstletter))
		{
			$conditions = array();
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, $this->firstletter. '*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+1). '*');
			$conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, chr(ord($this->firstletter)+2). '*');
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