<?php
require_once dirname(__FILE__).'/../lib/users_block.class.php';

class UsersLogin extends UsersBlock
{
	/**
	 * Runs this component and displays its output.
	 * This component is only meant for use within the home-component and not as a standalone item.
	 */
	function run()
	{
		return $this->as_html();
	}
	
	function as_html()
	{
		$html = array();
		
		$html[] = $this->display_header();
		$html[] = $this->display_anonymous_right_menu();
		$html[] = $this->display_footer();
		
		return implode("\n", $html);
	}
	
	function display_anonymous_right_menu()
	{
		global $loginFailed, $plugins;
		$html = array();
	
		if (!Authentication :: is_valid())
		{
			// TODO: New languageform
			//api_display_language_form();
			$html[] = $this->display_login_form();
	
			if ($loginFailed)
			{
				$html[] = $this->handle_login_failed();
			}
//			if ($this->get_platform_setting('allow_lostpassword') == 'true' OR $this->get_platform_setting('allow_registration') == 'true')
//			{
//				$html[] = '<div class="menusection"><span class="menusectioncaption">'.Translation :: get('MenuUser').'</span><ul class="menulist">';
//				if (get_setting('allow_registration') == 'true')
//				{
//					$html[] = '<li><a href="index_user.php?go=register">'.Translation :: get('Reg').'</a></li>';
//				}
//				if (get_setting('allow_lostpassword') == 'true')
//				{
//					//display_lost_password_info();
//				}
//				$html[] = '</ul></div>';
//			}
		}
		else
		{
			$user = $this->get_user();
			$html[] = '<table width="200">';
			$html[] = '<tr>';
			$html[] = '<td rowspan="2" width="70"><img src="'.$user->get_full_picture_url().'" /></td>';
			$html[] = '<td valign="top">'.$user->get_fullname().'</td>';
			$html[] = '</tr>';
			$html[] = '<tr>';
			$html[] = '<td></td>';
			$html[] = '</tr>';
			$html[] = '</table>';
			$html[] = '<a href="index.php?logout=true">Logout</a>';
		}
	
//		$html[] = '<div class="note">';
//		$html[] = '</div>';
		
		return implode("\n", $html);
	
	}
	
	function handle_login_failed()
	{
		$message = Translation :: get("InvalidId");
		if (PlatformSetting :: get('allow_registration', 'admin') == 'true')
			$message = Translation :: get("InvalidForSelfRegistration");
		return "<div id=\"login_fail\">".$message."</div>";
	}
	
	function display_login_form()
	{
		$form = new FormValidator('formLogin');
		$renderer =& $form->defaultRenderer();
		$renderer->setElementTemplate('<div>{label}&nbsp;<!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required --></div><div>{element}</div>');
		$renderer->setElementTemplate('<div>{element}</div>','submitAuth');
		$form->addElement('text','login',Translation :: get('UserName'),array('size'=>15));
		$form->addRule('login', Translation :: get('ThisFieldIsRequired'), 'required');
		$form->addElement('password','password',Translation :: get('Pass'),array('size'=>15));
		$form->addRule('password', Translation :: get('ThisFieldIsRequired'), 'required');
		$form->addElement('submit','submitAuth',Translation :: get('Ok'));
		return $form->toHtml();
	}
	
	function is_editable()
	{
		return false;
	}
	
	function is_hidable()
	{
		return false;
	}
	
	function is_deletable()
	{
		return false;
	}
}
?>