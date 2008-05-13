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
	private $breadcrumbtrail;
	
	/**
	 * Constructor
	 */
	function Banner($breadcrumbtrail)
	{
		$this->breadcrumbtrail = $breadcrumbtrail;
	}
	
	function get_setting($variable, $application)
	{
		return PlatformSetting :: get($variable, $application);
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
		$output[] = '<a href="'.$this->get_path(WEB_PATH).'index.php" target="_top">'.$this->get_setting('site_name', 'admin').'</a>';
		$output[] = '-';
		$output[] = '<a href="'.$this->get_setting('institution_url', 'admin').'" target="_top">'.$this->get_setting('institution', 'admin').'</a>';
		$output[] = '</div>';

		//not to let the header disappear if there's nothing on the left
		$output[] = '<div class="clear">&nbsp;</div>';
		$output[] = '</div> <!-- end of #header1 -->';
		$output[] = '<div id="header2">';
		$output[] = '<div id="Header2Right">';
		//$output[] = '<ul>';
		
		// TODO: Reimplement "Who is online ?" 

		//$output[] = '</ul>';
		$output[] = '</div>';
		$output[] = '<!-- link to campus home (not logged in)';
		$output[] = '<a href="'.$this->get_path(WEB_PATH).'index.php" target="_top">' . $this->get_setting('site_name', 'admin') . '</a>';
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

			$output[] = '<form method="get" action="'.$this->get_path(WEB_PATH).'index.php" class="banner_links" target="_top">';
			$output[] = '<input type="hidden" name="logout" value="true"/>';
			$output[] = '<input type="hidden" name="uid" value="'.$_SESSION['_uid'].'"/>';
			$output[] = '<div id="logout">';
			$output[] = '<input type="submit" name="submit" value="'.Translation :: get("Logout").'" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'" class="logout"/>';
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

				$output[] = '<a '.$link_class.' href="'.$this->get_path(WEB_PATH).'run.php?application='.$application.'" target="_top">';
				$output[] = Translation :: get(Application::application_to_class($application));
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

			$output[] = '<a '.$link_class.' href="'.$this->get_path(WEB_PATH).'index_repository_manager.php" target="_top">';
			$output[] = Translation :: get('MyRepository');
			$output[] = '</a>&nbsp;';

			if ($GLOBALS['this_section'] == "myaccount")
			{
				$link_class = 'class="here"';
			}
			else
			{
				$link_class = '';
			}

			$output[] = '<a '.$link_class.' href="'.$this->get_path(WEB_PATH).'index_user.php?go=account" target="_top">';
			$output[] = Translation :: get('ModifyProfile');
			$output[] = '</a>&nbsp;';

			if ($user->is_platform_admin())
			{
				if ($GLOBALS['this_section'] == "admin")
				{
					$link_class = 'class="here"';
				}
				else
				{
					$link_class = '';
				}
				$output[] = '<a id="admin" '.$link_class.' href="'.$this->get_path(WEB_PATH).'index_admin.php" target="_top">';
				$output[] = Translation :: get('PlatformAdmin');
				$output[] = '</a>&nbsp;';
			}

			$output[] = '</div> <!-- end of header3 (user) section -->';
		}
		
		$breadcrumbtrail = $this->breadcrumbtrail;
		// TODO: Add this CSS to the css-files
		$output[] = '<div id="breadcrumbtrail">';
		$output[] = $breadcrumbtrail->render();
		$output[] = '</div>';
		
		// TODO: Check whether we still need anything from the old breadcrumb-generating code
		
//		global $interbreadcrumb;
//		if (isset ($nameTools) || is_array($interbreadcrumb))
//		{
////			if (!isset ($_SESSION['_uid']))
////			{
////				$output[] = " ";
////			}
////			else
////			{
//				$output[] = '&nbsp;&nbsp;<a href="'.$this->get_path(WEB_PATH).'index.php" target="_top">'.$this->get_setting('site_name', 'admin').'</a>';
////			}
//		}
//
//		// else we set the site name bold
//		if (is_array($interbreadcrumb))
//		{
//			foreach ($interbreadcrumb as $breadcrumb_step)
//			{
//				$output[] = '&nbsp;&gt; <a href="'.$breadcrumb_step['url'].'" target="_top">'.$breadcrumb_step['name'].'</a>';
//			}
//		}
//
//		if (isset ($nameTools))
//		{
//			if (!isset ($_SESSION['_uid']))
//			{
//				$output[] = '&nbsp;';
//			}
//			elseif (!defined('DOKEOS_HOMEPAGE') || !DOKEOS_HOMEPAGE)
//			{
//				global $noPHP_SELF;
//				if ($noPHP_SELF)
//				{
//					$output[] = '&nbsp;&gt;&nbsp;'.$nameTools;
//				}
//				else
//				{
//					$output[] = ' &gt; <a href="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" target="_top">'.$nameTools.'</a>';
//				}
//			}
//		}

		$output[] = '<div class="clear">&nbsp;</div>';

		$output[] = '</div> <!-- end of the whole #header section -->';
		$output[] = '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->';
		$output[] = '<!--   Begin Of script Output   -->';

		return implode("\n", $output);
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
}
?>