<?php
/**
 * @package application.weblcms.tool.exercise.component
 */

require_once dirname(__FILE__).'/assessment_publication_table/assessment_publication_table.class.php';
require_once Path :: get_library_path().'/html/action_bar/action_bar_renderer.class.php';

/**
 * Represents the view component for the exercise tool.
 *
 */
class AssessmentToolViewerComponent extends AssessmentToolComponent 
{
	private $action_bar;
	
	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$trail = new BreadCrumbTrail();
		$this->display_header($trail);
		
		$this->action_bar = $this->get_toolbar();
		echo $this->action_bar->as_html();
		
		echo '<div style="width:19%; float: left;">';
		echo '<div style="border-bottom: 1px solid grey; padding: 5px; line-height: 25px;">';
		echo 'hier komen de categoriekes';
		
		echo '</div></div>';
		echo '<div style="width:79%; padding-left: 1%; float:right; border-left: 1px solid grey;">';
		$table = new AssessmentPublicationTable($this, $this->get_user(), array('exercise'), null);
		echo $table->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
}

?>