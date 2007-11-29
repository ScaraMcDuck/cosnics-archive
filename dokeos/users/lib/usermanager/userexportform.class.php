<?php
/**
 * @package users.lib.usermanager
 */
require_once dirname(__FILE__).'/../../../main/inc/lib/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../usersdatamanager.class.php';

class UserExportForm extends FormValidator {
	
	const TYPE_EXPORT = 1;
	
	private $current_tag;
	private $current_value;
	private $user;
	private $users;
	private $udm;

	/**
	 * Creates a new UserImportForm 
	 * Used to export users to a file
	 */
    function UserExportForm($form_type, $action) {
    	parent :: __construct('user_export', 'post', $action);
    	
		$this->form_type = $form_type;
		$this->failedcsv = array();
		if ($this->form_type == self :: TYPE_EXPORT)
		{
			$this->build_exporting_form();
		}
    }
    
    function build_exporting_form()
    {
    	$this->addElement('radio', 'file_type', get_lang('OutputFileType'), 'XML','xml');
		$this->addElement('radio', 'file_type', null, 'CSV','csv');
		$this->addElement('submit', 'user_export', get_lang('Ok'));
		$this->setDefaults(array('file_type'=>'csv'));
		
    }
    
    function export_users()
    {
		$export = $form->exportValues();
		$file_type = $export['file_type'];
		$udm = $this->udm;
    	$udm = UsersDataManager :: get_instance();
    	$udm->retrieve_users();
		
		$filename = 'export_users_'.date('Y-m-d_H-i-s');
		//$res = api_sql_query($sql,__FILE__,__LINE__);
		$data = array();
		//while($user = mysql_fetch_array($res,MYSQL_ASSOC))
		//{
		//	$data[] = $user	;
		//}
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