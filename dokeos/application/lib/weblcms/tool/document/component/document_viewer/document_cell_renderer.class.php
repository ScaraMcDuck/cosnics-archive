<?php

require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_cell_renderer.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class DocumentCellRenderer extends ObjectPublicationTableCellRenderer
{
	function DocumentCellRenderer($browser)
	{
		parent :: __construct($browser);
	}
	
	/*
	 * Inherited
	 */
	function render_cell($column, $publication)
	{
		switch($column->get_name())
		{
			case LearningObject :: PROPERTY_TITLE:
				 $lo = $publication->get_learning_object();
				 $feedback_url = $this->browser->get_url(array (Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => 'view'));
				 $data = '<div style="float: left;"><a href="' . $feedback_url . '">' . $lo->get_title() . '</a></div> ';
				 $data .= '<div style="float: right;"><a href="' . $lo->get_url() . '">' . Theme :: get_common_image('action_export') . '</a></div>';
      			 break;	
		}
		
		if($data)
		{
			if ($publication->is_hidden())
			{
				return '<span style="color: gray">'. $data .'</span>';
			}
			else
			{
				return $data;
			}
		}
		else 
		{
			return parent :: render_cell($column, $publication);
		}
	}

}
?>