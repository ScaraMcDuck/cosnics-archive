<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/learning_style_surveybrowser.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object/learning_style_survey_result/learning_style_survey_result_form.class.php';
require_once dirname(__FILE__).'/../../../../../common/condition/andcondition.class.php';
require_once dirname(__FILE__).'/../../../../../common/condition/equalitycondition.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyTool extends RepositoryTool
{
	const PARAM_SURVEY_PROFILE_ID = 'survey_profile';
	
	// TODO: Remove
	// TODO: Implement roles & rights
	function is_allowed() {
		return true;
	}
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			$this->display_header();
			api_not_allowed();
			$this->display_footer();
			return;
		}
		$toolbar = RepositoryUtilities::build_toolbar(
			array(
				array(
					'img' => api_get_path(WEB_CODE_PATH).'/img/browser.gif',
					'label' => get_lang('TakeSurvey'),
					'href' => $this->get_url(array('mode' => 0)),
					'display' => RepositoryUtilities::TOOLBAR_DISPLAY_ICON_AND_LABEL
				),
				array(
					'img' => api_get_path(WEB_CODE_PATH).'/img/publish.gif',
					'label' => get_lang('PublishSurvey'),
					'href' => $this->get_url(array('mode' => 1)),
					'display' => RepositoryUtilities::TOOLBAR_DISPLAY_ICON_AND_LABEL
				)
			),
			null,
			'margin-bottom: 1em;'
		);
		if (isset($_GET['mode']))
		{
			$_SESSION[get_class()]['mode'] = $_GET['mode'];
		}
		if ($_SESSION[get_class()]['mode'] == 1)
		{
			if ($this->is_allowed(ADD_RIGHT))
			{
				require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
				$this->display_header();
				echo $toolbar;
				$pub = new LearningObjectPublisher($this, 'learning_style_survey_profile', true);
				echo $pub->as_html();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_header();
			echo $toolbar;
			$profile_id = $_REQUEST[self :: PARAM_SURVEY_PROFILE_ID];
			$dm = RepositoryDataManager :: get_instance();
			// TODO: Make sure the object is published
			if ($profile_id
			&& ($profile = $dm->retrieve_learning_object($profile_id, 'learning_style_survey_profile')))
			{
				$condition = new AndCondition(
					new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, api_get_user_id()),
					new EqualityCondition(LearningStyleSurveyResult :: PROPERTY_PROFILE_ID, $profile_id)
				);
				$results = $dm->retrieve_learning_objects('learning_style_survey_result', $condition);
				if (!$results->is_empty())
				{
					$result = $results->next_result();
					$this->review_result($result, $profile);
				}
				else
				{
					$form = new LearningStyleSurveyResultForm($profile, 'survey', 'post', $this->get_url(array(self :: PARAM_SURVEY_PROFILE_ID => $profile_id)));
					if ($form->validate())
					{
						$object = $form->create_learning_object();
						// TODO: analyze answers, redirect to result, whatever
						Display :: display_normal_message(get_lang('AnswersStored'));
					}
					else {
						$form->display();
					}
				}
			}
			else
			{
				$browser = new LearningStyleSurveyBrowser($this);
				echo $browser->as_html();
			}
			$this->display_footer();
		}
	}
	
	private function review_result($result, $profile)
	{
		$answers = $result->get_result_answers();
		$answer_data = array();
		foreach ($answers as $answer)
		{
			$question = $answer->get_question();
			$answer_data[$question->get_id()] = $answer->get_answer(); 
		}
		$survey = $profile->get_survey();
		$category_total = $this->calculate_results($survey, $answer_data);
		$answers_html = $this->format_answers($survey, $answer_data);
		$result_html = $this->format_result($survey, $category_total);
		echo $result_html . $answers_html;
	}
	
	private function calculate_results ($survey, & $answer_data)
	{
		$model = $survey->get_survey_model();
		$sections = $survey->get_survey_sections();
		$res = array();
		foreach ($sections as $section)
		{
			$questions = $section->get_section_questions();
			foreach ($questions as $question)
			{
				$model->calculate_result($res, $answer_data, $survey, $section, $question);
			}
		}
		return $res;
	}
	
	private function format_answers($survey, & $answer_data)
	{
		$sections = $survey->get_survey_sections();
		$model = $survey->get_survey_model();
		$answers_html = '<h4>' . get_lang('MyAnswers') . '</h4>';
		$answers_html .= '<ol>';
		foreach ($sections as $section)
		{
			$answers_html .= '<li>' . $section->get_description() . '<ol>';
			$questions = $section->get_section_questions();
			foreach ($questions as $question)
			{
				$answers_html .= '<li>' . $question->get_description();
				$answers_html .= $model->format_answer($answer_data, $survey, $section, $question);
				$answers_html .= '</li>';
			}
			$answers_html .= '</ol></li>';
		}
		return $answers_html;
	}
	
	function format_result($survey, & $category_total)
	{
		$model = $survey->get_survey_model();
		$titles = array();
		$data = array();
		$result_html = '<h4>' . get_lang('MyResults') . '</h4>';
		$result_html .= '<dl>';
		$categories = $survey->get_survey_categories();
		foreach ($categories as $category)
		{
			$num = $category_total[$category->get_id()];
			$result_html .= '<dt>' . htmlspecialchars($category->get_title()) . '</dt>'
				. '<dd>' . $num . '</dd>';
			$titles[] = $category->get_title();
			$data[] = $num / $model->get_maximum_category_score($survey, $category) * 100;
		}
		$result_html .= '</dl>';
		if (count($data) > 2)
		{
			require_once dirname(__FILE__).'/lib/PsychePolygon.class.php';
			$p = new PsychePolygon($titles, $data);
			$img = $p->create_image(PsychePolygon::IMAGE_TYPE_PNG);
			$result_html .= '<div><img src="data:' . $img['mime_type'] . ';base64,'
				. base64_encode($img['data']) . '"'
				. ' width="' . $img['width'] . '" height="' . $img['height'] . '"/></div>';
		}
		return $result_html;
	}
}
?>