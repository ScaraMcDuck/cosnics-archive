<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../profiler.class.php';
require_once dirname(__FILE__).'/../profiler_component.class.php';
require_once Path :: get_repository_path(). 'lib/repository_utilities.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';

class ProfilerViewerComponent extends ProfilerComponent
{	
	private $folder;
	private $publication;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewProfile')));
		
		$id = $_GET[Profiler :: PARAM_PROFILE_ID];
		
		if ($id)
		{
			$this->publication = $this->retrieve_profile_publication($id);			
			$trail->add(new Breadcrumb($this->get_url(),  $this->publication->get_publication_publisher()->get_username()));
			
			$this->display_header($trail);
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