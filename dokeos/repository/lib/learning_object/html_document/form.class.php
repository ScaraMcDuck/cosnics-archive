<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class HtmlDocumentForm extends LearningObjectForm
{
	public function HtmlDocumentForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	public function build_create_form()
	{
		parent :: build_create_form();
		$this->addElement('text', 'filename', get_lang('Filename'));
		$this->addElement('html_editor', 'htmldoc', get_lang('HtmlDocument'));
		$this->add_submit_button();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->setDefaults();
		$this->addElement('text', 'path', get_lang('Path'));
		$this->addElement('text', 'filename', get_lang('Filename'));
		$this->addElement('html_editor', 'htmldoc', get_lang('HtmlDocument'));
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		$main_upload_dir = Configuration::get_instance()->get_parameter('general', 'upload_path');
		if (isset ($lo))
		{
			$defaults['path'] = $lo->get_path();
			$defaults['filename'] = $lo->get_filename();
			$defaults['htmldoc'] = file_get_contents($main_upload_dir.'/'.$lo->get_path());
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		
		$values = $this->exportValues();
		$file = $values['filename'];
		$file_base = strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$file));
		$filename = $file_base.'.html';
		$path = api_get_user_id().'/'.$filename;
		$main_upload_dir = Configuration::get_instance()->get_parameter('general', 'upload_path');
		$i = 1;
		while (file_exists($main_upload_dir.'/'.$path))
		{
			$file_base = substr($filename, 0, strlen($filename)-5);
			$file_ext = substr($filename, strlen($filename)-5, strlen($filename));
			$filename = $file_base.$i.$file_ext;
			$path = api_get_user_id().'/'.$filename;
			$i++;
		}
		$create_file = fopen($main_upload_dir.'/'.$path, 'w');
		fwrite ($create_file, $values['htmldoc']);
		fclose($create_file);
		$filesize = filesize($main_upload_dir.'/'.$path);
		$dataManager = RepositoryDataManager::get_instance();
		$document = new HtmlDocument();
		$document->set_owner_id($owner);
		$document->set_title($values['title']);
		$document->set_description($values['description']);
		$document->set_path($path);
		$document->set_filename($filename);
		$document->set_filesize($filesize);
		$document->set_category_id($values['category']);
		$document->create();
		return $document;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();	
		$main_upload_dir = Configuration::get_instance()->get_parameter('general', 'upload_path');
		$update_file = fopen($main_upload_dir.'/'.$values['path'], 'w');
		fwrite($update_file, $values['htmldoc']);
		fclose($update_file);
		$filesize = filesize($main_upload_dir.'/'.$values['path']);
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_path($values['path']);
		$object->set_filename($values['filename']);
		$object->set_filesize($filesize);
		$object->set_category_id($values['category']);
		$object->update();
	}
}
?>