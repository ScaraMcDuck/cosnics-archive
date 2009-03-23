<?php
//require_once dirname(__FILE__).'/question_types/document_exporter.class.php';
require_once dirname(__FILE__).'/question_types/fill_in_blanks_exporter.class.php';
require_once dirname(__FILE__).'/question_types/matching_exporter.class.php';
require_once dirname(__FILE__).'/question_types/multiple_answer_exporter.class.php';
require_once dirname(__FILE__).'/question_types/multiple_choice_exporter.class.php';
require_once dirname(__FILE__).'/question_types/open_question_exporter.class.php';
//require_once dirname(__FILE__).'/question_types/open_question_with_document_exporter.class.php';
//require_once dirname(__FILE__).'/question_types/percentage_exporter.class.php';
require_once dirname(__FILE__).'/question_types/score_exporter.class.php';
require_once dirname(__FILE__).'/question_types/hotspot_question_exporter.class.php';
require_once dirname(__FILE__).'/../qti_export.class.php';

abstract class QuestionQtiExport extends QtiExport
{
	private $question;
	
	function QuestionQtiExport($question)
	{
		$this->question = $question;
		parent :: __construct($question);
	}
	
	static function factory_question($question)
	{
		switch ($question->get_type())
		{
			case 'open_question':
				$export_type = new OpenQuestionQtiExport($question);
				break;
			case 'fill_in_blanks_question':
				$export_type = new FillInBlanksQuestionQtiExport($question);
				break;
			case 'matching_question':
				$export_type = new MatchingQuestionQtiExport($question);
				break;
			case 'multiple_choice_question':
				if ($question->get_answer_type() == 'radio')
					$export_type = new MultipleChoiceQuestionQtiExport($question);
				else
					$export_type = new MultipleAnswerQuestionQtiExport($question);
				break;
			case 'rating_question':
				$export_type = new ScoreQuestionQtiExport($question);
				break;
			case 'hotspot_question':
				$export_type = new HotspotQuestionQtiExport($question);
				break;
			default:
				$export_type = null;
				break;
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

	function include_question_images($text)
	{
		$tags = Text :: fetch_tag_into_array($text, '<img>');
		$temp_dir = Path :: get(SYS_TEMP_PATH). $this->get_learning_object()->get_owner_id() . '/export_qti/images/';
		
		if (!file_exists($temp_dir))
		{
			mkdir($temp_dir, null, true);
		}		
		
		foreach($tags as $tag)
		{
			$parts = split('/', $tag['src']);
			$newfilename = $temp_dir.$parts[count($parts)-1];
			$repl_filename = 'images/'.$parts[count($parts)-1];
			$files[$newfilename] = $tag['src'];//str_replace($base_path, '', $tag['src']);
			$text = str_replace($tag['src'], $repl_filename, $text);
		}
		foreach ($files as $new => $original)
		{
			copy($original, $new);
		}
		return $text;
	}
}
?>