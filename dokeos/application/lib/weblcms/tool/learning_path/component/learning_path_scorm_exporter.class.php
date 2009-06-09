<?php
require_once Path :: get_repository_path().'lib/export/learning_object_export.class.php';

class LearningPathToolScormExporterComponent extends LearningPathToolComponent
{
	function run()
	{
		$lpid = Request :: get(LearningPathTool :: PARAM_LEARNING_PATH_ID);
		$learning_path = RepositoryDataManager :: get_instance()->retrieve_learning_object($lpid);
		$exporter = LearningObjectExport::factory('scorm', $learning_path);
		$exporter->export_learning_object();
	}
}
?>