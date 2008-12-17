<?php

require_once dirname(__FILE__).'/question_types/document_import.class.php';
require_once dirname(__FILE__).'/question_types/fill_in_blanks_import.class.php';
require_once dirname(__FILE__).'/question_types/matching_import.class.php';
require_once dirname(__FILE__).'/question_types/multiple_answer_import.class.php';
require_once dirname(__FILE__).'/question_types/multiple_choice_import.class.php';
require_once dirname(__FILE__).'/question_types/open_question_import.class.php';
require_once dirname(__FILE__).'/question_types/open_question_with_document_import.class.php';
require_once dirname(__FILE__).'/question_types/percentage_import.class.php';
require_once dirname(__FILE__).'/question_types/score_import.class.php';

class QuestionQtiImport extends QtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		echo 'import question';
		$importer = $this->factory_qti_question($data);
	}
	
	function factory_qti_question($lo_file, $user, $category, $dir)
	{
		$itembody = $data['itemBody'];
		foreach ($itembody as $key => $itemdata)
		{
			$tag_type = substr($key, (strlen($key) - strlen('Interaction')), strlen($key));
			if ($tag_type == 'Interaction')
			{
				$tag = $key;
				
				//options needed to differentiate between question types with the same tag
				$num_choices = $itemdata['maxChoices'];
				$ubound = $itemdata['upperBound'];
				break;
			}
		}
		echo '<br/>'.$tag;
		
		switch ($tag)
		{
			case 'extendedTextInteraction':
				return new OpenQuestionQtiImport($dir.$lo_file, $user, $category);
			case 'uploadInteraction':
				return new DocumentQuestionQtiImport($dir.$lo_file, $user, $category);
			case 'choiceInteraction':
				if ($num_choices == 1)
					return new MultipleChoiceQtiImport($dir.$lo_file, $user, $category);
				else
					return new MultipleAnswerQtiImport($dir.$lo_file, $user, $category);
			case 'sliderInteraction':
				if ($ubound == 100)
					return new PercentageQuestionImport($dir.$lo_file, $user, $category);
				else
					return new ScoreQuestionImport($dir.$lo_file, $user, $category);
			case 'textEntryInteraction':
				return new FillInBlanksQuestionQtiImport($dir.$lo_file, $user, $category);
			case 'matchInteraction': 
				return new MatchingQuestionQtiImport($dir.$lo_file, $user, $category);
			default:
				return null;
		}
	}
}
?>