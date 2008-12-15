<?php

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_admin_path() . 'lib/admin_data_manager.class.php';

$id = $_POST['item'];

if(!isset($id)) 
{
	echo "false";
	exit();
} 
$adm = AdminDataManager :: get_instance();
$category = $adm->retrieve_categories(new EqualityCondition('id', $id))->next_result();
$bool = $category->delete();
$bool &= delete_children($id);

function delete_children($id)
{
	$bool = true;
	
	$adm = AdminDataManager :: get_instance();
	$categories = $adm->retrieve_categories(new EqualityCondition('parent', $id));
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