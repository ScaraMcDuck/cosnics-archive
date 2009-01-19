<?php

require_once dirname(__FILE__).'/../learning_object_export.class.php';
require_once dirname(__FILE__).'/learning_path/learning_path_scorm_export.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class ScormExport extends LearningObjectExport
{
	private $rdm;
	
	function ScormExport($learning_object)
	{
		$this->rdm = RepositoryDataManager :: get_instance();
		parent :: __construct($learning_object);	
	}
	
	public function export_learning_object()
	{
		$exporter = self :: factory_scorm($this->get_learning_object());
		return $exporter->export_learning_object();
	}
	
	function get_rdm()
	{
		return $this->rdm;
	}
	
	static function factory_scorm($learning_object)
	{
		switch ($learning_object->get_type())
		{
			case 'learning_path':
				$exporter = new LearningPathScormExport($learning_object);
				break;
			default:
				$exporter = null;
				break;
		}
		return $exporter;
	}
}
?>