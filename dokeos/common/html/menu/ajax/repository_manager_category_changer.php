<?php

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

$source = $_POST['source'];
$target = $_POST['target'];

if(!isset($source) || !isset($target)) exit;

//echo $_POST['target'] . ' ' . $_POST['source'];

$user_id = Session :: get_user_id();
$user_condition = new EqualityCondition('user_id', $user_id);
$id_condition = new EqualityCondition('id', $source);

$rdm = RepositoryDataManager :: get_instance();
$category = $rdm->retrieve_categories($id_condition)->next_result();
$old_parent = $category->get_parent();
$category->set_parent($target);
$category->set_display_order($rdm->select_next_category_display_order($target, $user_id));
$category->update();

$counter = 1;

$parent_condition = new EqualityCondition('parent', $old_parent);

$categories = $rdm->retrieve_categories(new AndCondition(array($user_condition,$parent_condition)), null, null, array('display_order'), array(SORT_ASC));
while($cat = $categories->next_result())
{
	$cat->set_display_order($counter);
	$cat->update();
	$counter++;
}
echo "test";
?>