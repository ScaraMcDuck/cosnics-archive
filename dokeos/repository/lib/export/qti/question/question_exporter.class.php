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

class QuestionQtiExport
{
	private $question;
	
	function QuestionQtiExport($question)
	{
		$this->question = $question;
	}
	
	function get_question()
	{
		return $this->question();
	}
	
	abstract function export();
	
	static function factory($question)
	{
		switch ($question->get_question_type())
		{
			case Question :: TYPE_OPEN:
				$export_type = new OpenQuestionQtiExporter($question);
				break;
			case Question :: TYPE_OPEN_WITH_DOCUMENT:
				$export_type = new OpenQuestionWithDocumentQtiExporter($question);
				break;
			case Question :: TYPE_DOCUMENT:
				$export_type = new DocumentQuestionQtiExporter($question);
				break;
			case Question :: TYPE_FILL_IN_BLANKS:
				$export_type = new FillInBlanksQuestionQtiExporter($question);
				break;
			case Question :: TYPE_MATCHING:
				$export_type = new MatchingQuestionQtiExporter($question);
				break;
			case Question :: TYPE_MULTIPLE_ANSWER:
				$export_type = new MultipleAnswerQuestionQtiExporter($question);
				break;
			case Question :: TYPE_MULTIPLE_CHOICE:
				$export_type = new MultipleChoiceQuestionQtiExporter($question);
				break;
			case Question :: TYPE_PERCENTAGE:
				$export_type = new PercentageQuestionQtiExporter($question);
				break;
			case Question :: TYPE_SCORE:
				$export_type = new ScoreQuestionQtiExporter($question);
				break;
			/*case Question :: TYPE_YES_NO:
				$export_type = new OpenQuestionQtiExport($clo_question);
				break;*/
			default:
				$export_type = null;
		}
		return $export_type;
	}
}
?>