<?php

require_once dirname(__FILE__) . '/../survey_builder_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . '/lib/learning_object/survey/survey.class.php';

class SurveyBuilderBrowserComponent extends SurveyBuilderComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS)), Translation :: get('Repository')));
		$trail->add(new Breadcrumb($this->get_url(array(ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id())), $this->get_root_lo()->get_title()));
		$trail->add_help('repository survey builder');

		$this->display_header($trail);
		$survey = $this->get_root_lo();
		$action_bar = $this->get_action_bar($survey);

		echo '<br />';
		if($action_bar)
		{
			echo $action_bar->as_html();
			echo '<br />';
		}

		$display = LearningObjectDisplay :: factory($this->get_root_lo());
		echo $display->get_full_html();

		echo '<br />';
		echo $this->get_creation_links($survey);
		echo '<div class="clear">&nbsp;</div><br />';

		echo $this->get_clo_table_html();

		$this->display_footer();
	}
}

?>
