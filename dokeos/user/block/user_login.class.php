<?php
require_once dirname(__FILE__).'/../lib/user_block.class.php';

class UserLogin extends UserBlock
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
			if (PlatformSetting :: get('allow_registration', 'user') || PlatformSetting :: get('allow_password_retrieval', 'user'))
			{
				$html[] = '<br />';
				//$html[] = '<div class="menusection"><span class="menusectioncaption">'.Translation :: get('MenuUser').'</span><ul class="menulist">';
				if (PlatformSetting :: get('allow_registration', 'user'))
				{
					$links[] = '<a href="index_user.php?go=register">'.Translation :: get('Reg').'</a>';
				}
				if (PlatformSetting :: get('allow_password_retrieval', 'user'))
				{
					//display_lost_password_info();
					$links[] = '<a href="index_user.php?go=reset_password">'.Translation :: get('ResetPassword').'</a>';
				}
				
				$html[] = implode(' - ', $links);
				//$html[] = '</ul></div>';
			}
		}
		else
		{
			$user = $this->get_user();
			
			$html[] = '<img src="'.$user->get_full_picture_url().'" />';
			$html[] = '<br />';
			$html[] = '<br />';
			$html[] = $user->get_fullname() . '<br />';
			$html[] = $user->get_email() . '<br />';
			$html[] = '<br />';
			$html[] = '<a href="index.php?logout=true">Logout</a>';
			
			/*if(PlatformSetting :: get('page_after_login') == 'weblcms')
			{
				//header('Location: run.php?application=weblcms');
				header('Location: index_repository_manager.php');
			}*/
		}
	
//		$html[] = '<div class="note">';
//		$html[] = '</div>';
		
		return implode("\n", $html);
	
	}
	
	function handle_login_failed()
	{
		$message = Translation :: get("InvalidId");
		if (PlatformSetting :: get('allow_registration', 'user') == 'true')
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