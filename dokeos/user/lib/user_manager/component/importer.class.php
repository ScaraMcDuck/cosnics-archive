<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/user_import_form.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class UserManagerImporterComponent extends UserManagerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserCreateCsv')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
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
		$html[] = '<b>lastname</b>;<b>firstname</b>;<b>username</b>;<b>password</b>;<b>email</b>;<b>language</b>;<b>status</b>;<b>active</b>;<b>official_code</b>;<b>phone</b>;<b>activation_date</b>;<b>expiration_date</b>;<b>auth_source</b>';
		$html[] = '<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		$html[] = '<p>'. Translation :: get('XMLMustLookLike') .' ('. Translation :: get('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;ISO-8859-1&quot;?&gt;';
		$html[] = '';
		$html[] = '&lt;Contacts&gt;';
		$html[] = '    &lt;Contact&gt;';
		$html[] = '        <b>&lt;lastname&gt;xxx&lt;/lastname&gt;</b>';
		$html[] = '        <b>&lt;firstname&gt;xxx&lt;/firstname&gt;</b>';
		$html[] = '        <b>&lt;username&gt;xxx&lt;/username&gt;</b>';
		$html[] = '';
		$html[] = '        <b>&lt;password&gt;xxx&lt;/password&gt;</b>';
		$html[] = '        <b>&lt;email&gt;xxx&lt;/email&gt;</b>';
		$html[] = '        <b>&lt;language&gt;xxx&lt;/language&gt;</b>';
		$html[] = '';
		$html[] = '        <b>&lt;status&gt;xxx&lt;/status&gt;</b>';
		$html[] = '        <b>&lt;active&gt;xxx&lt;/active&gt;</b>';
		$html[] = '';
		$html[] = '        <b>&lt;official_code&gt;xxx&lt;/official_code&gt;</b>';
		$html[] = '        <b>&lt;phone&gt;xxx&lt;/phone&gt;</b>';
		$html[] = '';
		$html[] = '        <b>&lt;activation_date&gt;YYYY-MM-DD HH:MM:SS&lt;/activation_date&gt;</b>';
		$html[] = '        <b>&lt;expiration_date&gt;YYYY-MM-DD HH:MM:SS&lt;/expiration_date&gt;</b>';
		$html[] = '';
		$html[] = '        <b>&lt;auth_source&gt;xxx&lt;/auth_source&gt;</b>';
		$html[] = '';
		$html[] = '    &lt;/Contact&gt;';
		$html[] = '&lt;/Contacts&gt;';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>