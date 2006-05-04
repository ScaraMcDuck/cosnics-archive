<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/document.class.php';
require_once dirname(__FILE__).'/../../../../claroline/inc/lib/formvalidator/Rule/DiskQuota.php';

/**
 * @package repository.learningobject
 * @subpackage document
 */
class DocumentForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('upload_or_create','upload_or_create');
		$this->addFormRule(array($this,'check_document_form'));
		//TODO: add Rule to check if a HTML-content was filled in when the 'create' option was selected
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$object = $this->get_learning_object();
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
			$this->addRule('file',get_lang('DiskQuotaExceeded'),'disk_quota');
			//TODO: add Rule to check if diskquota doesn't exceed when uploading a document
		}
	}
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if (isset ($object) && $this->is_html_document($object->get_path()))
		{
			$defaults['html_content'] = file_get_contents($this->get_upload_path().'/'.$object->get_path());
		}
		$defaults['choice'] = 0;
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$owner = $this->get_owner_id();
		$values = $this->exportValues();
		if($values['choice'])
		{
			$filename = $values[Document :: PROPERTY_TITLE].'.html';
			$filename = $this->create_unique_filename($owner,$filename);
			$path = $owner.'/'.$filename;
			$create_file = fopen($this->get_upload_path().'/'.$path, 'w');
			fwrite ($create_file, $values['html_content']);
			fclose($create_file);
		}
		else
		{
			$filename = $this->create_unique_filename($owner,$_FILES['file']['name']);
			$path = $owner.'/'.$filename;
			$target = $this->get_upload_path().'/'.$path;
			move_uploaded_file($_FILES['file']['tmp_name'],$target);
		}
		$object = new Document();
		$object->set_path($path);
		$object->set_filename($filename);
		$object->set_filesize(filesize($this->get_upload_path().'/'.$path));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$path = $object->get_path();
		$filename = $object->get_filename();
		$owner = $object->get_owner_id();
		if(isset($values['html_content']))
		{
			$create_file = fopen($this->get_upload_path().'/'.$object->get_path(), 'w');
			fwrite ($create_file, $values['html_content']);
			fclose($create_file);
		}
		else if(strlen($_FILES['file']['name']) > 0)
		{
			unlink($this->get_upload_path().'/'.$object->get_path());
			$filename = $this->create_unique_filename($owner, $_FILES['file']['name']);
			$path = $owner.'/'.$filename;
			move_uploaded_file($_FILES['file']['tmp_name'], $this->get_upload_path().'/'.$path);
		}
		$object->set_path($path);
		$object->set_filename($filename);
		$object->set_filesize(filesize($this->get_upload_path().'/'.$object->get_path()));
		return parent :: update_learning_object();
	}
	/**
	 * Checks if a file is an HTML document.
	 */
	// TODO: Move to RepositoryUtilities or some other relevant class.
	private function is_html_document($path)
	{
		return (preg_match('/\.x?html?$/',$path) === 1);
	}
	/**
	 * Creates a valid filename.
	 * @param string $desired_filename The desired filename.
	 * @return string A valid filename.
	 */
	private function create_valid_filename($desired_filename)
	{
		return strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$desired_filename));
	}
	/**
	 * Creates a unique path.
	 */
	private function create_unique_filename($path,$filename)
	{
		$filename = $this->create_valid_filename($filename);
		$new_filename = $filename;
		$index = 0;
		while(file_exists($this->get_upload_path().'/'.$path.'/'.$new_filename))
		{
			$file_parts = explode('.',$filename);
			$new_filename = array_shift($file_parts).($index++).'.'.implode($file_parts);
		}
		return $new_filename;
	}
	/**
	 *
	 */
	protected function check_document_form($fields)
	{
		$errors = array();
		if(!$fields['choice'])
		{
			// Upload document
			if( isset($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
			{
				if(!HTML_QuickForm_Rule_DiskQuota::validate($_FILES['file']))
				{
					$errors['upload_or_create'] = get_lang('DiskQuotaExceeded');
				}
			}
			else
			{
				$errors['upload_or_create'] = get_lang('NoFileSelected');
			}
		}
		else
		{
			// Create an HTML-document
			$tmpfname = tempnam ('','');
			$handle = fopen($tmpfname, "w");
			fwrite($handle, "writing to tempfile");
			fclose($handle);
			$file['size'] = filesize($tmpfname);
			if(!HTML_QuickForm_Rule_DiskQuota::validate($file))
			{
				$errors['upload_or_create'] = get_lang('DiskQuotaExceeded');
			}
			unlink($tmpfname);
		}
		if(count($errors) == 0)
		{
			return true;
		}
		return $errors;
	}

	private static function get_upload_path()
	{
		return Configuration::get_instance()->get_parameter('general', 'upload_path');
	}
}
?>