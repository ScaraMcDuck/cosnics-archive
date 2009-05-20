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
		$trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
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
			$this->redirect(Translation :: get($success ? 'UserCreatedCsv' : 'UserNotCreatedCsv'). '<br />' .$form->get_failed_csv(), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
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
		$html[] = '<b>lastname</b>;<b>firstname</b>;<b>email</b>;<b>username</b>;password;auth_source;<b>official_code</b>;phone;status;language;active;activation_date;expiration_date';
		$html[] = '<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;xxx;platform/ldap;xxx;<b>xxx</b>;1/5;xxx;1/0;date/0;date/0';
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
		$html[] = '        &lt;password&gt;xxx&lt;/password&gt;';
		$html[] = '        <b>&lt;email&gt;xxx&lt;/email&gt;</b>';
		$html[] = '        &lt;language&gt;xxx&lt;/language&gt;';
		$html[] = '';
		$html[] = '        &lt;status&gt;1/5&lt;/status&gt;';
		$html[] = '        &lt;active&gt;1/0&lt;/active&gt;';
		$html[] = '';
		$html[] = '        <b>&lt;official_code&gt;xxx&lt;/official_code&gt;</b>';
		$html[] = '        &lt;phone&gt;xxx&lt;/phone&gt;';
		$html[] = '';
		$html[] = '        &lt;activation_date&gt;YYYY-MM-DD HH:MM:SS/0&lt;/activation_date&gt;';
		$html[] = '        &lt;expiration_date&gt;YYYY-MM-DD HH:MM:SS/0&lt;/expiration_date&gt;';
		$html[] = '';
		$html[] = '        &lt;auth_source&gt;platform/ldap&lt;/auth_source&gt;';
		$html[] = '';
		$html[] = '    &lt;/Contact&gt;';
		$html[] = '&lt;/Contacts&gt;';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>