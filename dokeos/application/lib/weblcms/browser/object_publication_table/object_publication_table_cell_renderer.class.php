<?php

require_once Path :: get_repository_path(). 'lib/learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/object_publication_table_column_model.class.php';
require_once Path :: get_application_path().'lib/weblcms/trackers/weblcms_assessment_attempts_tracker.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class ObjectPublicationTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	private $browser;

	function ObjectPublicationTableCellRenderer($browser)
	{
		$this->browser = $browser;
	}
	/*
	 * Inherited
	 */
	function render_cell($column, $publication)
	{
		if ($column === ObjectPublicationTableColumnModel :: get_action_column())
		{
			return $this->get_actions($publication);
		} 

		switch($column->get_name())
		{
			case LearningObjectPublication :: PROPERTY_PUBLICATION_DATE:
				 $date_format = Translation :: get('dateTimeFormatLong');
      			 return Text :: format_locale_date($date_format,$publication->get_publication_date());		
		}
		
		$data = parent :: render_cell($column, $publication->get_learning_object());
		
		if ($publication->is_hidden())
		{
			return '<span style="color: gray">'. $data .'</span>';
		}
		else
		{
			return $data;
		}
	}
	
	function get_actions($publication) 
	{
		if ($this->browser->is_allowed(EDIT_RIGHT)) 
		{
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_DELETE_PUBLICATION, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Delete'), 
			'img' => Theme :: get_common_image_path().'action_delete.png'
			);
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Edit'), 
			'img' => Theme :: get_common_image_path().'action_edit.png'
			);
			
			$img = 'action_visible.png';
			if ($publication->is_hidden())
			{
				$img = 'action_visible_na.png';
			}
			$actions[] = array(
			'href' => $this->browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Visible'), 
			'img' => Theme :: get_common_image_path().$img
			);	
			
			$actions[] = array(
			'href' => $this->browser->get_url(array(AssessmentTool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), 
			'label' => Translation :: get('Move'), 
			'img' => Theme :: get_common_image_path().'action_move.png'
			);
			
		} 

		
		return DokeosUtilities :: build_toolbar($actions);
	}
}
?>