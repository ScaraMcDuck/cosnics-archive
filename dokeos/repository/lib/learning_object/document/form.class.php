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
		$this->addElement('hidden', 'path', get_lang('Filename'));
		$this->addElement('file', 'filename', get_lang('Filename'));
		$this->setDefaults();
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['path'] = $lo->get_path();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		global $path;
		global $filename;
		global $main_upload_dir;
		$values = $this->exportValues();			
		$this->upload_document();
		if(preg_match('/\.x?html?$/', $filename) === 1)
			$document = new HtmlDocument();
		else
			$document = new Document();			
		$dataManager = RepositoryDataManager::get_instance();
		$document->set_owner_id($owner);
		$document->set_title($values['title']);
		$document->set_description($values['description']);
		$document->set_path($path);
		$document->set_filename($filename);
		$document->set_filesize(filesize($main_upload_dir.'/'.$path));
		$document->set_parent_id($values['category']);
		$document->create();
		return $document;
	}
	public function update_learning_object(& $object)
	{
		global $path;
		global $filename;
		global $main_upload_dir;
		$values = $this->exportValues();
		unlink(Configuration::get_instance()->get_parameter('general', 'upload_path').'/'.$values['path']);
		$this->upload_document();		
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_path($path);
		$object->set_filename($filename);
		$object->set_filesize(filesize($main_upload_dir.'/'.$path));
		$object->set_parent_id($values['category']);
		$object->update();
	}
	public function upload_document()
	{
		global $path;
		global $filename;
		global $main_upload_dir;
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
		move_uploaded_file($_FILES['filename']['tmp_name'], $main_upload_dir.'/'.$path);
	}
}
?>