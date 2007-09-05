<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/learning_style_surveybrowser.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/repositoryutilities.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyTool extends RepositoryTool
{
	// TODO: Remove
	function is_allowed()
	{
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
		if (isset($_GET['admin']))
		{
			$_SESSION[get_class()]['admin'] = $_GET['admin'];
		}
		if ($_SESSION[get_class()]['admin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$this->display_header();
			echo RepositoryUtilities::build_toolbar(
				array(
					array(
						'img' => api_get_path(WEB_CODE_PATH).'/img/browser.gif',
						'label' => get_lang('BrowserTitle'),
						'href' => $this->get_url(array('admin' => 0)),
						'display' => RepositoryUtilities::TOOLBAR_DISPLAY_ICON_AND_LABEL
					)
				),
				null,
				'margin-bottom: 1em;'
			);
			$pub = new LearningObjectPublisher($this, 'learning_style_survey_profile', true);
			echo $pub->as_html();
			$this->display_footer();
		}
		else
		{
			$this->display_header();
			if($this->is_allowed(ADD_RIGHT))
			{
				echo RepositoryUtilities::build_toolbar(
					array(
						array(
							'img' => api_get_path(WEB_CODE_PATH).'/img/publish.gif',
							'label' => get_lang('Publish'),
							'href' => $this->get_url(array('admin' => 1)),
							'display' => RepositoryUtilities::TOOLBAR_DISPLAY_ICON_AND_LABEL
						)
					),
					null,
					'margin-bottom: 1em;'
				);
			}
			echo $this->perform_requested_actions();
			$browser = new LearningStyleSurveyBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>