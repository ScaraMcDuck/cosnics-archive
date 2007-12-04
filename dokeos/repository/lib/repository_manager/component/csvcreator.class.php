<?php
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/usersdatamanager.class.php';
require_once dirname(__FILE__).'/../../quotamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
include_once (api_get_library_path()."/formvalidator/FormValidator.class.php");

class CSVCreator extends RepositoryManagerComponent
{
	
	function quotacheck($csvarray,$user)
		{
		//$amount_to_add= 300;;
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
	function csv_validate($typearray, $csvarray)
		{
		$foutenarray= array();
		array_push($foutenarray, 0);
		
		for ($i = 0 ; $i < count($csvarray); $i++)
			{
				if (in_array($csvarray[$i][0],$typearray))
				{	
					$type = $csvarray[$i][0];					
					$category = $_GET[RepositoryManager :: PARAM_CATEGORY_ID];
					$user = api_get_user_id();
					$object = new AbstractLearningObject($type, $user, $category);
					
					$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create','post', $url);	

					//temporary to show that we create the correct learning objectforms
					$lo_form->display();	
					if ($lo_form->validate())
					{ 
					$object = $lo_form->create_learning_object();
					
					}
					else 
					{
					array_push($foutenarray, $i);
					}			
									
				}
				else 
				{
					array_push($foutenarray, $i);
				}
				
			}
			if (count($foutenarray)>1)
			{	
				//if errors happend we will return the array with the numbers where it went wrong!
				
				$foutenarray=asort($foutenarray);
				return $foutenarray;
			}
			else 
			{
				//we're gonna return an array filled with learning object forms. This way we can create them in a folowup function!
				//return $lobj_array;
			}
		

		}	
	
}
