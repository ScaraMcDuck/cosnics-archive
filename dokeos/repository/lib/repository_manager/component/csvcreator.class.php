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

	/*
	 * Deze functie zal een array teruggeven
	 * Indien er fouten voorkwamen in het csvbestand, zal deze functie een 
	 * array weergeven die de regelnummers bevat van de fouten in het bestand.
	 */
	function csv_validate($typearray, $csvarray)
	{
		$foutenarray= array();
		$objectarray= array();
		array_push($foutenarray, 0);
		
		//elke regel in het csvbestand aflopen
		for ($i = 0 ; $i < count($csvarray); $i++)
		{
			if (in_array($csvarray[$i][0],$typearray))
			{	
				//Het eerste element van de regel is het type van het te importeren object.
				$type = $csvarray[$i][0];					
				$category = $_GET[RepositoryManager :: PARAM_CATEGORY_ID];
				$user = api_get_user_id();
				$object = new AbstractLearningObject($type, $user, $category);
				$message = '';

				//Formulier voor dit object aanmaken
				$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create');	
				$valuearray= array();
				
				//aantal elementen in regel
				$number= count($csvarray[$i]);

				//valuearray opvullen met de waarden van de regel en formulier invullen
				for ( $j=1; $j<$number; $j++)
				{
					array_push($valuearray, $csvarray[$i][$j]);
				}
				$lo_form->setCsvValues($valuearray);
				
				//Elk element in regel valideren.
				for ( $h=1; $h<$number; $h++)
				{
					if (!$lo_form->validatecsv($csvarray[$i][$h]))
					{
						$message = 'a'.$message;
					}
				}
				//Message is leeg indien geen fouten gebeurt zijn.		
				if (empty($message))
				{
					array_push($objectarray, $lo_form);
				}
				else 
				{
					array_push($foutenarray, $i);
				}
											
			}
			//Type zit er niet tussen -> fout
			else 
			{
				array_push($foutenarray, $i);
			}
				
		}//Einde For

		if (count($foutenarray)>1)
		{			
			return $foutenarray;
		}
		else 
		{
			return $objectarray;
		}
	}	
	
}
