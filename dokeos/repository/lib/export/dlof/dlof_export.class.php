<?php
/**
 * @package export
 */
require_once dirname(__FILE__).'/../learning_object_export.class.php';
require_once Path :: get_library_path() . 'filecompression/filecompression.class.php';
require_once 'XML/Serializer.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class DlofExport extends LearningObjectExport
{
	/**
	 * @var RepositoryDataManager
	 */
	private $rdm;
	/**
	 * @var DOMDocument
	 */
	private $doc;
	
	/*
	 * Array of files to export
	 */
	private $files;
	
	/*
	 * The <learnings_objects> tag in the xml file
	 */
	private $root;
	
	/**
	 * Array of already exported learning objects to prevent doubles
	 */
	private $exported_learning_objects;
	
	function DlofExport($learning_object)
	{
		$this->rdm = RepositoryDataManager :: get_instance();
		$this->exported_learning_objects = array();
		parent :: __construct($learning_object);	
	}
	
	public function export_learning_object()
	{
		$learning_objects = $this->get_learning_object();
		$this->doc = new DOMDocument('1.0', 'UTF-8');
  		$this->doc->formatOutput = true;

  		$this->root = $this->doc->createElement('learning_objects');
		$this->doc->appendChild($this->root);
		
		$user = null;
		
		foreach($learning_objects as $lo)
		{
			if(!$user)
				$user = $lo->get_owner_id();
				
			$this->render_learning_object($lo);
		}
  		
  		$temp_dir = Path :: get(SYS_TEMP_PATH). $user . '/export_learning_objects/';
  		
  		if(!is_dir($temp_dir))
  		{
  			mkdir($temp_dir, 0777, true);
  		}

  		$xml_path = $temp_dir . 'learning_object.xml';
		$this->doc->save($xml_path);
		
		foreach($this->files as $filename => $file)
		{
			$newfile = $temp_dir . 'data/' . basename($filename);
			Filesystem :: copy_file($file, $newfile);
		}
		
		$zip = Filecompression :: factory();
		//$zip->set_filename('learning_objects_export');
		$zippath = $zip->create_archive($temp_dir);
		
		Filesystem :: remove($temp_dir);
		
		return $zippath;
	}

	function render_learning_object($learning_object)
	{
		if(in_array($learning_object->get_id(), $this->exported_learning_objects))
			return;
		
		$this->exported_learning_objects[] = $learning_object->get_id();
			
		$doc = $this->doc;
		$root = $this->root;
		
		$lo = $doc->createElement('learning_object');
  		$root->appendChild( $lo );
  		
  		$id = $doc->createAttribute('id');
  		$lo->appendChild($id);
  		
  		$id_value = $doc->createTextNode('object' . $learning_object->get_id());
  		$id->appendChild($id_value);
  		
  		$export_prop = array(LearningObject :: PROPERTY_TYPE, LearningObject :: PROPERTY_TITLE, LearningObject :: PROPERTY_DESCRIPTION, LearningObject :: PROPERTY_COMMENT,
  						  	 LearningObject :: PROPERTY_CREATION_DATE, LearningObject :: PROPERTY_MODIFICATION_DATE);
  		
  		$general = $doc->createElement('general');
  		$lo->appendChild( $general );
  		
  		foreach($export_prop as $prop)
  		{
	  		$property = $doc->createElement( $prop);
	  		$general->appendChild($property);
	  		
	  		$text = $doc->createTextNode($learning_object->get_default_property($prop));
			$text = $property->appendChild($text);
  		}
  		
  		if($learning_object->get_type() == 'document')
  		{
  			$this->files[$learning_object->get_filename()] = $learning_object->get_full_path();
  		}
  		
  		$extended = $doc->createElement('extended');
  		$lo->appendChild( $extended );
  		
  		foreach($learning_object->get_additional_properties() as $prop => $value)
  		{
  			$property = $doc->createElement($prop); 
	  		$extended->appendChild($property);
			$value = convert_uuencode($value);
	  		$text = $doc->createTextNode($value);
			$text = $property->appendChild($text);
  		}
  		
  		$type = $doc->createAttribute('type');
		$lo->appendChild($type);
  		
		if($learning_object->is_complex_learning_object())
		{	
			$text = $doc->createTextNode('complex');
			$type->appendChild($text);
			
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $learning_object->get_id(), ComplexLearningObjectItem :: get_table_name());
			$children = $this->rdm->retrieve_complex_learning_object_items($condition);
			
			if($children->size() > 0)
			{
				$sub_items = $doc->createElement('sub_items');
				$lo->appendChild($sub_items);	
			}
			
			while($child = $children->next_result())
			{
				$sub_item = $doc->createElement('sub_item');
				$sub_items->appendChild($sub_item);	
		
				$id_ref = $doc->createAttribute('idref');
				$sub_item->appendChild($id_ref);
				
				$id_ref_value = $doc->createTextNode('object' . $child->get_ref());
				$id_ref->appendChild($id_ref_value);				
				
				foreach($child->get_additional_properties() as $prop => $value)
		  		{
		  			$property = $doc->createAttribute($prop);
					$sub_item->appendChild($property);
			  		
			  		$text = $doc->createTextNode($value);
					$text = $property->appendChild($text);
		  		}
				
				$this->render_learning_object($this->rdm->retrieve_learning_object($child->get_ref()));
			}
		}
		else
		{
			$text = $doc->createTextNode('simple');
			$type->appendChild($text);
		}
	}
}
?>