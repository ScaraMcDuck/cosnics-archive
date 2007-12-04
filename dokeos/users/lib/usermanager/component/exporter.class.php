<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userexportform.class.php';
require_once dirname(__FILE__).'/../../../../common/export/export.class.php';

class UserManagerExporterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{

		if (!$this->get_user()->is_platform_admin())
		{
			$breadcrumbs = array();
			$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserCreateExport'));
			$this->display_header($breadcrumbs);
			Display :: display_error_message(get_lang("NotAllowed"));
			$this->display_footer();
			exit;
		}

		$form = new UserExportForm(UserExportForm :: TYPE_EXPORT, $this->get_url());

		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserCreateExport'));

		if($form->validate())
		{
			$export = $form->exportValues();
			$file_type = $export['file_type'];
			$result = parent :: retrieve_users();
			while($user = $result->next_result())
     		{
 	        	$data[] = array($user->get_user_id(),$user->get_lastname(),$user->get_firstname(),$user->get_fullname(),
 								$user->get_username(),$user->get_password(),$user->get_auth_source(),$user->get_email(),
 								$user->get_status(),$user->get_official_code(),$user->get_phone());
     		}
     		$success = $this->export_users($file_type,$data);
     		$this->redirect('url', get_lang($success ? 'UserCreatedExport' : 'UserNotCreatedExport'). '<br />', ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
		}
		else
		{
			$this->display_header($breadcrumbs);
			$form->display();
			$this->display_footer();
		}
	}

	function export_users($file_type, $data)
    {
    	$filename = 'export_users_'.date('Y-m-d_H-i-s');
    	$export = Export::factory($file_type,$filename);
    	$export->write_to_file($data);
    	return;
		switch($file_type)
		{
			case 'xml':
				Export::export_table_xml($data,$filename,'Contact','Contacts');
				break;
			case 'csv':
			    Export::export_table_csv($data,$filename);
			    break;
		}
    }
}
?>