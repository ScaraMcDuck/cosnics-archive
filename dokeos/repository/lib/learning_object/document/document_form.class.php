<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/document.class.php';
require_once dirname(__FILE__).'/../../../../main/inc/lib/formvalidator/Rule/DiskQuota.php';
require_once dirname(__FILE__).'/../../../../common/filecompression/filecompression.class.php';
require_once dirname(__FILE__).'/../../../../common/filesystem/filesystem.class.php';
/**
 * A form to create/update a document.
 *
 * A destinction is made between HTML documents and other documents. For HTML
 * documents an online HTML editor is used to edit the contents of the document.
 */

class DocumentForm extends LearningObjectForm
{
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('upload_or_create', 'upload_or_create', get_lang('FileName'));
		$this->addElement('checkbox','uncompress',get_lang('Uncompress'));
		$this->addFormRule(array ($this, 'check_document_form'));
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$object = $this->get_learning_object();
		if (RepositoryUtilities :: is_html_document($object->get_path()))
		{
			$this->add_html_editor('html_content', get_lang('HtmlDocument'),false,true);
			$this->addRule('html_content', get_lang('DiskQuotaExceeded'), 'disk_quota');
			//TODO: add option to upload & overwrite a HTML-document
			//TODO: add Rule to check if diskquota doesn't exceed when uploading a document
		}
		else
		{
			$this->addElement('file', 'file', get_lang('FileName'));
			$this->addRule('file', get_lang('DiskQuotaExceeded'), 'disk_quota');
		}
	}
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if (isset ($object) && RepositoryUtilities :: is_html_document($object->get_path()))
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
		$owner_path = $this->get_upload_path().'/'.$owner;
		Filesystem::create_dir($owner_path);
		if ($values['choice'])
		{
			$filename = $values[Document :: PROPERTY_TITLE].'.html';
			$filename = Filesystem::create_unique_filename($this->get_upload_path().'/'.$owner, $filename);
			$path = $owner.'/'.$filename;
			$full_path = $this->get_upload_path().'/'.$path;
			Filesystem::write_to_file($full_path,$values['html_content']);
		}
		else
		{
			$filename = Filesystem::create_unique_filename($this->get_upload_path().'/'.$owner, $_FILES['file']['name']);
			$path = $owner.'/'.$filename;
			$full_path = $this->get_upload_path().'/'.$path;
			move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "'.$full_path.'"');
		}
		chmod($full_path, 0777);
		$object = new Document();
		$object->set_path($path);
		$object->set_filename($filename);
		$object->set_filesize(Filesystem::get_disk_space($full_path));
		$this->set_learning_object($object);
		$document = parent :: create_learning_object();
		if($values['uncompress'])
		{
			$filecompression = Filecompression::factory();
			$dir = $filecompression->extract_file($document->get_full_path());
			//TODO: read contents from $dir and add all entries as new learning objects
		}
		return $document;
	}
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		$path = $object->get_path();
		$filename = $object->get_filename();
		$owner = $object->get_owner_id();
		$owner_path = $this->get_upload_path().'/'.$owner;
		Filesystem::create_dir($owner_path);
		if (isset ($values['html_content']))
		{
			if ((isset($values['version']) && $values['version'] == 0) || !isset($values['version']))
			{
				unlink($this->get_upload_path().'/'.$object->get_path());
			}

			$filename = Filesystem::create_unique_filename($this->get_upload_path().'/'.$owner, $object->get_title() . '.html');
			$path = $owner.'/'.$filename;
			$full_path = $this->get_upload_path().'/'.$path;
			Filesystem::write_to_file($full_path,$values['html_content']);
		}
		elseif (strlen($_FILES['file']['name']) > 0)
		{
			if ((isset($values['version']) && $values['version'] == 0) || !isset($values['version']))
			{
				unlink($this->get_upload_path().'/'.$object->get_path());
			}
			$filename = Filesystem::create_unique_filename($this->get_upload_path().'/'.$owner, $_FILES['file']['name']);
			$path = $owner.'/'.$filename;
			$full_path = $this->get_upload_path().'/'.$path;
			move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "'.$full_path.'"');
			chmod($full_path, 0777);
		}
		$object->set_path($path);
		$object->set_filename($filename);
		$object->set_filesize(Filesystem::get_disk_space($this->get_upload_path().'/'.$object->get_path()));
		return parent :: update_learning_object();
	}
	/**
	 *
	 */
	protected function check_document_form($fields)
	{
		// TODO: Do the errors need htmlentities()?
		$errors = array ();

		$owner_id = $this->get_owner_id();
		$udm = & UsersDataManager :: get_instance();

		$owner = $udm->retrieve_user($owner_id);

		$quotamanager = new QuotaManager($owner);

		if (!$fields['choice'])
		{
			if (isset ($_FILES['file']) && isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0)
			{
			  	switch($_FILES['file']['error']){
			   		case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
			     		$errors['upload_or_create'] = get_lang('FileTooBig');
			    		break;
			   		case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
			     		$errors['upload_or_create'] = get_lang('FileTooBig');
			     		break;
			   		case 3: //uploaded file was only partially uploaded
			     		$errors['upload_or_create'] = get_lang('UploadIncomplete');
			     		break;
			   		case 4: //no file was uploaded
			     		$errors['upload_or_create'] = get_lang('NoFileSelected');
			     		break;
			  	}
			}
			elseif (isset ($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
			{
				$size = $_FILES['file']['size'];
				$available_disk_space = $quotamanager->get_available_disk_space();

				if ($size > $available_disk_space)
				{
					$errors['upload_or_create'] = get_lang('DiskQuotaExceeded');
				}
				$filecompression = Filecompression::factory();
				if( $fields['uncompress'] && !$filecompression->is_supported_mimetype($_FILES['file']['type']))
				{
					$errors['uncompress'] = get_lang('UncompressNotAvailableForThisFile');
				}
				//TODO: Add a check to see if the uncompressed file doesn't take to much disk space
			}
			else
			{
				$errors['upload_or_create'] = get_lang('NoFileSelected');
			}
		}
		else
		{
			// Create an HTML-document
			$file['size'] = Filesystem::guess_disk_space($fields['html_content']);
			$available_disk_space = $quotamanager->get_available_disk_space();
			if ($file['size'] > $available_disk_space)
			{
				$errors['upload_or_create'] = get_lang('DiskQuotaExceeded');
			}
			else
			{
				if (!HTML_QuickForm_Rule_Required :: validate($fields['html_content']))
				{
					$errors['upload_or_create'] = get_lang('NoFileCreated');
				}
			}
		}
		if (count($errors) == 0)
		{
			return true;
		}
		return $errors;
	}

	private static function get_upload_path()
	{
		return realpath(Configuration :: get_instance()->get_parameter('general', 'upload_path'));
	}
}
?>