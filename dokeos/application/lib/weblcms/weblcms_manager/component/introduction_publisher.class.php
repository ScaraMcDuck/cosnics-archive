<?php

require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_repo_viewer.class.php';

class WeblcmsManagerIntroductionPublisherComponent extends WeblcmsManagerComponent
{
	function run()
	{
		/*if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}*/

		$trail = new BreadcrumbTrail();
		/*$pub = new LearningObjectPublisher($this, 'introduction', true);

		$html[] = '<p><a href="' . $this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE)) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();*/

		$object = $_GET['object'];
		$pub = new LearningObjectRepoViewer($this, 'introduction', true);

		if(!isset($object))
		{
			$html[] = '<div class="clear">&nbsp;</div><p><a href="' . $this->get_url(array('go' => 'course_viewer'), array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			$dm = WeblcmsDataManager :: get_instance();
			$do = $dm->get_next_learning_object_publication_display_order_index($this->get_course_id(),$this->get_tool_id(),0);

			$obj = new LearningObject();
			$obj->set_id($object);
			$pub = new LearningObjectPublication(null, $obj, $this->get_course_id(), 'introduction', 0, array(), array(), 0, 0, Session :: get_user_id(), time(), time(), 0, $do, false, 0);
			$pub->create();

			$parameters = $this->get_parameters();
			$parameters['go'] = WeblcmsManager :: ACTION_VIEW_COURSE;

			$this->redirect(Translation :: get('IntroductionPublished'), (false), $parameters);
		}

		$this->display_header($trail);
		echo '<div class="clear"></div><br />';
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>