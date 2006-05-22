<?php
/**
==============================================================================
 *	This is a simple test script that creates a root category for each user's
 *	repository if it doesn't exist already.
 *
 *	@author Tim De Pauw
 * @package repository
==============================================================================
 */

$langFile = 'repository';
require_once dirname(__FILE__).'/../../claroline/inc/claro_init_global.inc.php';
require_once dirname(__FILE__).'/../lib/learningobject.class.php';
require_once dirname(__FILE__).'/../lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../lib/condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../lib/condition/andcondition.class.php';

echo '<html><body><p>Creating root categories &hellip;</p><ul>';

$dm = RepositoryDataManager :: get_instance();

$user_table = Database :: get_main_table(MAIN_USER_TABLE);
$result = api_sql_query("SELECT user_id, username FROM $user_table",__FILE__,__LINE__);
$created = 0;
while ($row = mysql_fetch_array($result))
{
	$id = $row[0];
	$condition = get_cond($id);
	$res = $dm->retrieve_learning_objects(null, $condition);
	if ($res->next_result())
	{
		continue;
	}
	$object = new Category();
	$object->set_owner_id($id);
	$object->set_title(get_lang('MyRepository'));
	$object->set_description('');
	$object->create();
	echo '<li>', $row[1], '</li>', "\n";
	$created++;
}

echo '</ul>';

echo '<p>', ($created ? 'Created ' . $created . ' root ' . ($created > 1 ? 'categories' : 'category') . '.' : 'No new categories necessary.') . '</p>';

function get_cond ($id)
{
	$c = array();
	$c[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, 'category');
	$c[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $id);
	$c[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, 0);
	return new AndCondition($c);
}
?>