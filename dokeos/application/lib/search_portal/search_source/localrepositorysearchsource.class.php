<?php
require_once dirname(__FILE__).'/searchsource.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';

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
		return $this->data_manager->retrieve_learning_objects(null, $condition, array (LearningObject :: PROPERTY_TITLE), array (SORT_ASC));
	}
	
	static function is_supported()
	{
		return true;
	}
}
?>