<?php
/**
 * @package application.searchportal
 */
require_once dirname(__FILE__).'/../searchsource.class.php';
require_once Path :: get_repository_path(). 'lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../repositorysearchresult.class.php';

class LocalRepositorySearchSource implements SearchSource
{
	private $data_manager;
	
	function LocalRepositorySearchSource($data_manager)
	{
		$this->data_manager = $data_manager;
	}
	
	function search ($query)
	{
		$condition = RepositoryUtilities :: query_to_condition($query);
		
		$adm = AdminDataManager :: get_instance();
		$site_name_setting = PlatformSetting :: get('site_name');
		$repository_title = $site_name_setting->get_value();
		
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