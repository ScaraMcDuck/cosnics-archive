<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';
require_once Path::get_repository_path().'/lib/complex_learning_object_item.class.php';

class WikiToolPageCreatorComponent extends WikiToolComponent
{
    private $pub;
	function run()
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
        $this->display_header($trail);
		$object = $_GET['object'];
		$this->pub = new LearningObjectRepoViewer($this, 'wiki_page', true,RepoViewer :: SELECT_MULTIPLE, WikiTool ::ACTION_CREATE_PAGE);
        $this->pub->set_parameter('wiki_id', $_GET['wiki_id']);

		if(!isset($object))
		{
			$html[] = '<p><a href="' . $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $this->pub->as_html();

        echo implode("\n",$html);
		}
		else
        {   $cloi = new ComplexLearningObjectItem(array(ComplexLearningObjectItem :: PROPERTY_REF => $_GET['object'], ComplexLearningObjectItem ::PROPERTY_PARENT => $this->pub->get_parameter('wiki_id'), ComplexLearningObjectItem ::PROPERTY_USER_ID => $this->pub->get_user_id(), ComplexLearningObjectItem ::PROPERTY_DISPLAY_ORDER => '1'));
            $cloi->create();
            $action_bar = $this->get_toolbar();
            echo '<br />' . $action_bar->as_html();
            echo '<p>Page created</p>';
		}


		$this->display_footer();
	}

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Create'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'wiki_id' => $this->pub->get_parameter('wiki_id'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('wiki tool'));
		return $action_bar;
	}
}
?>