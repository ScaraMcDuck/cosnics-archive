<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/learning_style_surveybrowser.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learning_object/learning_style_survey_result/learning_style_survey_result_form.class.php';

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
			if ($profile_id && ($profile = RepositoryDataManager :: get_instance()->retrieve_learning_object($profile_id, 'learning_style_survey_profile')))
			{
				$form = new LearningStyleSurveyResultForm($profile, 'survey', 'post', $this->get_url(array(self :: PARAM_SURVEY_PROFILE_ID => $profile_id)));
				if ($form->validate())
				{
					// TODO
					//$object = $form->create_learning_object();
					var_dump($form->exportValues());
				}
				else {
					$form->display();
				}
			}
			else
			{
				// TODO
				echo '<p>Please add <code>&amp;', self :: PARAM_SURVEY_PROFILE_ID, '=<em>$profile_id</em></code> to the URL.</p>';
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