<?php

/**
 * @package admin.component
 */
require_once dirname(__FILE__).'/../admin.class.php';
require_once dirname(__FILE__).'/../admincomponent.class.php';
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
		
		if (!api_is_platform_admin())
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
	
	function get_application_platform_admin_sections()
	{
		$html = array();
		
		foreach ($this->get_application_platform_admin_links() as $application_links)
		{
			$html[] = '<div class="admin_section">';
			$html[] = '<div class="main"><img src="'. $this->get_web_code_path() .'/img/admin_'. $application_links['application']['class'] .'.gif" border="0" style="vertical-align: middle;" alt="' . get_lang($application_links['application']['name']) . '" title="' . get_lang($application_links['application']['name']) . '"/></div>';
			$html[] = '<div class="actions">';
			if (count($application_links['links']))
			{
				$html[] = '<ul>';
				foreach ($application_links['links'] as $link)
				{
					$html[] = '<li><a href="'.$link['url'] .'">'.$link['name'].'</a></li>';
				}
				$html[] = '</ul>';
			}
			$html[] = '</div>';
			$html[] = '</div>';
		}
		
		return implode("\n", $html);
	}
}
?>