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
				$answer_data = array();
				if (!$results->is_empty())
				{
					$question_count = 0;
					$result = $results->next_result();
					$answers = $result->get_result_answers();
					foreach ($answers as $answer)
					{
						$question = $answer->get_question();
						$answer_data[$question->get_id()] = $answer->get_answer(); 
					}
					$survey = $profile->get_survey();
					$sections = $survey->get_survey_sections();
					$pa_answers = LearningStyleSurvey :: get_proposition_agreement_answers();
					$category_total = array();
					foreach ($sections as $section)
					{
						$questions = $section->get_section_questions();
						foreach ($questions as $question)
						{
							echo $question->get_description();
							if ($survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_PROPOSITION_AGREEMENT)
							{
								$answer = $answer_data[$question->get_id()];
								echo '<p>', htmlspecialchars($pa_answers[$answer]), '</p>';
								$category_total[$question->get_question_category_id()] += $answer;
								$question_count++;
							}
							elseif ($survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_ANSWER_ORDERING)
							{
								$answers = $question->get_question_answers();
								$order = array();
								foreach ($answers as $answer)
								{
									$pos = $answer_data[$answer->get_id()];
									$order[$pos - 1] = $answer->get_description();
									$category_total[$answer->get_answer_category_id()] += $pos;
								}
								echo '<ol>';
								for ($i = 0; $i < count($order); $i++)
								{
									echo '<li>', $order[$i], '</li>';
								}
								echo '</ol>';
							}
						}
					}
					echo '<dl>';
					$categories = $survey->get_survey_categories();
					$titles = array();
					$data = array();
					foreach ($categories as $category)
					{
						$num = $category_total[$category->get_id()];
						echo '<dt>' . htmlspecialchars($category->get_title()) . '</dt>'
							. '<dd>' . $num . '</dd>';
						$titles[] = $category->get_title();
						$data[] = $num;
					}
					echo '</dl>';
					if (count($data) > 2)
					{
						require_once dirname(__FILE__).'/lib/PsychePolygon.class.php';
						if ($survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_PROPOSITION_AGREEMENT)
						{
							$max_value = $question_count * count($pa_answers);
						}
						elseif ($survey->get_survey_type() == LearningStyleSurvey :: SURVEY_TYPE_ANSWER_ORDERING)
						{
							// TODO: calculate maximum for each category
							$max_value = max($data); 
						}
						$p = new PsychePolygon($titles, $data, 0, $max_value);
						$img = $p->create_image(PsychePolygon::IMAGE_TYPE_PNG);
						echo '<div><img src="data:', $img['mime_type'], ';base64,',
							base64_encode($img['data']), '"',
							' width="', $img['width'], '" height="', $img['height'], '"/></div>';
					}
				}
				else
				{
					$form = new LearningStyleSurveyResultForm($profile, 'survey', 'post', $this->get_url(array(self :: PARAM_SURVEY_PROFILE_ID => $profile_id)));
					if ($form->validate())
					{
						$object = $form->create_learning_object();
						// TODO
						var_dump($object);
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
}
?>