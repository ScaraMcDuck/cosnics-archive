<?php
/**
 * @package export
 */
require_once dirname(__FILE__).'/../learning_object_export.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class DlofExport extends LearningObjectExport
{
	private $rdm;
	private $doc;
	private $files;
	
	function DlofExport()
	{
		$this->rdm = RepositoryDataManager :: get_instance();	
	}
	
	public function export_learning_object($learning_object)
	{
		$this->doc = new DOMDocument();
  		$this->doc->formatOutput = true;
  		$this->export_lo($learning_object, $this->doc);
  		$path = Path :: get(SYS_TEMP_PATH) . 'learning_object.xml';
  		//$path = FileSystem :: create_safe_name($path);
		$this->doc->save($path);
		
		if(count($this->files) > 0)
		{
			$zippath = Path :: get(SYS_TEMP_PATH) . 'files.zip';
			//$zippath = FileSystem :: create_safe_name($path);
			$zip = new ZipArchive();
			if ($zip->open($zippath) === TRUE) 
			{
			    foreach($this->files as $file)
			    {
			    	$zip->addFile($file);
			    }
			    $zip->addFile($path);
			    $zip->close();
			    echo 'ok';
			} else {
			    echo 'failed';
			}
			
			return $zippath;
		}
		
		return $path;
	}
	
	public function export_lo($learning_object, $parent)
	{
		$doc = $this->doc;
		
		$lo = $doc->createElement( "learning_object" );
  		$parent->appendChild( $lo );
  		
  		$export_prop = array(LearningObject :: PROPERTY_TYPE, LearningObject :: PROPERTY_TITLE, LearningObject :: PROPERTY_DESCRIPTION, LearningObject :: PROPERTY_COMMENT,
  						  	 LearningObject :: PROPERTY_CREATION_DATE, LearningObject :: PROPERTY_MODIFICATION_DATE);
  		
  		$general = $doc->createElement( "general" );
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
  			$this->files[] = Path :: get(SYS_FILE_PATH) . $learning_object->get_id() . '/' . $learning_object->get_filename();
  		}
  		
  		$extended = $doc->createElement( "extended" );
  		$lo->appendChild( $extended );
  		
  		foreach($learning_object->get_additional_properties() as $prop => $value)
  		{
  			$property = $doc->createElement( $prop);
	  		$extended->appendChild($property);
	  		
	  		$text = $doc->createTextNode($value);
			$text = $property->appendChild($text);
  		}
  		
  		$type = $doc->createAttribute("type");
		$lo->appendChild($type);
  		
		if($learning_object->is_complex_learning_object())
		{	
			$text = $doc->createTextNode("complex");
			$type->appendChild($text);
			
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $learning_object->get_id());
			$children = $this->rdm->retrieve_complex_learning_object_items($condition);
			
			if($children->size() > 0)
			{
				$sub_items = $doc->createElement("sub_items");
				$lo->appendChild($sub_items);	
			}
			
			while($child = $children->next_result())
			{
				$sub_item = $doc->createElement("sub_item");
				$sub_items->appendChild($sub_item);	
		
				foreach($child->get_additional_properties() as $prop => $value)
		  		{
		  			$property = $doc->createAttribute($prop);
					$sub_item->appendChild($property);
			  		
			  		$text = $doc->createTextNode($value);
					$text = $property->appendChild($text);
		  		}
				
				$this->export_lo($this->rdm->retrieve_learning_object($child->get_ref()), $sub_item);
			}
		}
		else
		{
			$text = $doc->createTextNode("simple");
			$type->appendChild($text);
		}
	}
}
?>