<?php

require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../publisher/learning_object_publisher.class.php';

class ToolIntroductionPublisherComponent extends ToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), Translation :: get('PublishIntroductionText')));
		/*$pub = new LearningObjectPublisher($this, 'introduction', true);

		$html[] = '<p><a href="' . $this->get_url() . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();*/

		$object = $_GET['object'];

		$pub = new LearningObjectRepoViewer($this, 'introduction', true);
		$pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_PUBLISH_INTRODUCTION);

		if(!isset($object))
		{
			$html[] = '<p><a href="' . $this->get_url() . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			$dm = WeblcmsDataManager :: get_instance();
			$do = $dm->get_next_learning_object_publication_display_order_index($this->get_course_id(),$this->get_tool_id(),0);

			$obj = new LearningObject();
			$obj->set_id($object);
			$pub = new LearningObjectPublication(null, $obj, $this->get_course_id(), $this->get_tool_id(), 0, array(), array(), 0, 0, Session :: get_user_id(), time(), time(), 0, $do, false, 0);
			$pub->create();

			$parameters = $this->get_parameters();
			$parameters['tool_action'] = null;

			$this->redirect(Translation :: get('IntroductionPublished'), (false), $parameters);
		}

		$this->display_header($trail);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>