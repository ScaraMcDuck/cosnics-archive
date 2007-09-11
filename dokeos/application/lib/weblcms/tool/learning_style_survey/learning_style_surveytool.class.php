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
	const PARAM_VIEW_SURVEY_RESULTS = 'view_survey_results';
	
	// TODO: Remove
	// TODO: Implement roles & rights
	function is_allowed()
	{
		return true;
	}
	
	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			$this->display_header();
			$this->not_allowed();
			$this->display_footer();
			return;
		}
		// TODO: icons etc.
		$toolbar = RepositoryUtilities::build_toolbar(
			array(
				array(
					'img' => api_get_path(WEB_CODE_PATH).'/img/browser.gif',
					'label' => get_lang('Browse'),
					'href' => $this->get_url(array('mode' => 0)),
					'display' => RepositoryUtilities::TOOLBAR_DISPLAY_ICON_AND_LABEL
				),
				array(
					'img' => api_get_path(WEB_CODE_PATH).'/img/publish.gif',
					'label' => get_lang('Publish'),
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
		$this->display_header();
		echo $toolbar;
		if ($_SESSION[get_class()]['mode'] == 1)
		{
			if ($this->is_allowed(ADD_RIGHT))
			{
				require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
				$pub = new LearningObjectPublisher($this, 'learning_style_survey_profile', true);
				echo $pub->as_html();
			}
			else
			{
				$this->not_allowed();
			}
		}
		else
		{
			$profile_id = $_REQUEST[self :: PARAM_SURVEY_PROFILE_ID];
			if ($profile_id)
			{
				$dm = RepositoryDataManager :: get_instance();
				$profile = $dm->retrieve_learning_object($profile_id, 'learning_style_survey_profile');
				// TODO: make sure $profile is published
				if ($_REQUEST[self :: PARAM_VIEW_SURVEY_RESULTS])
				{
					if ($this->is_allowed(ADD_RIGHT))
					{
						// TODO: filter on users or groups somehow?
						$condition = new EqualityCondition(LearningStyleSurveyResult :: PROPERTY_PROFILE_ID, $profile_id);
						$results = $dm->retrieve_learning_objects('learning_style_survey_result', $condition);
						while ($result = $results->next_result())
						{
							$this->review_result($result, $profile, true);
						}
					}
					else
					{
						$this->not_allowed();
					}
				}
				else
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
			}
			else
			{
				$browser = new LearningStyleSurveyBrowser($this);
				echo $browser->as_html();
			}
		}
		$this->display_footer();
	}
	
	private function review_result($result, $profile, $as_admin = false)
	{
		// TODO: display survey title
		$answers = $result->get_result_answers();
		$answer_data = array();
		foreach ($answers as $answer)
		{
			$question = $answer->get_question();
			$answer_data[$question->get_id()] = $answer->get_answer(); 
		}
		$survey = $profile->get_survey();
		$category_total = $this->calculate_results($survey, $answer_data);
		// TODO: determine how much to display in each case
		if ($as_admin)
		{
			$user = $result->get_owner_id();
			echo $this->format_result($survey, $category_total, $user),
				$this->format_answers($survey, $answer_data, $user);
		}
		else
		{
			echo $this->format_result($survey, $category_total),
				$this->format_answers($survey, $answer_data);
		}
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
	
	private function format_answers($survey, & $answer_data, $user = null)
	{
		$sections = $survey->get_survey_sections();
		$model = $survey->get_survey_model();
		$title = (isset($user)
			? get_lang('AnswersOfUserPrefix') . ' ' . $user
			: get_lang('MyAnswers'));
		$answers_html = '<h4>' . htmlspecialchars($title) . '</h4>';
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
	
	function format_result($survey, & $category_total, $user = null)
	{
		$model = $survey->get_survey_model();
		$titles = array();
		$data = array();
		// TODO: display user name
		$title = (isset($user)
			? get_lang('ResultsOfUserPrefix') . ' ' . $user
			: get_lang('MyResults'));
		$result_html = '<h4>' . htmlspecialchars($title) . '</h4>';
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