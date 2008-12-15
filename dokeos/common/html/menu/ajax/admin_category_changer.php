<?php

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_admin_path() . 'lib/admin_data_manager.class.php';

$source = $_POST['source'];
$target = $_POST['target'];

if(!isset($source) || !isset($target)) exit;

//echo $_POST['target'] . ' ' . $_POST['source'];

$adm = AdminDataManager :: get_instance();
$category = $adm->retrieve_categories(new EqualityCondition('id', $source))->next_result();
$old_parent = $category->get_parent();
$category->set_parent($target);
$category->set_display_order($adm->select_next_display_order($target));
$category->update();

$counter = 1;

$categories = $adm->retrieve_categories(new EqualityCondition('parent', $old_parent), null, null, array('display_order'), array(SORT_ASC));
while($cat = $categories->next_result())
{
	$cat->set_display_order($counter);
	$cat->update();
	$counter++;
}

?>