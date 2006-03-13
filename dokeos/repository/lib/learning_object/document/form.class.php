<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class DocumentForm extends LearningObjectForm
{
	public function DocumentForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	public function build_create_form()
	{
		parent :: build_create_form();
		$this->addElement('file', 'filename', get_lang('Filename'));
		//$this->addRule('filename',get_lang('DiskQuotaExceeded'),'disk_quota');
		$this->add_submit_button();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->setDefaults();
		$this->addElement('text', 'path', get_lang('Path'));
		$this->addElement('text', 'filename', get_lang('Filename'));
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['path'] = $lo->get_path();
			$defaults['filename'] = $lo->get_filename();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$file = $_FILES['filename'];
		$filename = strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$file['name']));
		$path = api_get_user_id().'/'.$filename;
		$main_upload_dir = Configuration::get_instance()->get_parameter('general', 'upload_path');
		$i = 1;
		$file_base = $filename;
		$file_ext = '';
		$dot_pos = strrpos($filename, '.');
		if ($dot_pos != FALSE)
		{
			$file_base = substr($filename, 0, $dot_pos);
			$file_ext = substr($filename, $dot_pos);
		}			
		while (file_exists($main_upload_dir.'/'.$path))
		{	
			$filename = $file_base.$i.$file_ext;
			$path = api_get_user_id().'/'.$filename;
			$i++;
		}
		if(preg_match('/^\.x?html?$/', $file_ext) === 1)
			$document = new HtmlDocument();
		else
		{
			$document = new Document();
			var_dump(preg_match('/^\.x?html?$/', $file_ext));
		}
		move_uploaded_file($_FILES['filename']['tmp_name'], $main_upload_dir.'/'.$path);
		$dataManager = RepositoryDataManager::get_instance();
		$document->set_owner_id($owner);
		$document->set_title($values['title']);
		$document->set_description($values['description']);
		$document->set_path($path);
		$document->set_filename($filename);
		$document->set_filesize(filesize($main_upload_dir.'/'.$path));
		$document->set_category_id($values['category']);
		$document->create();
		return $document;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();	
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_path($values['path']);
		$object->set_filename($values['filename']);
		$object->set_category_id($values['category']);
		$object->update();
	}
}
?>