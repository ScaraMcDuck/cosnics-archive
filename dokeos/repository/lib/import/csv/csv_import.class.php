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
	
	public function import_learning_object($file, $repository_manager)
	{
		$category = $_GET[RepositoryManager :: PARAM_CATEGORY_ID];
		$csvarray = Import :: read_csv($file);			
		$csvcreator = new CSVCreator();

		$true=$csvcreator->quota_check($csvarray,$repository_manager->get_user());
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
				$message= 'You created '.count($temparray).' objects';
				//this redirect is a solution to show the ROOT directory , needs to be modded when 						//users get chance to include 'category' into csv files
		
				$repository_manager->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, $message,1);					
			}
			else
			{
				$errormessage= 'The folowing rows have been reported as wrong: ';
				for ($i = 1 ; $i < count($temparray); $i++)
				{
					$errormessage=$errormessage.' '.$temparray[$i];
				}
				
				$repository_manager->display_header(new BreadcrumbTrail());							
				Display :: display_warning_message($errormessage);			
				$repository_manager->display_footer();
			}
		}
		//To much to be imported
		else 
		{	
			$repository_manager->display_header(new BreadcrumbTrail());	
			Display :: display_warning_message('Your quota would be exceeded by importing this CSV , aborted.');			
			$repository_manager->display_footer();
		}
	}
	
}
?>