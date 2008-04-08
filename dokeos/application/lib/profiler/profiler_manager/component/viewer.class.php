<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profilercomponent.class.php';
require_once Path :: get_repository_path(). 'lib/repositoryutilities.class.php';
require_once Path :: get_repository_path(). 'lib/learningobjectdisplay.class.php';

class ProfilerViewerComponent extends ProfilerComponent
{	
	private $folder;
	private $publication;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('ViewProfile'));
		
		$id = $_GET[Profiler :: PARAM_PROFILE_ID];
		
		if ($id)
		{
			$this->publication = $this->retrieve_profile_publication($id);			
			
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('ViewProfile') . ': ' . $this->publication->get_publication_publisher()->get_username());
			
			$this->display_header($breadcrumbs);
			echo $this->get_publication_as_html();
			
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoProfileSelected')));
		}
	}
	
	function get_publication_as_html()
	{
		$publication = $this->publication;
		$profile = $publication->get_publication_object();
		
		$display = LearningObjectDisplay :: factory($profile);

		$html = array();
		$html[] = $display->get_full_html();		
		
		return implode("\n",$html);
	}
}
?>