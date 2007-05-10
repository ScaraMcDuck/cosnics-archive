<?php

/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../admin.class.php';
require_once dirname(__FILE__).'/../admincomponent.class.php';
require_once dirname(__FILE__).'/../adminsearchform.class.php';
/**
 * Admin component
 */
class AdminBrowserComponent extends AdminComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => '', 'name' => get_lang('PlatformAdmin'));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->display_header($breadcrumbs);
		echo $this->get_application_platform_admin_sections();
		$this->display_footer();
	}
	
	/**
	 * Returns an HTML representation of the actions.
	 * @return string $html HTML representation of the actions.
	 */
	function get_application_platform_admin_sections()
	{
		$html = array();
		
		foreach ($this->get_application_platform_admin_links() as $application_links)
		{
			$html[] = '<div class="admin_section">';
			$html[] = '<div class="main"><img src="'. $this->get_web_code_path() .'img/admin_'. $application_links['application']['class'] .'.gif" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/><br />'. $application_links['application']['name'] .'</div>';
			$html[] = '<div class="actions">';
			if (count($application_links['links']))
			{
				foreach ($application_links['links'] as $link)
				{
					$html[] = '<div class="action"><a href="'.$link['url'] .'"><img src="'. $this->get_web_code_path() .'img/admin_action_'. $link['action'] .'.gif" alt="'. $link['name'] .'" title="'. $link['name'] .'"/><br />'.$link['name'].'</a></div>';
				}
			}
			$html[] = '</div>';
			if (isset($application_links['search']))
			{
				$search_form = new AdminSearchForm($this, $application_links['search']);
				$html[] = $search_form->display();
			}
			$html[] = '</div>';
		}
		
		return implode("\n", $html);
	}
}
?>