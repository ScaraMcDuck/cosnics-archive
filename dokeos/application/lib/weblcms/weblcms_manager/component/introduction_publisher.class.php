<?php

require_once dirname(__FILE__) . '/../weblcms.class.php';
require_once dirname(__FILE__) . '/../weblcms_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';

class WeblcmsIntroductionPublisherComponent extends WeblcmsComponent
{
	function run()
	{
		/*if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}*/
		
		$trail = new BreadcrumbTrail();
		$pub = new LearningObjectPublisher($this, 'description', true);
		
		$html[] = '<p><a href="' . $this->get_url(array(Weblcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE)) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();
		
		$this->display_header($trail);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>