<?php
require_once Path :: get_repository_path().'lib/export/content_object_export.class.php';

class LearningPathToolScormExporterComponent extends LearningPathToolComponent
{
	function run()
	{
		$lpid = Request :: get(LearningPathTool :: PARAM_LEARNING_PATH_ID);
		$learning_path = RepositoryDataManager :: get_instance()->retrieve_content_object($lpid);
		$exporter = ContentObjectExport::factory('scorm', $learning_path);
		$exporter->export_content_object();
	}
}
?>