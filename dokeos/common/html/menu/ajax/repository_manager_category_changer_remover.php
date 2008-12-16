<?php

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

$id = $_POST['item'];

if(!isset($id)) 
{
	echo "false";
	exit();
} 
$user_id = Session :: get_user_id();
$user_condition = new EqualityCondition('user_id', $user_id);
$id_condition = new EqualityCondition('id', $id);

$rdm = RepositoryDataManager :: get_instance();
$category = $rdm->retrieve_categories($id_condition)->next_result();
$bool = $category->delete();
$bool &= delete_children($id);

function delete_children($id)
{
	global $user_condition;
	
	$parent_condition = new EqualityCondition('parent', $id);
	$cond = new AndCondition(array($user_condition, $parent_condition));
	
	$bool = true;
	
	$rdm = RepositoryDataManager :: get_instance();
	$categories = $rdm->retrieve_categories($cond);
	while($category = $categories->next_result())
	{
		$bool &= $category->delete();
		$bool &= delete_children($category->get_id());
	}
	
	return $bool;
}

if($bool)
	echo "true";
else
	echo "false";

?>