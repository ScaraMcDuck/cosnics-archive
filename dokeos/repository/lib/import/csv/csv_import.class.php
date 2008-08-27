<?php
/**
 * @package export
 */
require_once dirname(__FILE__).'/../learning_object_import.class.php';
require_once Path :: get_library_path().'import/import.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class CsvImport extends LearningObjectImport
{
	private $rdm;
	
	function CsvImport()
	{
		$this->rdm = RepositoryDataManager :: get_instance();	
	}
	
	public function import_learning_object()
	{
		$category = $_GET[RepositoryManager :: PARAM_CATEGORY_ID];
		$csvarray = Import :: read_csv($this->get_learning_object_file_property('tmp_name'));			
		$csvcreator = new CSVCreator();

		$true=$csvcreator->quota_check($csvarray,$this->get_user());
		if ($true)
		{	
			$typearray = $this->rdm->get_registered_types(true);
			$temparray= $csvcreator->csv_validate($typearray, $csvarray);
			if (!($temparray[0]=='faultyarrayreturn'))
			{
				for($i = 0;$i <count($temparray);$i++)
				{
					$temparray[$i]->create_learning_object();
				}
				
				return $temparray[$i];
			}
			else
			{
				$errormessage= 'The folowing rows have been reported as wrong: ';
				for ($i = 1 ; $i < count($temparray); $i++)
				{
					$errormessage=$errormessage.' '.$temparray[$i];
				}
				
				return false;
			}
		}
		//To much to be imported
		else 
		{	
			return false;
		}
	}
	
}
?>