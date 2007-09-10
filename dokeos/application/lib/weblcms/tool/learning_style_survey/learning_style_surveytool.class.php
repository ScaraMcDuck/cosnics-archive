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
					'label' => get_lang('BrowserTitle'),
					'href' => $this->get_url(array('mode' => 0)),
					'display' => RepositoryUtilities::TOOLBAR_DISPLAY_ICON_AND_LABEL
				),
				array(
					'img' => api_get_path(WEB_CODE_PATH).'/img/browser.gif',
					'label' => get_lang('TakeSurvey'),
					'href' => $this->get_url(array('mode' => 2)),
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
		elseif ($_SESSION[get_class()]['mode'] == 2)
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
				$result = $dm->retrieve_learning_objects('learning_style_survey_result', $condition);
				if (!$result->is_empty())
				{
					Display :: display_error_message(get_lang('SurveyAlreadyTaken'));
					// TODO: review answers
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
		else
		{
			$this->display_header();
			echo $toolbar;
			echo $this->perform_requested_actions();
			$browser = new LearningStyleSurveyBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>