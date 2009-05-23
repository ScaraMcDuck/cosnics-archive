<?php
require_once dirname(__FILE__).'/../menu_manager.class.php';
require_once dirname(__FILE__).'/../menu_manager_component.class.php';

class MenuManagerMiniBarComponent extends MenuManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$root_item_condition = new EqualityCondition(MenuItem :: PROPERTY_CATEGORY, 0);
		$root_items = $this->retrieve_menu_items($root_item_condition);

		global $this_section;
		$html = array();

		$html[] = '<div class="minidropnav">';

		//$html[] = '<ul class="bracket_left"><li></li></ul>';

		while ($root_item = $root_items->next_result())
		{
			$application = $root_item->get_application();

			if (isset($application))
			{
				if($application == 'root')
				{
					$url = 'index.php';
				}
				else
				{
					$url = 'run.php?application='.$root_item->get_application().$root_item->get_extra();
				}

				$options = '';
			}
			else
			{
				$url = $root_item->get_url();
				$options = 'target="about:blank"';
			}

			$subitem_condition = new EqualityCondition(MenuItem :: PROPERTY_CATEGORY, $root_item->get_id());
			$subitems = $this->retrieve_menu_items($subitem_condition);
			$count = $subitems->size();
			if ($count > 0)
			{
				$is_current = false;
				$html_sub = array();

				if ($count > 0)
				{
					$html_sub[] = '<ul>';

					while ($subitem = $subitems->next_result())
					{
						$application = $subitem->get_application();

						if (isset($application))
						{
							if($application == 'root')
							{
								$url = 'index.php';
							}
							else
							{
								$url = 'run.php?application='.$subitem->get_application().$subitem->get_extra();
							}

							$options = '';
						}
						else
						{
							$url = $subitem->get_url();
							$options = 'target="about:blank"';
						}
						$html_sub[] = '<li><a' . ($subitems->is_last() ? ' class="last_subitem"' : '') . ' href="'. $url .'" ' . $options . '>'. $subitem->get_title() .'</a></li>';

						if ($this_section == $subitem->get_section())
						{
							$is_current = true;
						}
					}

					$html_sub[] = '</ul>';
				}

				$html[] = '<ul>';
				$html[] = '<li' . ($is_current ? ' class="current"' : '') . '><a href="#" '. ($is_current ? 'class="current"' : '') .'  ' . $options . '>'. $root_item->get_title() .'</a>';

				$html[] = implode("\n", $html_sub);

				$html[] = '</li>';
				$html[] = '</ul>';
			}
			else
			{
				$html[] = '<ul>';
				$html[] = '<li' . ($this_section == $root_item->get_section() ? ' class="current"' : '') . '><a href="'.$url.'" '. ($this_section == $root_item->get_section() ? 'class="current"' : '') .' ' . $options . '>'. $root_item->get_title() .'</a></li>';
				$html[] = '</ul>';
			}

//			$html[] = '<ul>';
//			$html[] = '<li class="bullet">&bull;</li>';
//			$html[] = '</ul>';
		}

		$user = $this->get_user();
		if (isset($user))
		{
			$html[] = '<ul class="admin">';
			$html[] = '<li class="admin' . ($this_section == 'user' ? ' current' : '') . '"><a' . ($this_section == 'user' ? ' class="current""' : '') . ' href="index_user.php?go=account">' . Translation :: get('MyAccount') . '</a></li>';
			$html[] = '</ul>';

//			$html[] = '<ul class="admin">';
//			$html[] = '<li class="bullet">&bull;</li>';
//			$html[] = '</ul>';

			$html[] = '<ul class="admin">';
			$html[] = '<li class="admin' . ($this_section == 'repository' ? ' current' : '') . '"><a' . ($this_section == 'repository' ? ' class="current""' : '') . ' href="index_repository_manager.php">' . Translation :: get('Repository') . '</a></li>';
			$html[] = '</ul>';

//			$html[] = '<ul class="admin">';
//			$html[] = '<li class="bullet">&bull;</li>';
//			$html[] = '</ul>';

			if ($user->is_platform_admin())
			{
				$html[] = '<ul class="admin">';
				$html[] = '<li class="admin' . ($this_section == 'admin' ? ' current' : '') . '"><a' . ($this_section == 'admin' ? ' class="current""' : '') . ' href="index_admin.php">' . Translation :: get('Administration') . '</a></li>';
				$html[] = '</ul>';

//				$html[] = '<ul class="admin">';
//				$html[] = '<li class="bullet">&bull;</li>';
//				$html[] = '</ul>';
			}

			$html[] = '<ul class="admin">';
			$html[] = '<li class="admin"><a href="index.php?logout=true">' . Translation :: get('Logout') . '</a></li>';
			$html[] = '</ul>';

			//$html[] = '<ul class="bracket_right"><li></li></ul>';
		}

		$html[] = '</div>';

		return implode("\n", $html);
	}
}
?>