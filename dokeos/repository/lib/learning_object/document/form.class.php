<?php
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
define('HTML_DOCUMENT','1');
define('MAIN_UPLOAD_DIR',Configuration::get_instance()->get_parameter('general', 'upload_path'));
/**
 * @package learningobject.document
 */
class DocumentForm extends LearningObjectForm
{
	/**
	 * Create a new form to handle a document
	 */
	public function DocumentForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	/**
	 * Build a form to create a document
	 */
	public function build_creation_form()
	{
		parent :: build_creation_form();
		$this->setDefaults();
		$this->addElement('upload_or_create','');
		//TODO: add Rule to check if diskquota doesn't exceed when creating a HTML-document
		//TODO: add Rule to check if diskquota doesn't exceed when uploading a document
		//TODO: add Rule to check if a file was actually uploaded when the 'upload' option was selected
		//TODO: add Rule to check if a HTML-content was filled in when the 'create' option was selected
		$this->add_submit_button();
	}
	/**
	 * Build a form to edit a document
	 */
	public function build_modification_form($object)
	{
		parent :: build_editing_form($object);
		if($this->is_html_document($object->get_path()))
		{
			$this->addElement('html_editor', 'html_content', get_lang('HtmlDocument'));
			//TODO: add option to upload & overwrite a HTML-document
			//TODO: add Rule to check if diskquota doesn't exceed when creating a HTML-document
			//TODO: add Rule to check if diskquota doesn't exceed when uploading a document
		}
		else
		{
			$this->addElement('file', 'file', get_lang('FileName'));
			//TODO: add Rule to check if diskquota doesn't exceed when uploading a document
		}
		$this->setDefaults();
		$this->add_submit_button();
	}
	/**
	 * Set the default values
	 */
	public function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if (isset ($object) && $this->is_html_document($object->get_path()))
		{
			$defaults['html_content'] = file_get_contents(MAIN_UPLOAD_DIR.'/'.$object->get_path());
		}
		$defaults['choice'] = 0;
		parent :: setDefaults($defaults);
	}
	/**
	 * Create a document from submitted the form values
	 */
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		if($values['choice'] === HTML_DOCUMENT)
		{
			$filename = $values['title'].'.html';
			$filename = $this->create_unique_filename(api_get_user_id(),$filename);
			$path = api_get_user_id().'/'.$filename;
			$this->check_file();
			$create_file = fopen(MAIN_UPLOAD_DIR.'/'.$path, 'w');
			fwrite ($create_file, $values['html_content']);
			fclose($create_file);
		}
		else
		{
			$filename = $this->create_unique_filename(api_get_user_id(),$_FILES['file']['name']);
			$path = api_get_user_id().'/'.$filename;
			$target = MAIN_UPLOAD_DIR.'/'.$path;
			move_uploaded_file($_FILES['file']['tmp_name'],$target);
		}
		$dataManager = RepositoryDataManager::get_instance();
		$document = new Document();
		$document->set_owner_id($owner);
		$document->set_title($values['title']);
		$document->set_description($values['description']);
		$document->set_path($path);
		$document->set_filename($filename);
		$document->set_filesize(filesize(MAIN_UPLOAD_DIR.'/'.$path));
		$document->set_parent_id($values['category']);
		$document->create();
		return $document;
	}
	/**
	 * Update a learning object from the submitted form values
	 */
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();
		$path = $object->get_path();
		$filename = $object->get_filename();
		if(isset($values['html_content']))
		{
			$create_file = fopen(MAIN_UPLOAD_DIR.'/'.$object->get_path(), 'w');
			fwrite ($create_file, $values['html_content']);
			fclose($create_file);
		}
		else
		{
			if(strlen($_FILES['file']['name']) > 0)
			{
				unlink(MAIN_UPLOAD_DIR.'/'.$object->get_path());
				$filename = $this->create_unique_filename(api_get_user_id(),$_FILES['file']['name']);
				$path = api_get_user_id().'/'.$filename;
				move_uploaded_file($_FILES['file']['tmp_name'], MAIN_UPLOAD_DIR.'/'.$path);
			}
		}
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_path($path);
		$object->set_filename($filename);
		$object->set_filesize(filesize(MAIN_UPLOAD_DIR.'/'.$object->get_path()));
		$object->set_parent_id($values['category']);
		$object->update();
	}
	/**
	 * Check if a file is a html-document
	 */
	private function is_html_document($path)
	{
		return 	(preg_match('/\.x?html?$/',$path) === 1);
	}
	/**
	 * Create a valid filename
	 * @param string $desired_filename desired filename
	 * @return string A valid filename
	 */
	private function create_valid_filename($desired_filename)
	{
		return strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$desired_filename));
	}
	/**
	 * Create a unique path
	 */
	private function create_unique_filename($path,$filename)
	{
		$new_filename = $this->create_valid_filename($filename);
		$index = 0;
		while(file_exists(MAIN_UPLOAD_DIR.'/'.$path.'/'.$new_filename))
		{
			$file_parts = explode('.',$filename);
			$new_filename = array_shift($file_parts).($index++).'.'.implode($file_parts);
		}
		return $new_filename;
	}
}
?>