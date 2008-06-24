<?php

/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../admin_search_form.class.php';
require_once dirname(__FILE__).'/../../configuration_form.class.php';
/**
 * Admin component
 */
class AdminConfigurerComponent extends AdminComponent
{
	private $application;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$application = $this->application = Request :: get('application');
		if (!isset($application))
		{
			$application = $this->application = 'admin';
		}
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get(Application :: application_to_class($application)) . '&nbsp;' . Translation :: get('Settings')));

		if (!$this->get_user()->is_platform_admin())
		{
			Display :: display_not_allowed();
			exit;
		}
		
		$form = new ConfigurationForm($application, 'config', 'post', $this->get_url(array(Admin :: PARAM_APPLICATION => $application)));
		
		if($form->validate())
		{
			$success = $form->update_configuration();
			$this->redirect('url', Translation :: get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), ($success ? false : true), array(Admin :: PARAM_ACTION => Admin :: ACTION_CONFIGURE_PLATFORM, Admin :: PARAM_APPLICATION => $application));
		}
		else
		{
			$this->display_header($trail);
			echo $this->get_applications();
			$form->display();
			$this->display_footer();
		}
	}
	
	function get_applications()
	{
		$application = $this->application;
		
		$html = array();
		
		$html[] = '
<script type="text/javascript">jQuery(document).ready(function($){

$(\'div.application, div.application_current\').css(\'fontSize\', \'37%\');
$(\'div.application, div.application_current\').css(\'width\', \'40px\');
$(\'div.application, div.application_current\').css(\'height\', \'32px\');
$(\'div.application, div.application_current\').css(\'margin-top\', \'19px\');
$(\'div.application, div.application_current\').css(\'margin-bottom\', \'19px\');

$(\'div.application, div.application_current\').mouseover(function(){
	$(this).css(\'fontSize\', \'75%\');
	$(this).css(\'width\', \'80px\');
	$(this).css(\'height\', \'68px\');
	$(this).css(\'margin-top\', \'0px\');
	$(this).css(\'margin-bottom\', \'0px\');
	$(this).css(\'background-color\', \'#EBEBEB\');
	$(this).css(\'border\', \'1px solid #c0c0c0\');
})

$(\'div.application\').mouseout(function(){
	$(this).css(\'fontSize\', \'37%\');
	$(this).css(\'width\', \'40px\');
	$(this).css(\'height\', \'32px\');
	$(this).css(\'margin-top\', \'19px\');
	$(this).css(\'margin-bottom\', \'19px\');
	$(this).css(\'background-color\', \'#FFFFFF\');
	$(this).css(\'border\', \'1px solid #EBEBEB\');
})

$(\'div.application_current\').mouseout(function(){
	$(this).css(\'fontSize\', \'37%\');
	$(this).css(\'width\', \'40px\');
	$(this).css(\'height\', \'32px\');
	$(this).css(\'margin-top\', \'19px\');
	$(this).css(\'margin-bottom\', \'19px\');
	$(this).css(\'background-color\', \'#EBEBEB\');
	$(this).css(\'border\', \'1px solid #c0c0c0\');
})

})</script>';
		
		$html[] = '<div class="configure">';
			
		foreach ($this->get_application_platform_admin_links() as $application_links)
		{
			if (isset($application) && $application == $application_links['application']['class'])
			{
				$html[] = '<div class="application_current">';
			}
			else
			{
				$html[] = '<div class="application">';
			}
			$html[] = '<a href="'. $this->get_url(array(Admin :: PARAM_ACTION => Admin :: ACTION_CONFIGURE_PLATFORM, ADMIN :: PARAM_APPLICATION => $application_links['application']['class'])) .'">';
			$html[] = '<img src="'. Theme :: get_img_path() . 'place_' . $application_links['application']['class'] .'.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/><br />'. $application_links['application']['name'];
			$html[] = '</a>';
			$html[] = '</div>';
		}
		
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';

		return implode("\n", $html);
	}
}
?>