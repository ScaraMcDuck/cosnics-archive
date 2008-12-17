<?php
require_once dirname(__FILE__).'/assessment/assessment_qti_import.class.php';
require_once dirname(__FILE__).'/question/question_qti_import.class.php';
require_once Path :: get_library_path() . 'filecompression/filecompression.class.php';

class QtiImport extends LearningObjectImport
{
	
	function import_learning_object()
	{
		$file = $this->get_learning_object_file();
		$user = $this->get_user();
		
		$zip = Filecompression :: factory();
		$temp = $zip->extract_file($this->get_learning_object_file_property('tmp_name'));
		
		$dir = $temp . '/';
		if (file_exists($dir))
		{
			$files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
			foreach($files as $f)
			{
				$type = split('_', $f);
				if ($type[0] == 'qti')
				{
					$importer = self :: factory_qti($f, $this->get_user(), $this->get_category(), $dir);
					if ($importer != null)
					{
						$importer->import_learning_object();
					}
				}
			}
		}
	}
	
	function factory_qti($lo_file, $user, $category, $dir)
	{
		$type = split('_', $lo_file);
		switch ($type[0])
		{
			case 'qti':
				return new AssessmentQtiImport($dir.$lo_file, $user, $category);
			case 'question':
				return new QuestionQtiImport($dir.$lo_file, $user, $category);
			default:
				return null;
		}
	}
	
	function get_file_content_array()
	{
		$file = parent ::get_learning_object_file();
		$result = array();
		
		if (file_exists($file))
		{			
			$unserializer = &new XML_Unserializer();
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
			$unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('location'));
			
			// userialize the document
			$status = $unserializer->unserialize($file, true);    
			if (PEAR::isError($status))
			{
				echo 'Error: ' . $status->getMessage();
			}
			else
			{
				$data = $unserializer->getUnserializedData();
			}
		}
		
		return $data;
	}
}
?>