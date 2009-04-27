<?php
require_once Path :: get_repository_path().'lib/import/learning_object_import.class.php';
require_once Path :: get_repository_path() .'lib/learning_object_import_form.class.php';

class LearningPathToolScormImporterComponent extends LearningPathToolComponent
{
	function run()
	{
		$parameters = array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_IMPORT_SCORM);
		$import_form = new LearningObjectImportForm('import', 'post', $this->get_url($parameters), 0, $this->get_user(), 'scorm');
		
		if ($import_form->validate())
		{
			$learning_object = $import_form->import_learning_object();
			dump($learning_object);
		}
		else
		{
			$trail = new BreadCrumbTrail();
			$trail->add(new BreadCrumb($this->get_url($parameters), Translation :: get('ImportScorm')));
			$this->display_header($trail);
			$import_form->display();
			$this->display_footer();
		}
	}
}
?>