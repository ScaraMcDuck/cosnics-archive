<?php
require_once dirname(__FILE__).'/../searchsource.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
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
		$repository_title = api_get_setting('siteName');
		$repository_url = api_get_path(WEB_PATH);
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