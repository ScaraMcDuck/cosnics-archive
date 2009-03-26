<?php

/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../admin_search_form.class.php';
require_once dirname(__FILE__).'/../../admin_rights.class.php';
/**
 * Admin component
 */
class AdminBrowserComponent extends AdminManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PlatformAdmin')));
				
		if (!AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		
		$links = $this->get_application_platform_admin_links();

		$this->display_header($trail);
		echo $this->get_application_platform_admin_tabs($links);
		echo '<div class="clear"></div>';
		echo '<br /><br /><br /><br />';
		echo '<div class="clear"></div>';
		//echo $this->get_application_platform_admin_sections($links);
		$this->display_footer();
	}
	
	/**
	 * Returns an HTML representation of the actions.
	 * @return string $html HTML representation of the actions.
	 */
	function get_application_platform_admin_tabs($links)
	{
		$html = array();
		
		$html[] = '<div id="tabs">';
		$html[] = '<ul>';
		
		// Render the tabs
		
		$index = 0;
		foreach ($links as $application_links)
		{
			$index++;
			
			if (count($application_links['links']))
			{
				$html[] = '<li><a href="#tabs-'. $index .'">';
				$html[] = '<span class="category">';
				$html[] = '<img src="'. Theme :: get_image_path() . 'place_mini_' . $application_links['application']['class'] .'.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>';
				$html[] = '<span class="title">' . $application_links['application']['name'] . '</span>';
				$html[] = '</span>';
				$html[] = '</a></li>';
			}
		}
		
		$html[] = '</ul>';
		
		$index = 0;
		foreach ($links as $application_links)
		{
			$index++;
			
			if (count($application_links['links']))
			{
				$html[] = '<div class="tab" id="tabs-'. $index .'">';
				
				$html[] = '<a class="prev"></a>';
				
				$html[] = '<div class="scrollable">';
				$html[] = '<div class="items">';
				foreach ($application_links['links'] as $link)
				{
					if($link['confirm'])
					{
						$onclick = 'onclick = "return confirm(\'' . $link['confirm'] . '\')"';
					}
					$html[] = '<div class="action"><a href="'.$link['url'] .'" ' . $onclick . '><img src="'. Theme :: get_image_path() .'action_'. $link['action'] .'.png" alt="'. $link['name'] .'" title="'. $link['name'] .'"/><br />'.$link['name'].'</a></div>';
				}
				$html[] = '</div>';
				$html[] = '</div>';
				
				$html[] = '<a class="next"></a>';
				
				$html[] = '<div class="clear"></div>';
				
				if (isset($application_links['search']))
				{
					$search_form = new AdminSearchForm($this, $application_links['search'], $index);
					$html[] = $search_form->display();
				}
				
				$html[] = '</div>';
			}
		}
		
		
		$html[] = '</div>';
		
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/admin_ajax.js');







		return implode("\n", $html);
	}

	/**
	 * Returns an HTML representation of the actions.
	 * @return string $html HTML representation of the actions.
	 */
	function get_application_platform_admin_sections($links)
	{		
		$html = array();
		$search_form_index = 0;
		$margin_index = 0;
		foreach ($links as $application_links)
		{
			$search_form_index++;
			
			if (count($application_links['links']))
			{
				$margin_index++;
				
				$html[] = '<div class="admin"'. ($margin_index % 2 == 0 ? ' style="margin-right: 0px;"' : '') .'>';
				$html[] = '<div class="admin_header">';
				$html[] = '<span class="category">';
				$html[] = '<img src="'. Theme :: get_image_path() . 'place_mini_' . $application_links['application']['class'] .'.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>';
				$html[] = '<span class="title">' . $application_links['application']['name'] . '</span>';
				$html[] = '</span>';
				
				if (isset($application_links['search']))
				{
					$search_form = new AdminSearchForm($this, $application_links['search'], $search_form_index);
					$html[] = $search_form->display();
				}
				else
				{
					$html[] = '<div class="admin_search">';
					$html[] = '</div>';
				}
				$html[] = '<div class="clear"></div>';
				$html[] = '</div>';
				
				$html[] = '<div class="admin_section">';
				$html[] = '<div class="actions">';
				foreach ($application_links['links'] as $link)
				{
					if($link['confirm'])
					{
						$onclick = 'onclick = "return confirm(\'' . $link['confirm'] . '\')"';
					}
					$html[] = '<div class="action"><a href="'.$link['url'] .'" ' . $onclick . '><img src="'. Theme :: get_image_path() .'action_'. $link['action'] .'.png" alt="'. $link['name'] .'" title="'. $link['name'] .'"/><br />'.$link['name'].'</a></div>';
				}
				$html[] = '<div class="clear"></div>';
				$html[] = '</div>';
				$html[] = '<div class="clear"></div>';
				
				$html[] = '</div>';
				$html[] = '</div>';
			}
		}

		return implode("\n", $html);
	}
}
?>