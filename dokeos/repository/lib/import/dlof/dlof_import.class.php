<?php
/**
 * @package export
 */
require_once dirname(__FILE__).'/../learning_object_import.class.php';
require_once Path :: get_library_path() . 'filecompression/filecompression.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class DlofImport extends LearningObjectImport
{
	/**
	 * @var RepositoryDataManager
	 */
	private $rdm;
	
	/**
	 * The imported xml file
	 * @var DomDOCUMENT
	 */
	private $doc;
	
	/**
	 * Array of files that are created (hash + path)
	 * @var Array 
	 */
	private $files;
	
	/**
	 * The reference to store the file id's of each learning_object and the new learning_object
	 * @var Array of INT
	 */
	private $learning_object_reference;
	
	/**
	 * The array where the subitems are stored untill all the learning objects are created
	 * With this array the wrappers will then be created
	 * 
	 * Example:
	 * 
	 * $lo_subitems['object0'] = array(0 => array(id => 'object1', properties => array()));
	 */
	private $lo_subitems;
	
	function DlofImport($learning_object_file, $user, $category)
	{
		$this->rdm = RepositoryDataManager :: get_instance();
		parent :: __construct($learning_object_file, $user, $category);	
	}
	
	public function import_learning_object()
	{
		$user = $this->get_user();
		
		$zip = Filecompression :: factory();
		$temp = $zip->extract_file($this->get_learning_object_file_property('tmp_name'));
		$dir = $temp . '/';
		
		$lo_data_dir = $dir . 'data/';
		$path = $dir . 'learning_object.xml';
		
		if (file_exists($lo_data_dir))
		{
			$files = Filesystem :: get_directory_content($lo_data_dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
			
			foreach($files as $f)
			{
				$repdir = Path :: get(SYS_REPO_PATH);
				$hash = md5($f);
		
				$usr_path = $user->get_id() . '/' . Text :: char_at($hash, 0);
				$full_path =  $repdir . $usr_path;
			
				$hash = Filesystem :: create_unique_name($full_path, $hash);
				
				Filesystem :: copy_file($dir . 'data/' . $f, $full_path . '/' . $hash, false);
				$this->files[$f] = array('hash' => $hash, 'path' => $usr_path . '/' . $hash);
			}
		}
		
		$doc = $this->doc;
		$doc = new DOMDocument();
		
		$doc->load($path);
		$learning_objects = $doc->getElementsByTagname('learning_object');

		foreach($learning_objects as $lo)
		{ 
			$this->create_learning_object($lo);
		}

		$this->create_complex_wrappers();
		
		if($temp)
		{
			Filesystem :: remove($temp);
		}
		
		exit();
		return true;
	}
	
	public function create_learning_object($learning_object)
	{
		$id = $learning_object->getAttribute('id');
		if(isset($this->learning_object_reference[$id]))
			return;
		
		if($learning_object->hasChildNodes())
		{ 
			$general = $learning_object->getElementsByTagName('general')->item(0);
			$type = $general->getElementsByTagName('type')->item(0)->nodeValue;
			$title = $general->getElementsByTagName('title')->item(0)->nodeValue;
			$description = $general->getElementsByTagName('description')->item(0)->nodeValue;
			$comment = $general->getElementsByTagName('comment')->item(0)->nodeValue;
			$created = $general->getElementsByTagName('created')->item(0)->nodeValue;
			$modified = $general->getElementsByTagName('modified')->item(0)->nodeValue;
			
			$lo = LearningObject :: factory($type);
			$lo->set_title($title);
			$lo->set_description($description);
			$lo->set_comment($comment);
			$lo->set_creation_date($created);
			$lo->set_modification_date($modified);
			$lo->set_owner_id($this->get_user()->get_id());
			$lo->set_parent_id($this->get_category());
			
			$extended = $learning_object->getElementsByTagName('extended')->item(0);
		
			if($extended->hasChildNodes())
			{
				$nodes = $extended->childNodes;
				$additionalProperties = array();

				foreach($nodes as $node)
				{
					if($node->nodeName == "#text") continue;
					$additionalProperties[$node->nodeName] = convert_uudecode($node->nodeValue);
				}
				
				if($type == 'document')
				{
					$filename = $additionalProperties['filename'];
					$additionalProperties['hash'] = $this->files[$filename]['hash'];
					$additionalProperties['path'] = $this->files[$filename]['path'];
				}

				$lo->set_additional_properties($additionalProperties);
			}
			
			//$lo->set_id('test');
			$lo->create_all();
			
			$this->learning_object_reference[$id] = $lo->get_id();
			
			$subitems = $learning_object->getElementsByTagName('sub_items')->item(0);
			$children = $subitems->childNodes;
			for($i = 0; $i < $children->length; $i++)
			{
				$subitem = $children->item($i);
				if($subitem->nodeName == "#text") continue;
				
				if($subitem->hasAttributes())
				{ 
					$properties = array();

					foreach ($subitem->attributes as $attrName => $attrNode) 
					{
						if($attrName == 'idref')
						{
							$idref = $attrNode->value;
						}
						else
						{ 
							$properties[$attrName] = $attrNode->value;
						}
					}
				}
				
				$this->lo_subitems[$id][] = array('id' => $idref, 'properties' => $properties);
			}
		}
	}
	
	function create_complex_wrappers()
	{
		foreach($this->lo_subitems as $parent_id => $children)
		{
			$real_parent_id = $this->learning_object_reference[$parent_id];
			foreach($children as $child)
			{
				$real_child_id = $this->learning_object_reference[$child['id']];
				
				$childlo = $this->rdm->retrieve_learning_object($real_child_id);
				
				$cloi = ComplexLearningObjectItem :: factory($childlo->get_type());
	
				$cloi->set_ref($childlo->get_id());
				$cloi->set_user_id($this->get_user()->get_id());
				$cloi->set_parent($real_parent_id);
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($real_parent_id));
				$cloi->set_additional_properties($child['properties']);
				$cloi->create();
			}
		}
	}
}
?>