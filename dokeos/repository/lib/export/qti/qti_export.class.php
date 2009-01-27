<?php

require_once dirname(__FILE__).'/../learning_object_export.class.php';
require_once dirname(__FILE__).'/assessment/assessment_qti_export.class.php';
require_once dirname(__FILE__).'/question/question_qti_export.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class QtiExport extends LearningObjectExport
{
	private $rdm;
	
	function QtiExport($learning_object)
	{
		$this->rdm = RepositoryDataManager :: get_instance();
		parent :: __construct($learning_object);	
	}
	
	public function export_learning_object()
	{
		$exporter = self :: factory_qti($this->get_learning_object());
		return $exporter->export_learning_object();
	}
	
	static function factory_qti($learning_object)
	{
		switch ($learning_object->get_type())
		{
			case 'assessment':
				$exporter = new AssessmentQtiExport($learning_object);
				break;
			case 'survey':
				$exporter = new AssessmentQtiExport($learning_object);
				break;
			default:
				$exporter = QuestionQtiExport :: factory_question($learning_object);
				break;
		}
		return $exporter;
	}

}
?>