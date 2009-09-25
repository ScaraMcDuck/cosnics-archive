<?php
require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

$html = array();

if (Authentication :: is_valid())
{
	$query = Request :: post('query');
	
	$conditions = array();
//	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $this->get_parent_id());
	$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
	$or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query);
	$or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query);
	$conditions[] = new OrCondition($or_conditions);
	$condition = new AndCondition($conditions);
	
	$objects = RepositoryDataManager :: get_instance()->retrieve_content_objects(null, $condition, array(new ObjectTableOrder(ContentObject :: PROPERTY_TITLE)));
	
	$html[] = '<ul>';
	while ($object = $objects->next_result())
	{
		$html[] = '<li>' . $object->get_title() . '</li>';
	}
	$html[] = '</ul>';
}
else
{
	$html[] = '<ul>';
	$html[] = '</ul>';
}

echo implode("\n", $html);
?>