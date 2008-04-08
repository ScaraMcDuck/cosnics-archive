<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/adminuserbrowser/adminuserbrowsertable.class.php';
require_once dirname(__FILE__).'/../../usermenu.class.php';

class UserManagerAdminUserBrowserComponent extends UserManagerComponent
{
	private $firstletter;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->firstletter = $_GET[UserManager :: PARAM_FIRSTLETTER];
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('UserList'));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$menu = $this->get_menu_html();
		$output = $this->get_user_html();
		
		$this->display_header($breadcrumbs, true);
		echo $menu;
		echo $output;
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new AdminUserBrowserTable($this, null, null, $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_menu_html()
	{
		$extra_items = array ();
		if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = Translation :: get('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = $search;
		}
		else
		{
			$search_url = null;
		}
		
		$temp_replacement = '__FIRSTLETTER__';
		$url_format = $this->get_url(array (UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS, UserManager :: PARAM_FIRSTLETTER => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$user_menu = new UserMenu($this->firstletter, $url_format, $extra_items);
		
		if (isset ($search_url))
		{
			$user_menu->forceCurrentUrl($search_url, true);
		}
		
		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $user_menu->render_as_tree();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}

	function get_condition()
	{
		$search_conditions = $this->get_search_condition();
		$condition = null;
		if (isset($this->firstletter))
		{
			$conditions = array();
			$conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $this->firstletter. '%');
			$conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, chr(ord($this->firstletter)+1). '%');
			$conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, chr(ord($this->firstletter)+2). '%');
			$condition = new OrCondition($conditions);
			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}
		return $condition;
	}
}
?>