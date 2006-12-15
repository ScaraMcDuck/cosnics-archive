<?php
/**
 * $Id: usertool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Search tool
 * @package application.weblcms.tool
 * @subpackage search
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/searchform.class.php';
/**
 * Tool to search in the course.
 * @todo: Implementation
 */
class SearchTool extends Tool
{
	// Inherited
	function run()
	{
		$this->display_header();
		// Display the search form
		$form = new SearchForm($this);
		echo '<div style="text-align:center">';
		$form->display();
		echo '</div>';
		// If form validates, show results
		if($form->validate())
		{
			$datamanager = WeblcmsDataManager :: get_instance();
			$user_id = $this->get_user_id();
			$groups = $this->get_groups();
			$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $user_id, $groups);
			$id_conditions = array();
			$id = $publications->next_learning_object_id();
			while($id != null)
			{
				$id_conditions[] = new EqualityCondition(LearningObject::PROPERTY_ID,$id);
				$id = $publications->next_learning_object_id();
			}
			$repositorymanager = RepositoryDataManager :: get_instance();
			//@todo Maybe this can be implemented better by implemeting an 'InCondition' which matches the MySQL IN(x,x,x,x) condition
			$id_condition = new OrCondition($id_conditions);
			$search_condition = $form->get_condition();
			$condition = new AndCondition($id_condition,$search_condition);
			$objects = $repositorymanager->retrieve_learning_objects(null,$condition)->as_array();
			foreach($objects as $index => $object)
			{
				echo '<div class="learning_object" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/'.$object->get_icon_name().'.gif);">';
				echo '<div class="title"">'.$object->get_title().'</div>';
				echo '<div class="description">'.$object->get_description().'</div>';
				echo '</div>';
			}
		}
		$this->display_footer();
	}
}
?>