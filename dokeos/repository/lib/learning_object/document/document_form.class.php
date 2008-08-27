<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/document.class.php';
require_once Path :: get_library_path().'html/formvalidator/Rule/DiskQuota.php';
require_once Path :: get_library_path().'filecompression/filecompression.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
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
		$this->addElement('upload_or_create', 'upload_or_create', Translation :: get('FileName'));
		$this->addElement('checkbox','uncompress',Translation :: get('Uncompress'));
		$this->addFormRule(array ($this, 'check_document_form'));
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$object = $this->get_learning_object();
		if (DokeosUtilities :: is_html_document($object->get_path()))
		{
			$this->add_html_editor('html_content', Translation :: get('HtmlDocument'),false,true);
			$this->addRule('html_content', Translation :: get('DiskQuotaExceeded'), 'disk_quota');
			//TODO: add option to upload & overwrite a HTML-document
			//TODO: add Rule to check if diskquota doesn't exceed when uploading a document
		}
		else
		{
			$this->addElement('file', 'file', Translation :: get('FileName'));
			$this->addRule('file', Translation :: get('DiskQuotaExceeded'), 'disk_quota');
		}
	}
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if (isset ($object) && DokeosUtilities :: is_html_document($object->get_path()))
		{
			$defaults['html_content'] = file_get_contents($this->get_upload_path().$object->get_path());
		}
		$defaults['choice'] = 0;
		parent :: setDefaults($defaults);
	}
	function create_learning_object()
	{
		$owner = $this->get_owner_id();
		$values = $this->exportValues();
		$owner_path = $this->get_upload_path().$owner;
		Filesystem::create_dir($owner_path);
		if ($values['choice'])
		{
			$filename = $values[Document :: PROPERTY_TITLE].'.html';
			$filename = Filesystem::create_unique_name($this->get_upload_path().$owner, $filename);
			$path = $owner.'/'.$filename;
			$full_path = $this->get_upload_path().$path;
			Filesystem::write_to_file($full_path,$values['html_content']);
		}
		else
		{
			$filename = Filesystem::create_unique_name($this->get_upload_path().$owner, $_FILES['file']['name']);
			$path = $owner.'/'.$filename;
			$full_path = $this->get_upload_path().$path;
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
			$entries = Filesystem::get_directory_content($dir);
			foreach($entries as $index => $entry)
			{
				$url = str_replace(realpath($dir),'',realpath($entry));
				if(is_dir($entry))
				{
					//Create a category in the repository
					$object = new Category();
					$this->set_learning_object($object);
					$object = parent::create_learning_object();
					$object->set_title(basename($url));
					if(isset($created_directories[dirname($url)]))
					{
						$object->set_parent_id($created_directories[dirname($url)]);
					}
					$object->update();
					$created_directories[$url] = $object->get_id();
				}
				elseif(is_file($entry))
				{
					//Create a document in the repository
					$new_path = $owner_path.'/'.basename($entry);
					Filesystem::copy_file($entry,$new_path);
					$object = new Document();
					$object->set_path($owner.'/'.basename($entry));
					$object->set_filename(basename($entry));
					$object->set_filesize(Filesystem::get_disk_space($new_path));
					$this->set_learning_object($object);
					$object = parent :: create_learning_object();
					$object->set_title(basename($url));
					if(isset($created_directories[dirname($url)]))
					{
						$object->set_parent_id($created_directories[dirname($url)]);
					}
					$object->update();
				}
			}
			Filesystem::remove($dir);
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
		$owner_path = $this->get_upload_path().$owner;
		Filesystem::create_dir($owner_path);
		if (isset ($values['html_content']))
		{
			if ((isset($values['version']) && $values['version'] == 0) || !isset($values['version']))
			{
				Filesystem::remove($this->get_upload_path().$object->get_path());
			}

			$filename = Filesystem::create_unique_name($this->get_upload_path().$owner, $object->get_title() . '.html');
			$path = $owner.'/'.$filename;
			$full_path = $this->get_upload_path().$path;
			Filesystem::write_to_file($full_path,$values['html_content']);
		}
		elseif (strlen($_FILES['file']['name']) > 0)
		{
			if ((isset($values['version']) && $values['version'] == 0) || !isset($values['version']))
			{
				Filesystem::remove($this->get_upload_path().$object->get_path());
			}
			$filename = Filesystem::create_unique_name($this->get_upload_path().$owner, $_FILES['file']['name']);
			$path = $owner.'/'.$filename;
			$full_path = $this->get_upload_path().$path;
			move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "'.$full_path.'"');
			chmod($full_path, 0777);
		}
		$object->set_path($path);
		$object->set_filename($filename);
		$object->set_filesize(Filesystem::get_disk_space($this->get_upload_path().$object->get_path()));
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
		$udm = UsersDataManager :: get_instance();

		$owner = $udm->retrieve_user($owner_id);

		$quotamanager = new QuotaManager($owner);

		if (!$fields['choice'])
		{
			if (isset ($_FILES['file']) && isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0)
			{
			  	switch($_FILES['file']['error']){
			   		case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
			     		$errors['upload_or_create'] = Translation :: get('FileTooBig');
			    		break;
			   		case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
			     		$errors['upload_or_create'] = Translation :: get('FileTooBig');
			     		break;
			   		case 3: //uploaded file was only partially uploaded
			     		$errors['upload_or_create'] = Translation :: get('UploadIncomplete');
			     		break;
			   		case 4: //no file was uploaded
			     		$errors['upload_or_create'] = Translation :: get('NoFileSelected');
			     		break;
			  	}
			}
			elseif (isset ($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
			{
				$size = $_FILES['file']['size'];
				$available_disk_space = $quotamanager->get_available_disk_space();

				if ($size > $available_disk_space)
				{
					$errors['upload_or_create'] = Translation :: get('DiskQuotaExceeded');
				}
				$filecompression = Filecompression::factory();
				if( $fields['uncompress'] && !$filecompression->is_supported_mimetype($_FILES['file']['type']))
				{
					$errors['uncompress'] = Translation :: get('UncompressNotAvailableForThisFile');
				}
				//TODO: Add a check to see if the uncompressed file doesn't take to much disk space
			}
			else
			{
				$errors['upload_or_create'] = Translation :: get('NoFileSelected');
			}
		}
		else
		{
			// Create an HTML-document
			$file['size'] = Filesystem::guess_disk_space($fields['html_content']);
			$available_disk_space = $quotamanager->get_available_disk_space();
			if ($file['size'] > $available_disk_space)
			{
				$errors['upload_or_create'] = Translation :: get('DiskQuotaExceeded');
			}
			else
			{
				if (!HTML_QuickForm_Rule_Required :: validate($fields['html_content']))
				{
					$errors['upload_or_create'] = Translation :: get('NoFileCreated');
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
		return Path :: get(SYS_REPO_PATH);
	}
}
?>