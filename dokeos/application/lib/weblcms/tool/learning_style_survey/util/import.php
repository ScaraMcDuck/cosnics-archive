<?php

require_once dirname(__FILE__).'/../../../../../../main/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/learning_object/learning_style_survey/learning_style_survey.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/learning_object/learning_style_survey_category/learning_style_survey_category.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/learning_object/learning_style_survey_section/learning_style_survey_section.class.php';
require_once dirname(__FILE__).'/../../../../../../repository/lib/learning_object/learning_style_survey_question/learning_style_survey_question.class.php';

header('Content-Type: text/plain; charset=UTF-8');

// ID of the owner of the survey
$owner_id = 1;
// source file
$file = dirname(__FILE__).'/survey.txt';

is_readable($file) or die('Cannot read from ' . $file);

$dm = RepositoryDataManager :: get_instance();
$condition = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $owner_id);
$objects = $dm->retrieve_learning_objects('category', $condition, array(LearningObject :: PROPERTY_PARENT_ID), array(SORT_ASC));
$my_repository = $objects->next_result()->get_id();
 
$contents = array_map('rtrim', file($file));
$survey_title = array_shift($contents);
$category_count = intval(array_shift($contents));
$categories = array();
$question_categories = array();
for ($i = 0; $i < $category_count; $i++)
{
	$title = array_shift($contents);
	$questions = explode(' ', array_shift($contents));
	foreach ($questions as $q)
	{
		$question_categories[$q - 1][] = $i;
	}
	$categories[] = $title;
}
$section_count = intval(array_shift($contents));
$sections = array();
for ($i = 0; $i < $section_count; $i++)
{
	$sections[] = array(
		'title' => array_shift($contents),
		'questions' => intval(array_shift($contents)),
		'introduction' => array_shift($contents)
	);
}

$question_index = 0;
foreach ($sections as $index => $section)
{
	$question_count = $section['questions'];
	$questions = array();
	for ($i = 0; $i < $question_count; $i++)
	{
		$questions[] = array(
			'text' => array_shift($contents),
			'categories' => $question_categories[$question_index]
		);
		$question_index++;
	}
	$sections[$index]['questions'] = $questions;
}

echo 'Creating survey ', $survey_title, "\n";

$survey = new LearningStyleSurvey();
$survey->set_owner_id($owner_id);
$survey->set_survey_type(LearningStyleSurveyModel :: TYPE_PROPOSITION_AGREEMENT);
$survey->set_title($survey_title);
$survey->set_description('<p>' . htmlspecialchars($survey_title) . '</p>');
$survey->set_parent_id($my_repository);
$survey->create();

$category_map = array();

foreach ($categories as $index => $category) 
{
	echo 'Creating category ', $category, "\n";
	$obj = new LearningStyleSurveyCategory();
	$obj->set_owner_id($owner_id);
	$obj->set_title($category);
	$obj->set_description(htmlspecialchars($category));
	$obj->set_parent_id($survey->get_id());
	$obj->create();
	$category_map[$index] = $obj->get_id(); 
}

foreach ($sections as $section)
{
	echo 'Creating section ', $section['title'], "\n";
	$obj = new LearningStyleSurveySection();
	$obj->set_owner_id($owner_id);
	$obj->set_title($section['title']);
	$obj->set_description($section['introduction']);
	$obj->set_parent_id($survey->get_id());
	$obj->create();
	foreach ($section['questions'] as $question)
	{
		echo 'Creating question ', $question['text'], "\n";
		$qobj = new LearningStyleSurveyQuestion();
		$qobj->set_owner_id($owner_id);
		$qobj->set_title($question['text']);
		$qobj->set_description('<p>' . htmlspecialchars($question['text']) . '</p>');
		$cids = array();
		foreach ($question['categories'] as $i)
		{
			$cids[] = $category_map[$i];
		}
		$qobj->set_question_category_ids($cids);
		$qobj->set_parent_id($obj->get_id());
		$qobj->create();
	}
}

echo 'Complete.';

?>