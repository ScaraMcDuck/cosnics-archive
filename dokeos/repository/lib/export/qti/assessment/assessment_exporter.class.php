<?php
require_once dirname(__FILE__).'/../qti_export.class.php';

class AssessmentQtiExport extends QtiExport
{
	private $assessment;
	
	function AssessmentQtiExport($assessment)
	{
		$this->assessment = $assessment;
	}
	
	function export_learning_object()
	{
		
	}
}
?>