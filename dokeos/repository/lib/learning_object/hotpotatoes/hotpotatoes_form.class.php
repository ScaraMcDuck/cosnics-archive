<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/hotpotatoes.class.php';
require_once Path :: get_library_path().'filecompression/filecompression.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
/**
 * This class represents a form to create or update open questions
 */
class HotpotatoesForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		$defaults[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS] = $valuearray[3];

		parent :: set_values($defaults);			
	}
	
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if ($object != null) 
		{
			$defaults[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS] = $object->get_maximum_attempts();
		}
		else
		{
			$defaults[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS] = 0;
		}
			
		parent :: setDefaults($defaults);
	}

	protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    	$this->add_textfield(Assessment :: PROPERTY_MAXIMUM_ATTEMPTS, Translation :: get('MaximumAttempts')); 
    	$this->addElement('html', Translation :: get('NoMaximumAttemptsFillIn0'));
    	$this->addElement('file', 'file', Translation :: get('UploadHotpotatoes'));
    	$this->addRule('file', Translation :: get('ThisFileIsRequired'), 'required');
    	$this->addElement('category');
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->add_textfield(Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS, Translation :: get('MaximumAttempts')); 
    	$this->addElement('html', Translation :: get('NoMaximumAttemptsFillIn0'));
    	$this->addElement('file', 'file', Translation :: get('ChangeHotpotatoes'));
    	$this->addRule('file', Translation :: get('ThisFileIsRequired'), 'required');
    	$this->addElement('category');
	}

	private $includes;
	
	// Inherited
	function create_learning_object()
	{
		$object = new Hotpotatoes();
		$values = $this->exportValues();
		
		if(!$this->upload_file($object))
			return false;
		
		$att = $values[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS];
		$object->set_maximum_attempts($att ? $att : 0);
		
		$this->set_learning_object($object);
		//$object->add_javascript();
		$succes = parent :: create_learning_object();
		
		foreach($this->includes as $include)
			$object->include_learning_object($include);
		
		return $succes;
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$values = $this->exportValues();
		
		if(isset($_FILES['file']) && $_FILES['file']['name'] != '')
		{
			$object->delete_file();
			if(!$this->upload_file($object))
				return false;
		}
		
		$att = $values[Hotpotatoes :: PROPERTY_MAXIMUM_ATTEMPTS];
		$object->set_maximum_attempts($att ? $att : 0);
		
		$this->set_learning_object($object);
		
		$succes = parent :: update_learning_object();
		
		foreach($this->includes as $include)
			$object->include_learning_object($include);
		
		return $succes;
	}
	
	function upload_file($object)
	{
		if($_FILES['file']['error'] == '4')
		{
			return false;
		}
		
		$path = $this->upload();
		
		//dump($_FILES['file']);
		$filename = $_FILES['file']['name'];
		if(substr($filename, -4) == '.zip')
		{
			$path = $this->manage_zip_file($object, $path);
			$object->set_path($path);
		}
		else
		{
			$object->set_path($path);
		}
		
		return true;
	}
	
	function upload()
	{  
		$owner = $this->get_owner_id();
		$filename = Filesystem :: create_unique_name(Path :: get(SYS_REPO_PATH).$owner, $_FILES['file']['name']);

		$repo_path = Path :: get(SYS_REPO_PATH) . $owner . '/';
		$full_path = $repo_path . $filename;
		
		if(!is_dir($repo_path))
				Filesystem :: create_dir($repo_path);
		
		move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "'.$full_path.'"');
		chmod($full_path, 0777);
		
		return $owner . '/' . $filename;
	}
	
	function manage_zip_file($object, $path)
	{
		$owner = $this->get_owner_id();
		$full_path = Path :: get(SYS_REPO_PATH) . $path;
		$repo_path = Path :: get(SYS_REPO_PATH) . $owner . '/';
		
		$filecompression = Filecompression::factory();
		$dir = $filecompression->extract_file($full_path);
		$entries = Filesystem::get_directory_content($dir);
		
		$this->includes = array();
		
		foreach($entries as $index => $entry)
		{
			$filename = Filesystem :: create_unique_name($repo_path, basename($entry));
			$full_new_path = $repo_path . $filename;
			$new_path = $owner . '/' . $filename;
			
			Filesystem :: move_file($entry, $full_new_path, false);
			if(substr($filename, -4) == '.htm' || substr($filename, -5) == '.html')
			{
				$return_path = $new_path;
			}
			else
			{
				$doc = new Document();
				$doc->set_path($new_path);
				$doc->set_filename($filename);
				$doc->set_filesize(Filesystem::get_disk_space($full_new_path));
				$doc->set_parent_id(0);
				$this->set_learning_object($doc);
				parent :: create_learning_object();
				$this->includes[] = $doc->get_id();
			}
		}
		
		Filesystem :: remove($dir);
		Filesystem :: remove($full_path);
		
		return $return_path;
	}
}
?>
