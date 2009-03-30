<?php
require_once dirname(__FILE__).'/../../../common/global.inc.php';
require_once Path :: get_repository_path().'/lib/repository_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_repository_path().'/lib/learning_object.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/not_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';

Translation :: set_application('repository');

$html = array();

if (Authentication :: is_valid())
{
	$rdm = RepositoryDataManager :: get_instance();
	
	$conditions = array();
	$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
	$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, 'template');
	$condition = new AndCondition($conditions);
	
	$templates = $rdm->retrieve_learning_objects(null, $condition, array(LearningObject :: PROPERTY_TITLE), array(SORT_ASC));
	
	$html[] = '<?xml version="1.0" encoding="utf-8" ?>';
	$html[] = '<Templates>';
	
	while ($template = $templates->next_result())
	{
		//$html[] = '<Template title="'. $template->get_title() .'" image="'. Theme :: get_common_image_path() .'status_error.png">';
		$html[] = '<Template title="'. $template->get_title() .'">';
		$html[] = '<Description>'. $template->get_description() .'</Description>';
		$html[] = '<Html>';
		$html[] = '<![CDATA[';
		$html[] = $template->get_design();
		$html[] = ']]>';
		$html[] = '</Html>';
		$html[] = '</Template>';
	}
	
	$html[] = '</Templates>';
}

echo implode("\n", $html);
?>
