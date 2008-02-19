<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/learning_style_surveybrowser.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learningobjectform.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learningobjectdisplay.class.php';
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
			$this->disallow();
			return;
		}
		if (isset($_GET['mode']))
		{
			$_SESSION[get_class()]['mode'] = $_GET['mode'];
		}
		$toolbar = RepositoryUtilities::build_toolbar(
			array(
				array(
					'img' => $this->get_parent()->get_path(WEB_IMG_PATH).'browser.gif',
					'label' => get_lang('Browse'),
					'href' => ($_SESSION[get_class()]['mode'] != 0
						? $this->get_url(array('mode' => 0))
						: null),
					'display' => RepositoryUtilities::TOOLBAR_DISPLAY_ICON_AND_LABEL
				),
				array(
					'img' => $this->get_parent()->get_path(WEB_IMG_PATH).'publish.gif',
					'label' => get_lang('Publish'),
					'href' => ($_SESSION[get_class()]['mode'] != 1
						? $this->get_url(array('mode' => 1))
						: null),
					'display' => RepositoryUtilities::TOOLBAR_DISPLAY_ICON_AND_LABEL
				)
			),
			null,
			'margin-bottom: 1em;'
		);
		$toolbar .= $this->perform_requested_actions();
		if ($_SESSION[get_class()]['mode'] == 1)
		{
			if ($this->is_allowed(ADD_RIGHT))
			{
				$this->display_header();
				echo $toolbar;
				require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
				$pub = new LearningObjectPublisher($this, 'learning_style_survey_profile', true);
				echo $pub->as_html();
				$this->display_footer();
			}
			else
			{
				$this->disallow();
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
					// TODO: is this the correct right?
					if ($this->is_allowed(ADD_RIGHT))
					{
						$this->display_header();
						echo $toolbar;
						// TODO: filter on users or groups somehow?
						$condition = new EqualityCondition(LearningStyleSurveyResult :: PROPERTY_PROFILE_ID, $profile_id);
						$results = $dm->retrieve_learning_objects('learning_style_survey_result', $condition);
						if (!$results->is_empty())
						{
							while ($result = $results->next_result())
							{
								$this->review_result($result, true);
							}
						}
						else
						{
							Display :: display_normal_message(get_lang('NoSurveysTakenSoFar'));
						}
						$this->display_footer();
					}
					else
					{
						$this->disallow();
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
						$this->display_header();
						echo $toolbar;
						$result = $results->next_result();
						$this->review_result($result);
						$this->display_footer();
					}
					else
					{
						$object = new AbstractLearningObject('learning_style_survey_result', api_get_user_id());
						$extra = array(
							LearningStyleSurveyResultForm :: KEY_PROFILE => $profile
						);
						$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'survey', 'post', $this->get_url(array(self :: PARAM_SURVEY_PROFILE_ID => $profile_id)), $extra, $profile);
						if ($form->validate())
						{
							$object = $form->create_learning_object();
							$this->redirect(null, get_lang('SurveyAnswersStored'), false, array(self :: PARAM_SURVEY_PROFILE_ID => $profile_id));
						}
						else {
							$this->display_header();
							echo $toolbar;
							$form->display();
							$this->display_footer();
						}
					}
				}
			}
			else
			{
				$this->display_header();
				echo $toolbar;
				$browser = new LearningStyleSurveyBrowser($this);
				echo $browser->as_html();
				$this->display_footer();
			}
		}
	}
	
	private function review_result($result, $as_admin = false)
	{
		$display = LearningObjectDisplay :: factory($result);
		$display->set_administrative_view($as_admin);
		echo $display->get_full_html();
	}
}
?>