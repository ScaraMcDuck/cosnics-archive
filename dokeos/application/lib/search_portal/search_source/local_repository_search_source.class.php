<?php
/**
 * @package application.searchportal
 */
require_once dirname(__FILE__).'/../search_source.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/../repository_search_result.class.php';

class LocalRepositorySearchSource implements SearchSource
{
	private $data_manager;
	
	function LocalRepositorySearchSource($data_manager)
	{
		$this->data_manager = $data_manager;
	}
	
	function search ($query)
	{
		$condition = DokeosUtilities :: query_to_condition($query);
		
		$adm = AdminDataManager :: get_instance();
		$repository_title = PlatformSetting :: get('site_name');
		
		$repository_url = Path :: get(WEB_PATH);
		$returned_results = $this->data_manager->retrieve_learning_objects(null, $condition, array (LearningObject :: PROPERTY_TITLE), array (SORT_ASC));
		$result_count = count($returned_results);
		return new RepositorySearchResult($repository_title, $repository_url, $returned_results, $result_count);
	}
	
	static function is_supported()
	{
		return true;
	}
}
?>