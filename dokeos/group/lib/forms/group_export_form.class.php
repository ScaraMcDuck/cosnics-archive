<?php
/**
 * @package groups.lib.groupmanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../group.class.php';
require_once dirname(__FILE__).'/../group_data_manager.class.php';

class GroupExportForm extends FormValidator {

	const TYPE_EXPORT = 1;

	private $current_tag;
	private $current_value;
	private $group;
	private $groups;


	/**
	 * Creates a new GroupImportForm
	 * Used to export groups to a file
	 */
    function GroupExportForm($form_type, $action) {
    	parent :: __construct('group_export', 'post', $action);

		$this->form_type = $form_type;
		$this->failedcsv = array();
		if ($this->form_type == self :: TYPE_EXPORT)
		{
			$this->build_exporting_form();
		}
    }

    function build_exporting_form()
    {
    	$this->addElement('select', 'file_type', Translation :: get('OutputFileType'),Export::get_supported_filetypes(array('ical', 'csv', 'pdf')));
		//$this->addElement('submit', 'group_export', Translation :: get('Ok'));
		
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Ok'), array('class' => 'positive'));
		//$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		
		$this->setDefaults(array('file_type'=>'xml'));

    }
}
?>