<?php
/**
 * @package application.weblcms.tool.exercise.component.exercise_publication_table
 */
require_once Path :: get_repository_path(). 'lib/learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/learning_path_publication_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../../trackers/weblcms_lp_attempt_tracker.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class LearningPathPublicationTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	private $table_actions;
	private $browser;
	/**
	 * Constructor.
	 * @param string $publish_url_format URL for publishing the selected
	 * learning object.
	 * @param string $edit_and_publish_url_format URL for editing and publishing
	 * the selected learning object.
	 */
	function LearningPathPublicationTableCellRenderer($browser)
	{
		$this->table_actions = array();
		$this->browser = $browser;
	}
	/*
	 * Inherited
	 */
	function render_cell($column, $publication)
	{
		if ($column === LearningPathPublicationTableColumnModel :: get_action_column())
		{
			return $this->get_actions($publication);
		}
		$learning_object = $publication->get_learning_object();
		
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case LearningObject :: PROPERTY_TITLE :
					$title = '<a href="' . $this->browser->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, Tool :: PARAM_PUBLICATION_ID => $publication->get_id() )) . '">' . htmlspecialchars($learning_object->get_title()) . '</a>';
					if($publication->is_hidden())
					{
						return '<span style="color: gray">'. $title .'</span>';
					}
					return $title;
			}
		}
		
		if($title = $column->get_title())
		{
			switch($title)
			{
				case Translation :: get('Progress'):
					$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->browser->get_course_id());
					$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $learning_object->get_id());
					$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->browser->get_user_id());
					//$conditions[] = new NotCondition(new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS, 100));
					$condition = new AndCondition($conditions);
					
					$dummy = new WeblcmsLpAttemptTracker();
					$trackers = $dummy->retrieve_tracker_items($condition);
					$lp_tracker = $trackers[0];
					
					if($lp_tracker)
						$progress = $lp_tracker->get_progress();
					else
						$progress = 0;
					
					return $this->get_progress_bar($progress);
			}
		}
		
		$info = parent :: render_cell($column, $publication->get_learning_object());
		if($publication->is_hidden())
		{
			return '<span style="color: gray">'. $info .'</span>';
		}
		
		return $info;
	}
	
	private function get_progress_bar($progress)
	{
		$html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
		$html[] = '<div style="background-color: lightblue; height: 14px; width:' . $progress . 'px; text-align: center;">';
		$html[] = '</div>';
		$html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round($progress) . '%</div></div>';
		
		return implode("\n", $html);
	}
	
	function get_actions($publication) 
	{
		/*$execute = array(
		'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_TAKE_EXERCISE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
		'label' => Translation :: get('Take exercise'),
		'img' => Theme :: get_common_image_path().'action_right.png'
		);*/
		
		//$actions[] = $execute;
		
		if ($this->browser->is_allowed(EDIT_RIGHT)) 
		{
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Delete'), 
			'img' => Theme :: get_common_image_path().'action_delete.png'
			);
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Edit'), 
			'img' => Theme :: get_common_image_path().'action_edit.png'
			);
			
			if($publication->is_hidden())
				$icon = 'action_invisible.png';
			else
				$icon = 'action_visible.png';
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Visible'), 
			'img' => Theme :: get_common_image_path(). $icon
			);
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_EXPORT_SCORM, LearningPathTool :: PARAM_LEARNING_PATH_ID => $publication->get_learning_object()->get_id())),
			'label' => Translation :: get('ExportSCORM'),
			'img' => Theme :: get_common_image_path().'action_export.png'
			);
			
			$actions[] = array(
				'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())),
				'label' => Translation :: get('Statistics'),
				'img' => Theme :: get_common_image_path().'action_reporting.png'
			);
		}
		
		return DokeosUtilities :: build_toolbar($actions);
	}
	
	/**
	 * Gets the links to publish or edit and publish a learning object.
	 * @param LearningObject $learning_object The learning object for which the
	 * links should be returned.
	 * @return string A HTML-representation of the links.
	 */
	private function get_publish_links($learning_object)
	{
		$toolbar_data = array();
		$table_actions = $this->table_actions;
		
		foreach($table_actions as $table_action)
		{
			$table_action['href'] = sprintf($table_action['href'], $learning_object->get_id());
			$toolbar_data[] = $table_action;
		}
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>