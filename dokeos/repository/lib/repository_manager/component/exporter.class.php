<?php
/**
 * $Id: deleter.class.php 15420 2008-05-26 17:34:32Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../export/learning_object_export.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerExporterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$id = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		
		if($id)
		{
			$lo = $this->retrieve_learning_object($id);
			$exporter = LearningObjectExport :: factory('dlof');
			$path = $exporter->export_learning_object($lo);
			
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
			
		}
	}
}
?>