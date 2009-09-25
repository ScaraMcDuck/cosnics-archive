<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/../common/global.inc.php';
require_once dirname(__FILE__).'/lib/repository_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/lib/content_object.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/not_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';

Translation :: set_application('repository');

if (Authentication :: is_valid())
{
	$conditions = array ();

	$query_condition = DokeosUtilities :: query_to_condition($_POST['queryString'], ContentObject :: PROPERTY_TITLE);
	if (isset ($query_condition))
	{
		$conditions[] = $query_condition;
	}

	$owner_condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
	$conditions[] = $owner_condition;

	$category_type_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'category');
	$conditions[] = new NotCondition($category_type_condition);

	$condition = new AndCondition($conditions);

	$dm = RepositoryDataManager :: get_instance();
	$objects = $dm->retrieve_content_objects(null, $condition, array (ContentObject :: PROPERTY_TITLE), array (SORT_ASC));

	while ($lo = $objects->next_result())
	{
		echo '<li onclick="fill(\''. $lo->get_title() .'\');">';
		echo $lo->get_title();
		echo '</li>';
	}

}
?>