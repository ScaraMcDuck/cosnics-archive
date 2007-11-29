<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userexportform.class.php';
require_once dirname(__FILE__).'/../../../common/export/export.class.php';

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
			$udm = $this->udm;
    		$udm = UsersDataManager :: get_instance();
    		$udm->retrieve_users();
    		$data = array();
			$success = $this->export_users($file_type,$data);
			$this->redirect('url', get_lang($success ? 'UserCreatedExport' : 'UserNotCreatedExport'). '<br />' .$form->get_failed_csv(), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
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