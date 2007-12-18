<?php
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../quotamanager.class.php';

class CSVCreator extends RepositoryManagerComponent
{
	
	function quotacheck($csvarray,$user)
	{
		$amount_to_add=count($csvarray);
		$quotamanagercsv = new QuotaManager($user);
		$numberofused=$quotamanagercsv->get_used_database_space();
		$maximum=$quotamanagercsv->get_max_database_space();
		if ($amount_to_add+$numberofused <= $maximum)
			{
			return true;}	
		else 
			{return false;}
	}
	//function to check 
	function parent_split($parent)
	{
	$aparent = explode('#',$parent);
	$bparent = explode(' ',$aparent[1]);
	return $bparent[0];
	}
	/*
	 * Deze functie zal een array teruggeven
	 * Indien er fouten voorkwamen in het csvbestand, zal deze functie een 
	 * array weergeven die de regelnummers bevat van de fouten in het bestand.
	 */
	function csv_validate($typearray, $csvarray)
	{
		$errorarray= array();
		$objectarray= array();
		$errorarray[0]=''; 
		
		//elke regel in het csvbestand aflopen
		for ($i = 0 ; $i < count($csvarray); $i++)
		{
			//Kijken of het object in csv wel bestaat.
			if (in_array($csvarray[$i][0],$typearray))
			{	
				$dataManager = RepositoryDataManager :: get_instance();
				//Het eerste element van de regel is het type van het te importeren object.
				$type = $csvarray[$i][0];	
				//retrieve the root category (this is for now , can be modded later on so users can include 					the category where they want everything to be added
				$user = api_get_user_id();
				$categorystring= $dataManager->retrieve_root_category($user);
				$category= $this->parent_split($categorystring);
				//create the abstract learning object			
				$object = new AbstractLearningObject($type, $user, $category);
				$message = '';
				//Create a form for the Learning object
				$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create');	
				$valuearray= array();
				//Fill up the valuearray with values , retrieved from the csv , id is automatically put on 					1 (root)
				for ( $j=1; $j<count($csvarray[$i]); $j++)
				{
					if ($j==2)
					{	//second value needs to be the category (predefined as root category here)
						array_push($valuearray, $category);
					}
					array_push($valuearray, $csvarray[$i][$j]);
				}
				
				$lo_form->setCsvValues($valuearray);				
				
				if ($lo_form->validatecsv($valuearray))
				{
					
					array_push($objectarray, $lo_form);
				}
				else 
				{
						$errorarray[0]='faultyarrayreturn';
						array_push($errorarray, ($i+1));
				}								
			
			}
			//Type not found in our list
			else 
			{
				$errorarray[0]='faultyarrayreturn';
				array_push($errorarray, ($i+1));
			}
				
		}
		//return the errorarray if its filled
		if (!empty($errorarray[0]))
		{		
			return $errorarray;
		}
		else 
		{
			return $objectarray;
		}

	}	

	
}
