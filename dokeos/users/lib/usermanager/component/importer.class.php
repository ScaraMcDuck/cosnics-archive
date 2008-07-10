<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../user_import_form.class.php';

class UserManagerImporterComponent extends UserManagerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserCreateCsv')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new UserImportForm(UserImportForm :: TYPE_IMPORT, $this->get_url(), $this->get_user());
		
		if($form->validate())
		{
			$success = $form->import_users();
			$this->redirect('url', Translation :: get($success ? 'UserCreatedCsv' : 'UserNotCreatedCsv'). '<br />' .$form->get_failed_csv(), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_extra_information();
			$this->display_footer();
		}
	}
	
	function display_extra_information()
	{
		$html = array();
		$html[] = '<p>'. Translation :: get('CSVMustLookLike') .' ('. Translation :: get('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '<b>LastName</b>;<b>FirstName</b>;<b>UserName</b>;<b>Password</b>;<b>Email</b>;<b>Language</b>;<b>Status</b>';
		$html[] = '<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		$html[] = '<p>'. Translation :: get('XMLMustLookLike') .' ('. Translation :: get('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;ISO-8859-1&quot;?&gt;';
		$html[] = '';
		$html[] = '&lt;Contacts&gt;';
		$html[] = '    &lt;Contact&gt;';
		$html[] = '        <b>&lt;LastName&gt;xxx&lt;/LastName&gt;</b>';
		$html[] = '        <b>&lt;FirstName&gt;xxx&lt;/FirstName&gt;</b>';
		$html[] = '        <b>&lt;UserName&gt;xxx&lt;/UserName&gt;</b>';
		$html[] = '';
		$html[] = '        <b>&lt;Password&gt;xxx&lt;/Password&gt;</b>';
		$html[] = '        <b>&lt;Email&gt;xxx&lt;/Email&gt;</b>';
		$html[] = '        <b>&lt;Language&gt;xxx&lt;/Language&gt;</b>';
		$html[] = '';
		$html[] = '        <b>&lt;Status&gt;user/teacher&lt;/Status&gt;</b>';
		$html[] = '';
		$html[] = '    &lt;/Contact&gt;';
		$html[] = '&lt;/Contacts&gt;';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>