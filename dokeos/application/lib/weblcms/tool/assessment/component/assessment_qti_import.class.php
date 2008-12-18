<?php
require_once Path::get_repository_path().'lib/import/learning_object_import.class.php';

class AssessmentToolQtiImportComponent extends AssessmentToolComponent {

	function run()
	{
		if (!$this->is_allowed(EDIT_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$form = $this->build_importing_form();
		if ($form->validate())
		{
			//import
			$aid = $this->import_qti($form);
			echo $aid;
			$this->redirect(null, null, false, array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH, 'object' => $aid));
		}
		else
		{
			$trail = new BreadCrumbTrail();
			$this->display_header($trail);
		
			$this->action_bar = $this->get_toolbar();
			echo $this->action_bar->as_html();
			echo $form->toHtml();
			$this->display_footer();
		}
	}
    
    function build_importing_form()
    {
    	$url = $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_IMPORT_QTI)); 
    	$form = new FormValidator('qti_import', 'post', $url);
    	$form->addElement('html', '<b>Import assessment from QTI</b><br/><br/>');
    	$form->addElement('html', '<em>'.Translation::get('FileMustContainAllAssessmentXML').'</em>');
    	$form->addElement('file', 'file', Translation :: get('FileName'));
    	
    	$allowed_upload_types = array ('zip');
		$form->addRule('file', Translation :: get('OnlyZipAllowed'), 'filetype', $allowed_upload_types);
    	
		$form->addElement('submit', 'submit', Translation :: get('Import'));
		return $form;
    }
    
    function import_qti($form)
    {
    	$values = $form->exportValues();
    	$file = $_FILES['file'];
    	$user = $this->get_user();
    	//TODO: change categories
    	$category = 0;
    	
    	$importer = LearningObjectImport ::factory('qti', $file, $user, $category);
    	$result = $importer->import_learning_object();
    	return $result->get_id();
    }
    
    function import_groups()
    {
    	$values = $this->exportValues();
    	$this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
    	return true;
    }
  
}
?>