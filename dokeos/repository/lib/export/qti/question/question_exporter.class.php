<?php
require_once dirname(__FILE__).'/question_types/document_exporter.class.php';
require_once dirname(__FILE__).'/question_types/fill_in_blanks_exporter.class.php';
require_once dirname(__FILE__).'/question_types/matching_exporter.class.php';
require_once dirname(__FILE__).'/question_types/multiple_answer_exporter.class.php';
require_once dirname(__FILE__).'/question_types/multiple_choice_exporter.class.php';
require_once dirname(__FILE__).'/question_types/open_question_exporter.class.php';
require_once dirname(__FILE__).'/question_types/open_question_with_document_exporter.class.php';
require_once dirname(__FILE__).'/question_types/percentage_exporter.class.php';
require_once dirname(__FILE__).'/question_types/score_exporter.class.php';
require_once dirname(__FILE__).'/../qti_export.class.php';

abstract class QuestionQtiExport extends QtiExport
{
	//private $question;
	
	function QuestionQtiExport($question)
	{
		$this->question = $question;
		parent :: __construct($question);
	}
	
	static function factory_question($question)
	{
		switch ($question->get_question_type())
		{
			case Question :: TYPE_OPEN:
				$export_type = new OpenQuestionQtiExport($question);
				break;
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				$export_type = new OpenQuestionWithDocumentQtiExport($question);
				break;
			case Question :: TYPE_DOCUMENT:
				$export_type = new DocumentQuestionQtiExport($question);
				break;
			case Question :: TYPE_FILL_IN_BLANKS:
				$export_type = new FillInBlanksQuestionQtiExport($question);
				break;
			case Question :: TYPE_MATCHING:
				$export_type = new MatchingQuestionQtiExport($question);
				break;
			case Question :: TYPE_MULTIPLE_ANSWER:
				$export_type = new MultipleAnswerQuestionQtiExport($question);
				break;
			case Question :: TYPE_MULTIPLE_CHOICE:
				$export_type = new MultipleChoiceQuestionQtiExport($question);
				break;
			case Question :: TYPE_PERCENTAGE:
				$export_type = new PercentageQuestionQtiExport($question);
				break;
			case Question :: TYPE_SCORE:
				$export_type = new ScoreQuestionQtiExport($question);
				break;
			default:
				$export_type = null;
		}
		return $export_type;
	}
	
	function create_qti_file($xml)
	{
		$doc = new DOMDocument();
		$doc->loadXML($xml);
		
		$temp_dir = Path :: get(SYS_TEMP_PATH). $this->get_learning_object()->get_owner_id() . '/export_qti/';
  		
  		if(!is_dir($temp_dir))
  		{
  			mkdir($temp_dir, '0777', true);
  		}
  	
  		$xml_path = $temp_dir . 'question_qti_'.$this->get_learning_object()->get_id().'.xml';
		$doc->save($xml_path);
			
		return $xml_path;
	}

}
?>