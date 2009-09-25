<?php
/**
 * @package application.weblcms.tool.assessment.component
 */

require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

/**
 * Represents the view component for the assessment tool.
 *
 */
class GlossaryToolViewerComponent extends GlossaryToolComponent 
{
	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		/*$publication_id = Request :: get('pid');
		$publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);
		$object = $publication->get_content_object();
	
		Request :: set_get('pid',$object->get_id())*/
		
		$object = RepositoryDataManager :: get_instance()->retrieve_content_object(Request :: get('pid'));
		
		$this->set_parameter(Tool :: PARAM_ACTION, GlossaryTool :: ACTION_VIEW_GLOSSARY);
		
		$trail = new BreadcrumbTrail();
		$trail->add(new BreadCrumb($this->get_url(), Translation :: get('ViewGlossary')));
		
		$this->display_header($trail);
		 
		$display = ComplexDisplay :: factory($this, $object->get_type());
        $display->run();
        $this->display_footer();
	}
}

?>