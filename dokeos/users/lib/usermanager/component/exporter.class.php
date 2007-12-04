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
     			$user_array[USER::PROPERTY_USER_ID] = $user->get_user_id();
     			$user_array[USER::PROPERTY_LASTNAME] = $user->get_lastname();
     			$user_array[USER::PROPERTY_FIRSTNAME] = $user->get_firstname();
     			$user_array[USER::PROPERTY_USERNAME] = $user->get_username();
     			$user_array[USER::PROPERTY_AUTH_SOURCE] = $user->get_auth_source();
     			$user_array[USER::PROPERTY_EMAIL] = $user->get_email();
     			$user_array[USER::PROPERTY_STATUS] = $user->get_status();
     			$user_array[USER::PROPERTY_PHONE] = $user->get_phone();
     			$user_array[USER::PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
     			$user_array[USER::PROPERTY_LANGUAGE] = $user->get_language();
     			var_dump($user_array);
     			$data[] = $user_array; 
 	        }
			$this->export_users($file_type,$data);
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
    }
}
?>