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
	private $rdm;
	private $doc;
	private $user;
	private $files;
	
	function DlofImport()
	{
		$this->rdm = RepositoryDataManager :: get_instance();	
	}
	
	public function import_learning_object($file, $repository_manager, $user, $original_name)
	{
		$this->user = $user;
		if(strpos($original_name, '.zip') !== false)
		{
			$zip = Filecompression :: factory();
			$temp = $zip->extract_file($file);
			$dir = $temp . '/' . $user->get_id() . '/'; 
			$files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
			
			foreach($files as $f)
			{
				if(strpos($f, '.dlof') !== false)
				{
					$file = $dir . $f;
				}
				else
				{
					$new_unique_filename = 
						Filesystem :: copy_file_with_double_files_protection($dir, $f, Path :: get(SYS_REPO_PATH) . $user->get_id() . '/', $f, true);
					$this->files[$f] = $new_unique_filename;
				}
			}
			
		}
		
		$doc = $this->doc;
		$doc = new DOMDocument();
		$doc->load($file);
		$learning_object = $doc->getElementsByTagname('learning_object')->item(0);
		
		if($temp)
		{
			Filesystem :: remove($temp);
		}
		
		return $this->import_lo($learning_object);
	}
	
	public function import_lo($learning_object)
	{
		$lotype = $learning_object->getAttribute('type');
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
			$lo->set_owner_id($this->user->get_id());
			$lo->set_parent_id($this->rdm->retrieve_root_category($this->user->get_id())->get_id());
			
			$extended = $learning_object->getElementsByTagName('extended')->item(0);
			
			if($extended->hasChildNodes())
			{
				$nodes = $extended->childNodes;
				$additionalProperties = array();

				foreach($nodes as $node)
				{
					if($node->nodeName == "#text") continue;
					$additionalProperties[$node->nodeName] = $node->nodeValue;
				}
				
				if($type == 'document')
				{
					$filename = $additionalProperties['filename'];
					if($this->files[$filename] != null && $this->files[$filename] != $filename)
					{
						$additionalProperties['filename'] = $this->files[$filename];
					}
					
					$additionalProperties['path'] = $this->user->get_id() . '/' . $this->files[$filename];
				}
				
				
				$lo->set_additional_properties($additionalProperties);
			}
			
			$lo->create_all();
			
			$subitems = $learning_object->getElementsByTagName('sub_items')->item(0);
			$children = $subitems->childNodes;
			for($i = 0; $i < $children->length; $i++)
			{
				$subitem = $children->item($i);
				if($subitem->nodeName == "#text") continue;
				
				$learning_object = $subitem->getElementsByTagname('learning_object')->item(0);
				$childlo = $this->import_lo($learning_object);
				
				$cloi = ComplexLearningObjectItem :: factory($childlo->get_type);
				
				$cloi->set_ref($childlo->get_id());
				$cloi->set_user_id($this->user->get_id());
				$cloi->set_parent($lo->get_id());
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($lo->get_id()));
				
				if($subitem->hasAttributes())
				{ 
					$additionalProperties = array();
					foreach ($subitem->attributes as $attrName => $attrNode) 
					{
						$additionalProperties[$attrName] = $attrNode;
					}
					
					$cloi->setAdditionalProperties($additionalProperties);
				}
				
				$cloi->create();
				
			}
			
			return $lo;
		}
	}
}
?>