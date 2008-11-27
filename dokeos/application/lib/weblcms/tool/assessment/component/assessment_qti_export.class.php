<?php
require_once Path::get_repository_path().'/lib/export/learning_object_export.class.php';

class AssessmentToolQtiExportComponent extends AssessmentToolComponent
{
	
	private $redirect_params;
	
	function set_redirect_params($redirect_params)
	{
		$this->redirect_params = $redirect_params;
	}
	
	function run()
	{
		$pid = $_GET[AssessmentTool :: PARAM_PUBLICATION_ID];
		
		$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication($pid);
		$assessment = $publication->get_learning_object();
		$exporter = LearningObjectExport :: factory('qti', $assessment);
		$path = $exporter->export_learning_object();
		
		header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
		header('Cache-Control: public');
		header('Pragma: no-cache');
		header('Content-type: application/octet-stream');
		header('Content-length: '.filesize($path));
			
		if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
		{
			header('Content-Disposition: filename= '.basename($path));
		}
		else
		{
			header('Content-Disposition: attachment; filename= '.basename($path));
		}
		
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
		{
			header('Pragma: ');
			header('Cache-Control: ');
			header('Cache-Control: public'); // IE cannot download from sessions without a cache
		}
		
		header('Content-Description: '.basename($path));
		header('Content-transfer-encoding: binary');
		$fp = fopen($path, 'r');
		fpassthru($fp);
		fclose($fp);
		Filesystem :: remove($path);
		//$this->redirect(null, null, false, $this->redirect_params);
	}
}
?>