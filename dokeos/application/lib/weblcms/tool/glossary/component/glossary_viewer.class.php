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
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($publication_id);
		$object = $publication->get_learning_object();
	
		$_GET['pid'] = $object->get_id();*/
		
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object(Request :: get('pid'));
		
		$this->set_parameter(Tool :: PARAM_ACTION, GlossaryTool :: ACTION_VIEW_GLOSSARY);
		
		$this->display_header(new BreadcrumbTrail()); 
		$display = ComplexDisplay :: factory($this, $object->get_type());
        $display->run();
        $this->display_footer();
	}
}

?>