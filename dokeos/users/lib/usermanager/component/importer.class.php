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
		if (!api_is_platform_admin())
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
		$html[] = '<b>Code</b>;<b>Title</b>;<b>CourseCategory</b>;<b>Teacher</b>';
		$html[] = 'BIO0015;Biology;BIO;username';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>