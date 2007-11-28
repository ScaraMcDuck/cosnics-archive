<?php

/**
 * $Id$
 * @package repository
 */
/**
 * Class to display the banner of a HTML-page
 */
class Banner
{
	/**
	 * Constructor
	 */
	function Banner()
	{

	}
	/**
	 * Displays the banner.
	 */
	public function display()
	{
		echo $this->toHtml();
	}
	/**
	 * Creates the HTML output for the banner.
	 */
	public function toHtml()
	{
		$output = array ();
		$output[] = '<div id="header">  <!-- header section start -->';
		$output[] = '<div id="header1"> <!-- top of banner with institution name/hompage link -->';
		$output[] = '<div id="institution">';
		$output[] = '<a href="'.api_get_path(WEB_PATH).'index.php" target="_top">'.api_get_setting('siteName').'</a>';
		$output[] = '-';
		$output[] = '<a href="'.api_get_setting('InstitutionUrl').'" target="_top">'.api_get_setting('Institution').'</a>';
		$output[] = '</div>';

		//not to let the header disappear if there's nothing on the left
		$output[] = '<div class="clear">&nbsp;</div>';

		/*
		-----------------------------------------------------------------------------
			Plugins for banner section
		-----------------------------------------------------------------------------
		*/
		if (is_array($plugins['banner']))
		{
			foreach ($plugins['banner'] as $this_plugin)
			{
				include (api_get_path(PLUGIN_PATH)."$this_plugin/index.php");
			}
		}

		$output[] = '</div> <!-- end of #header1 -->';
		$output[] = '<div id="header2">';
		$output[] = '<div id="Header2Right">';
		$output[] = '<ul>';

		if ((api_get_setting('showonline', 'world') == "true" AND !$_SESSION['_uid']) OR (api_get_setting('showonline', 'users') == "true" AND $_SESSION['_uid']) OR (api_get_setting('showonline', 'course') == "true" AND $_SESSION['_uid'] AND $_SESSION['_cid']))
		{
			$statistics_database = Database :: get_statistic_database();
			$number = count(WhoIsOnline(api_get_user_id(), $statistics_database, 30));
			$online_in_course = who_is_online_in_this_course(api_get_user_id(), 30, $_course['id']);
			$number_online_in_course = count($online_in_course);
			$output[] = "<li>".get_lang('UsersOnline').": ";

			// Display the who's online of the platform
			if ((api_get_setting('showonline', 'world') == "true" AND !$_SESSION['_uid']) OR (api_get_setting('showonline', 'users') == "true" AND $_SESSION['_uid']))
			{
				$output[] = "<a href='".api_get_path(WEB_PATH)."whoisonline.php' target='_top'>".$number."</a>";
			}

			// Display brackets if who's online of the campus AND who's online in the course are active
			if (api_get_setting('showonline', 'users') == "true" AND api_get_setting('showonline', 'course') == "true" AND $_course)
			{
				$output[] = '(';
			}

			// Display the who's online for the course
			if ($_course AND api_get_setting('showonline', 'course') == "true")
			{
				$output[] = "<a href='".api_get_path(REL_CLARO_PATH)."online/whoisonlinecourse.php' target='_top'>$number_online_in_course ".get_lang('InThisCourse')."</a>";
			}

			// Display brackets if who's online of the campus AND who's online in the course are active
			if (api_get_setting('showonline', 'users') == "true" AND api_get_setting('showonline', 'course') == "true" AND $_course)
			{
				$output[] = ')';
			}

			$output[] = '</li>';
		}

		$output[] = '</ul>';
		$output[] = '</div>';
		$output[] = '<!-- link to campus home (not logged in)';
		$output[] = '<a href="'.api_get_path(WEB_PATH).'index.php" target="_top">'.api_get_setting('siteName').'</a>';
		$output[] = '-->';
		//not to let the empty header disappear and ensure help pic is inside the header
		$output[] = '<div class="clear">&nbsp;</div>';

		$output[] = '</div><!-- End of header 2-->';

		/*
		-----------------------------------------------------------------------------
			User section
		-----------------------------------------------------------------------------
		*/
		if ($_SESSION['_uid'])
		{

			$output[] = '<div id="header3"> <!-- start user section line with name, my course, my profile, scorm info, etc -->';

			$output[] = '<form method="get" action="'.api_get_path(WEB_PATH).'index.php" class="banner_links" target="_top">';
			$output[] = '<input type="hidden" name="logout" value="true"/>';
			$output[] = '<input type="hidden" name="uid" value="'.$_SESSION['_uid'].'"/>';
			$output[] = '<div id="logout">';
			$output[] = '<input type="submit" name="submit" value="'.get_lang("Logout").'" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'" class="logout"/>';
			$output[] = '</div>';
			$output[] = '</form>';

			$usermgr = new UserManager($_SESSION['_uid']);
			$user = $usermgr->get_user();

			$applications = Application::load_all();

			foreach ($applications as $application)
			{
				if ($GLOBALS['this_section'] == $application)
				{
					$link_class = 'class="here"';
				}
				else
				{
					$link_class = '';
				}

				if ($application == 'personal_messenger')
				{
					$pmmgr = PersonalMessengerDataManager :: get_instance();
					$count = $pmmgr->count_unread_personal_message_publications($user);
				}
				else
				{
					$count = 0;
				}

				$output[] = '<a '.$link_class.' href="'.api_get_path(WEB_PATH).'run.php?application='.$application.'" target="_top">';
				$output[] = get_lang(Application::application_to_class($application));
				$output[] = ($count > 0 ? '&nbsp;('.$count.')' : null);
				$output[] = '</a>&nbsp;';
			}

			if ($GLOBALS['this_section'] == "myrepository")
			{
				$link_class = 'class="here"';
			}
			else
			{
				$link_class = '';
			}

			$output[] = '<a '.$link_class.' href="'.api_get_path(WEB_PATH).'index_repository_manager.php" target="_top">';
			$output[] = get_lang('MyRepository');
			$output[] = '</a>&nbsp;';

			if ($GLOBALS['this_section'] == "myaccount")
			{
				$link_class = 'class="here"';
			}
			else
			{
				$link_class = '';
			}

			$output[] = '<a '.$link_class.' href="'.api_get_path(WEB_PATH).'index_user.php?go=account" target="_top">';
			$output[] = get_lang('ModifyProfile');
			$output[] = '</a>&nbsp;';

			if ($user->is_platform_admin())
			{
				if ($GLOBALS['this_section'] == "platform_admin")
				{
					$link_class = 'class="here"';
				}
				else
				{
					$link_class = '';
				}
				$output[] = '<a id="platform_admin" '.$link_class.' href="'.api_get_path(WEB_PATH).'index_admin.php" target="_top">';
				$output[] = get_lang('PlatformAdmin');
				$output[] = '</a>&nbsp;';
			}

			$output[] = '</div> <!-- end of header3 (user) section -->';
		}
		global $interbredcrump;
		if (isset ($nameTools) || is_array($interbredcrump))
		{
			if (!isset ($_SESSION['_uid']))
			{
				$output[] = " ";
			}
			else
			{
				$output[] = '&nbsp;&nbsp;<a href="'.api_get_path(WEB_PATH).'index.php" target="_top">'.api_get_setting('siteName').'</a>';
			}
		}

		// else we set the site name bold
		if (is_array($interbredcrump))
		{
			foreach ($interbredcrump as $breadcrumb_step)
			{
				$output[] = '&nbsp;&gt; <a href="'.$breadcrumb_step['url'].'" target="_top">'.$breadcrumb_step['name'].'</a>';
			}
		}

		if (isset ($nameTools))
		{
			if (!isset ($_SESSION['_uid']))
			{
				$output[] = '&nbsp;';
			}
			elseif (!defined('DOKEOS_HOMEPAGE') || !DOKEOS_HOMEPAGE)
			{
				global $noPHP_SELF;
				if ($noPHP_SELF)
				{
					$output[] = '&nbsp;&gt;&nbsp;'.$nameTools;
				}
				else
				{
					$output[] = ' &gt; <a href="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" target="_top">'.$nameTools.'</a>';
				}
			}
		}

		$output[] = '<div class="clear">&nbsp;</div>';

		$output[] = '</div> <!-- end of the whole #header section -->';
		$output[] = '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->';
		$output[] = '<!--   Begin Of script Output   -->';

		return implode("\n", $output);
	}
}
?>