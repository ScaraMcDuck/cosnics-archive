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
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PlatformAdmin')));

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$this->display_header($trail);
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
		$search_form_index = 0;
		foreach ($this->get_application_platform_admin_links() as $application_links)
		{
			$search_form_index++;
			$html[] = '<div class="admin_section">';
			$html[] = '<div class="main"><img src="'. Theme :: get_img_path() . $application_links['application']['class'] .'.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/><br />'. $application_links['application']['name'] .'</div>';
			$html[] = '<div class="actions">';
			if (count($application_links['links']))
			{
				foreach ($application_links['links'] as $link)
				{
					$html[] = '<div class="action"><a href="'.$link['url'] .'"><img src="'. Theme :: get_img_path() .'action_'. $link['action'] .'.png" alt="'. $link['name'] .'" title="'. $link['name'] .'"/><br />'.$link['name'].'</a></div>';
				}
			}
			$html[] = '</div>';
			if (isset($application_links['search']))
			{
				$search_form = new AdminSearchForm($this, $application_links['search'], $search_form_index);
				$html[] = $search_form->display();
			}
			$html[] = '</div>';
		}

		return implode("\n", $html);
	}
}
?>