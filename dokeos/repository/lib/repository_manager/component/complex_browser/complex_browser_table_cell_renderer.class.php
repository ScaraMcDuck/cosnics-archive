<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 15489 2008-05-29 07:53:34Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/complex_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../learning_object_table/default_learning_object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../learning_object.class.php';
require_once dirname(__FILE__).'/../../../learning_object.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ComplexBrowserTableCellRenderer extends DefaultLearningObjectTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	private $learning_object;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ComplexBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $cloi)
	{
		if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($cloi);
		}
		
		if(!$this->learning_object || $this->learning_object->get_id() != $cloi->get_ref())
		{
			$learning_object = $this->browser->retrieve_learning_object($cloi->get_ref());
			$this->learning_object = $learning_object;
		}
		else
		{
			$learning_object = $this->learning_object;
		}
		
		switch ($column->get_title())
		{ 
			case LearningObject :: PROPERTY_TYPE :
				$type = $learning_object->get_type();
				$icon = $learning_object->get_icon_name();
				$url = '<img src="'.Theme :: get_common_img_path() . 'learning_object/' .$icon.'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($type).'TypeName')).'"/>';
				return $url;//'<a href="'.htmlentities($this->browser->get_type_filter_url($learning_object->get_type())).'">'.$url.'</a>';
			case LearningObject :: PROPERTY_TITLE :
				$title = htmlspecialchars($learning_object->get_title());
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return $title_short; //'<a href="'.htmlentities($this->browser->get_learning_object_viewing_url($learning_object)).'" title="'.$title.'">'.$title_short.'</a>';
			case LearningObject :: PROPERTY_DESCRIPTION :
				$description = strip_tags($learning_object->get_description());
				if(strlen($description) > 203)
				{
					mb_internal_encoding("UTF-8");
					$description = mb_substr(strip_tags($learning_object->get_description()),0,200).'&hellip;';
				}
				return $description;
		}
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($learning_object)
	{
//		$toolbar_data = array();
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_learning_object_editing_url($learning_object),
//			'label' => Translation :: get('Edit'),
//			'img' => Theme :: get_common_img_path().'action_edit.png'
//		);
//		$html = array ();
//		if ($url = $this->browser->get_learning_object_recycling_url($learning_object))
//		{
//			$toolbar_data[] = array(
//				'href' => $url,
//				'label' => Translation :: get('Remove'),
//				'img' => Theme :: get_common_img_path().'action_recycle_bin.png',
//				'confirm' => true
//			);
//		}
//		else
//		{
//			$toolbar_data[] = array(
//				'label' => Translation :: get('Remove'),
//				'img' => Theme :: get_common_img_path().'action_recycle_bin_na.png'
//			);
//		}
//		if($this->browser->get_number_of_categories() > 1)
//		{
//			$toolbar_data[] = array(
//				'href' => $this->browser->get_learning_object_moving_url($learning_object),
//				'label' => Translation :: get('Move'),
//				'img' => Theme :: get_common_img_path().'action_move.png'
//			);
//		}
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_learning_object_metadata_editing_url($learning_object),
//			'label' => Translation :: get('Metadata'),
//			'img' => Theme :: get_common_img_path().'action_metadata.png'
//		);
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_learning_object_rights_editing_url($learning_object),
//			'label' => Translation :: get('Rights'),
//			'img' => Theme :: get_common_img_path().'action_rights.png'
//		);
//		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>