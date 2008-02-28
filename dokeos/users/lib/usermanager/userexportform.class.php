<?php
/**
 * @package users.lib.usermanager
 */
require_once dirname(__FILE__).'/../../../common/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../usersdatamanager.class.php';

class UserExportForm extends FormValidator {

	const TYPE_EXPORT = 1;

	private $current_tag;
	private $current_value;
	private $user;
	private $users;


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
    	$this->addElement('select', 'file_type', Translation :: get_lang('OutputFileType'),Export::get_supported_filetypes());
		$this->addElement('submit', 'user_export', Translation :: get_lang('Ok'));
		$this->setDefaults(array('file_type'=>'csv'));

    }
}
?>