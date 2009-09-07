<?php

require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

if (Authentication :: is_valid())
{
	$title = Request :: post('title');
	
	$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_TITLE, $title);
	$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
	$condition = new AndCondition($conditions);
	
	$count = RepositoryDataManager :: get_instance()->count_learning_objects(null, $condition); 
	if($count > 0)
	{
		echo '<div class="warning-message">' . Translation :: get('TitleExists') . '</div>';
	}
}

?>