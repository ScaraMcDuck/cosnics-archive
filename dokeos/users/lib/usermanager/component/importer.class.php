<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userimportform.class.php';

/**
 * Weblcms component allows the use to create a course
 */
class UserManagerImporterComponent extends UserManagerComponent
{	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if (!$this->get_user()->is_platform_admin())
		{
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserCreateCsv'));
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new UserImportForm(UserImportForm :: TYPE_IMPORT, $this->get_url());
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserCreateCsv'));
		
		if($form->validate())
		{
			$success = $form->import_users();
			$this->redirect(null, get_lang($success ? 'UserCreatedCsv' : 'UserNotCreatedCsv'). '<br />' .$form->get_failed_csv(), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
		}
		else
		{
			$this->display_header($breadcrumbs);
			$form->display();
			$this->display_extra_information();
			$this->display_footer();
		}
	}
	
	function display_extra_information()
	{
		$html = array();
		$html[] = '<p>'. get_lang('CSVMustLookLike') .' ('. get_lang('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '<b>LastName</b>;<b>FirstName</b>;UserName;Password;AuthSource;<b>Email</b>;OfficialCode;PhoneNumber;Status;Courses;ClassName';
		$html[] = '<b>xxx</b>;<b>xxx</b>;xxx;xxx;platform;<b>xxx</b>;xxx;xxx;user/teacher;xxx1|xxx2|xxx3;xxx';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		$html[] = '<p>'. get_lang('XMLMustLookLike') .' ('. get_lang('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;ISO-8859-1&quot;?&gt;';
		$html[] = '';
		$html[] = '&lt;Contacts&gt;';
		$html[] = '    &lt;Contact&gt;';
		$html[] = '        <b>&lt;LastName&gt;xxx&lt;/LastName&gt;</b>';
		$html[] = '        <b>&lt;FirstName&gt;xxx&lt;/FirstName&gt;</b>';
		$html[] = '        &lt;UserName&gt;xxx&lt;/UserName&gt;';
		$html[] = '';
		$html[] = '        &lt;Password&gt;xxx&lt;/Password&gt;';
		$html[] = '        &lt;AuthSource&gt;platform&lt;/AuthSource&gt;';
		$html[] = '        <b>&lt;Email&gt;xxx&lt;/Email&gt;</b>';
		$html[] = '        &lt;OfficialCode&gt;xxx&lt;/OfficialCode&gt;';
		$html[] = '';
		$html[] = '        &lt;PhoneNumber&gt;xxx&lt;/PhoneNumber&gt;';
		$html[] = '        &lt;Status&gt;user/teacher&lt;/Status&gt;';
		$html[] = '        &lt;Courses&gt;xxx1|xxx2|xxx3&lt;/Courses&gt;';
		$html[] = '        &lt;ClassName&gt;class 1&lt;/ClassName&gt;';
		$html[] = '';
		$html[] = '    &lt;/Contact&gt;';
		$html[] = '&lt;/Contacts&gt;';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>