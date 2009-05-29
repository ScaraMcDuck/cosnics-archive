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

		$publication_id = Request :: get('pid');
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($publication_id);
		$_GET['pid'] = $publication->get_learning_object()->get_id();
		
		$display = ComplexDisplay :: factory($this, 'glossary');
        $display->run();
	}
}

?>