<?php

require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

/**
 * @author Tim De Pauw
 */
abstract class LearningStyleSurveyModel
{
	const TYPE_PROPOSITION_AGREEMENT = 1;
	const TYPE_ANSWER_ORDERING = 2;
	
	private static $types;
	
	private static $type2class;
	
	abstract function calculate_result($result, $answer_data, $profile, $section, $question);
	
	abstract function format_answer($answer_data, $profile, $section, $question);
	
	abstract function format_question($survey, $section, $question, $categories);
	
	abstract function create_user_answer_element($name, $profile, $section, $question);
	
	abstract function save_user_answer($profile, $section, $question, $answer_element, $owner_id, $survey_id);
	
	abstract function get_maximum_category_score($profile, $category);
	
	abstract function get_additional_parameters();
	
	function get_additional_result_html($profile, $result, $answer_data)
	{
		return '';
	}
	
	function format_category_name($id, $categories)
	{
		if (isset($id) && array_key_exists($id, $categories))
		{
			return htmlspecialchars($categories[$id]->get_title());
		}
		return Translation :: get('None');
	}
	
	static function get_known_types()
	{
		self :: load_types();
		return self :: $types;
	}
	
	private static function load_types()
	{
		if (!self :: $types)
		{
			self :: $types = array();
			self :: $type2class = array();
			$basename = basename(__FILE__);
			$pattern = dirname(__FILE__) . DIRECTORY_SEPARATOR
				. 'model' . DIRECTORY_SEPARATOR
				. '*_' . $basename;
			$files = glob($pattern);
			$pattern = '{([^/]+)_' . preg_quote($basename) . '$}';
			foreach ($files as $file)
			{
				if (preg_match($pattern, $file, $matches))
				{
					$type = $matches[1];
					$cctype = DokeosUtilities :: underscores_to_camelcase($type);
					$const = constant(get_class() . '::TYPE_' . strtoupper($type));
					self :: $types[$const] = Translation :: get($cctype . 'Survey');
					self :: $type2class[$const] = $cctype . get_class();
				}
			}
		}
	}
	
	static function factory($type)
	{
		self :: load_types();
		$class = self :: $type2class[$type];
		if (isset($class))
		{
			$file = dirname(__FILE__) . DIRECTORY_SEPARATOR
				. 'model' . DIRECTORY_SEPARATOR
				. DokeosUtilities :: camelcase_to_underscores($class)
				. '.class.php';
			require_once $file;
			return new $class();
		}
		return null;
	}
}

?>