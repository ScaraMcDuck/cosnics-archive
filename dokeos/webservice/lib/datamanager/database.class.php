<?php
/**
 * @package webservices
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_webservice_result_set.class.php';
require_once dirname(__FILE__).'/database/database_webservice_rel_user_result_set.class.php';
require_once dirname(__FILE__).'/../webservice_data_manager.class.php';
require_once dirname(__FILE__).'/../webservice.class.php';
require_once dirname(__FILE__).'/../webservice_category.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
 * ==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Stefan Billiet
==============================================================================
 */
 
class DatabaseWebserviceDataManager extends WebserviceDataManager
{
	private $database;
	
	function initialize()
	{
		$this->database = new Database(array('webservice' => 'cw', 'webservice_category' => 'cwc'));
		$this->database->set_prefix('webservice_');
	}
	
	function get_next_webservice_id()
	{
		$id = $this->database->get_next_id(Webservice :: get_table_name());
		return $id;
	}
	
	function count_webservices($conditions = null)
	{
		return $this->database->count_objects(Webservice :: get_table_name(), $conditions);
	}
	
	function truncate_webservice($webservice)
	{
		$condition = new EqualityCondition(Webservice :: PROPERTY_WEBSERVICE_ID, $webservice->get_id());
		return $this->database->delete(Webservice :: get_table_name(), $condition);
	}
	
	function truncate_webservice_category($webserviceCategory)
	{
		$condition = new EqualityCondition(WebserviceCategory :: PROPERTY_WEBSERVICE_ID, $webservice->get_id());
		return $this->database->delete(WebserviceCategory :: get_table_name(), $condition);
	}
	
	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}
	
	function retrieve_webservice($id)
	{
		$condition = new EqualityCondition(Webservice :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Webservice :: get_table_name(), $condition);
	}
	
	function retrieve_webservices($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(Webservice :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_webservice_category($id)
	{
		$condition = new EqualityCondition(WebserviceCategory :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(WebserviceCategory :: get_table_name(), $condition);
	}
	
	function retrieve_webservice_categories($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(WebserviceCategory :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function delete_webservice($webservice)
	{
		$condition = new EqualityCondition(Webservice :: PROPERTY_ID, $webservice->get_id());
		$bool = $this->database->delete($webservice->get_table_name(), $condition);
		
		$condition_subwebservices = new EqualityCondition(Webservice :: PROPERTY_PARENT, $webservice->get_id());
		$webservices = $this->retrieve_webservices($condition_subwebservices);
		while($ws = $webservices->next_result())
		{
			$bool = $bool & $this->delete_webservice($ws);
		}
		
		$this->truncate_webservice($webservice);
		
		return $bool;
	}
	
	function delete_webservice_category($webserviceCategory)
	{
		$condition = new EqualityCondition(WebserviceCategory :: PROPERTY_ID, $webservice->get_id());
		$bool = $this->database->delete($webserviceCategory->get_table_name(), $condition);
		$this->truncate_webservice_category($webserviceCategory);
		
		return $bool;
	}
	
	function update_webservice($webservice)
	{
		$condition = new EqualityCondition(Webservice :: PROPERTY_ID, $webservice->get_id());
		return $this->database->update($webservice, $condition);
	}
	
	function update_webservice_category($webserviceCategory)
	{
		$condition = new EqualityCondition(WebserviceCategory :: PROPERTY_ID, $webserviceCategory->get_id());
		return $this->database->update($webserviceCategory, $condition);
	}
	
	function create_webservice($webservice)
	{
		return $this->database->create($webservice);
	}
	
	function create_webservice_category($webserviceCategory)
	{
		return $this->database->create($webserviceCategory);
	}	

}
?>