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
		//$this->addRule('filename',get_lang('DiskQuotaExceeded'),'disk_quota');
		$this->addElement('upload_or_create','');
		$this->add_submit_button();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->addElement('hidden', 'path');
		if(preg_match('/\.x?html?$/',$object->get_path()) === 1)
			$this->addElement('html_editor', 'html_content', get_lang('HtmlDocument'));
		else
			$this->addElement('file', 'file', get_lang(FileName));
		$this->setDefaults();		
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['path'] = $lo->get_path();
			$defaults['html_content'] = file_get_contents((Configuration::get_instance()->get_parameter('general', 'upload_path')).'/'.$lo->get_path());
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$main_upload_dir = Configuration::get_instance()->get_parameter('general', 'upload_path');
		if($values['choice'] === '1')
		{
			$filename = strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$values['title']));
			$path = api_get_user_id().'/'.$filename.'.html';
			$i = 1;
			while (file_exists($main_upload_dir.'/'.$path))
			{
				$filename = $filename.$i;
				$path = api_get_user_id().'/'.$filename.'.html';
				$i++;
			}
			$create_file = fopen($main_upload_dir.'/'.$path, 'w');
			fwrite ($create_file, $values['html_content']);
			fclose($create_file);
		}
		else
		{
			$file = $_FILES['file'];
			$filename = strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$file['name']));
			$path = api_get_user_id().'/'.$filename;
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
			move_uploaded_file($_FILES['file']['tmp_name'], $main_upload_dir.'/'.$path);
		}
		$dataManager = RepositoryDataManager::get_instance();
		$document = new Document();
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
		$values = $this->exportValues();
		$main_upload_dir = Configuration::get_instance()->get_parameter('general', 'upload_path');
		unlink($main_upload_dir.'/'.$values['path']);
		if(isset($values['html_content']))
		{
			$filename = strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$values['title']));
			$path = api_get_user_id().'/'.$filename.'.html';
			$i = 1;
			while (file_exists($main_upload_dir.'/'.$path))
			{
				$filename = $filename.$i;
				$path = api_get_user_id().'/'.$filename.'.html';
				$i++;
			}
			$create_file = fopen($main_upload_dir.'/'.$path, 'w');
			fwrite ($create_file, $values['html_content']);
			fclose($create_file);
		}
		else
		{
			$file = $_FILES['file'];
			$filename = strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$file['name']));
			$path = api_get_user_id().'/'.$filename;
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
			move_uploaded_file($_FILES['file']['tmp_name'], $main_upload_dir.'/'.$path);
		}
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_path($path);
		$object->set_filename($filename);
		$object->set_filesize(filesize($main_upload_dir.'/'.$path));
		$object->set_parent_id($values['category']);
		$object->update();
	}
}
?>