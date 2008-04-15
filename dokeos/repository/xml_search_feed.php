<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/../common/global.inc.php';
require_once dirname(__FILE__).'/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/lib/learningobject.class.php';
require_once Path :: get_library_path().'condition/equalitycondition.class.php';
require_once Path :: get_library_path().'condition/notcondition.class.php';
require_once Path :: get_library_path().'condition/andcondition.class.php';
require_once Path :: get_library_path().'condition/orcondition.class.php';

Translation :: set_application('repository');

if (PlatformSession :: get_user_id())
{
	$conditions = array ();

	$query_condition = RepositoryUtilities :: query_to_condition($_POST['queryString'], LearningObject :: PROPERTY_TITLE);
	if (isset ($query_condition))
	{
		$conditions[] = $query_condition;
	}

	$owner_condition = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, PlatformSession :: get_user_id());
	$conditions[] = $owner_condition;

	$category_type_condition = new EqualityCondition(LearningObject :: PROPERTY_TYPE, 'category');
	$conditions[] = new NotCondition($category_type_condition);

	$condition = new AndCondition($conditions);

	$dm = RepositoryDataManager :: get_instance();
	$objects = $dm->retrieve_learning_objects(null, $condition, array (LearningObject :: PROPERTY_TITLE), array (SORT_ASC));

	while ($lo = $objects->next_result())
	{
		echo '<li onclick="fill(\''. $lo->get_title() .'\');">';
		echo $lo->get_title();
		echo '</li>';
	}

}
?>