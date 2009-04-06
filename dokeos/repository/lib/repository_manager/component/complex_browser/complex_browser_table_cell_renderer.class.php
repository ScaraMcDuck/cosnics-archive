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
	private $rdm;
	private $condition;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ComplexBrowserTableCellRenderer($browser, $condition)
	{
		parent :: __construct();
		$this->browser = $browser;
		$this->rdm = RepositoryDataManager :: get_instance();
		$this->condition = $condition;
	}
	// Inherited
	function render_cell($column, $cloi)
	{
		if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($cloi);
		}

		$ref = $cloi->get_ref();
		
		if(!$this->learning_object || $this->learning_object->get_id() != $ref)
		{ 
			$learning_object = $this->rdm->retrieve_learning_object($ref);
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
				$url = '<img src="'.Theme :: get_common_image_path() . 'learning_object/' .$icon.'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($type).'TypeName')).'"/>';
				return $url;//'<a href="'.htmlentities($this->browser->get_type_filter_url($learning_object->get_type())).'">'.$url.'</a>';
			case LearningObject :: PROPERTY_TITLE :
				$title = htmlspecialchars($learning_object->get_title());
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				
				if($learning_object->is_complex_learning_object())
				{
					$title_short = '<a href="' . $this->browser->get_url(
						array(RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->browser->get_root(), 
							  RepositoryManager :: PARAM_CLOI_ID => $cloi->get_ref(), 'publish' => $_GET['publish'])) . '">' . $title_short . '</a>'; 
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
			case 'subitems':
				if($cloi->is_complex())
				{
					$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $cloi->get_ref());
					return $this->browser->count_complex_learning_object_items($condition);
				}
				return 0;
		}
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($cloi)
	{
		$toolbar_data = array();
		
		$edit_url = $this->browser->get_complex_learning_object_item_edit_url($cloi, $this->browser->get_root());
		if($cloi->is_extended() || get_class($this->browser) == 'AssessmentBuilder')
		{
			$toolbar_data[] = array(
				'href' => $edit_url,
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('EditNA'),
				'img' => Theme :: get_common_image_path().'action_edit_na.png'
			);
		}
		
		$delete_url = $this->browser->get_complex_learning_object_item_delete_url($cloi, $this->browser->get_root());
		$moveup_url = $this->browser->get_complex_learning_object_item_move_url($cloi, $this->browser->get_root(), RepositoryManager :: PARAM_DIRECTION_UP);
		$movedown_url = $this->browser->get_complex_learning_object_item_move_url($cloi, $this->browser->get_root(), RepositoryManager :: PARAM_DIRECTION_DOWN);
		
		$toolbar_data[] = array(
			'href' => $delete_url,
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
			'confirm' => true
		);
		
		$allowed = $this->check_move_allowed($cloi);
		
		if($allowed["moveup"])
		{
			$toolbar_data[] = array(
				'href' => $moveup_url,
				'label' => Translation :: get('MoveUp'),
				'img' => Theme :: get_common_image_path().'action_up.png',
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveUpNA'),
				'img' => Theme :: get_common_image_path().'action_up_na.png',
			);

		}
		
		if($allowed["movedown"])
		{
			$toolbar_data[] = array(
				'href' => $movedown_url,
				'label' => Translation :: get('MoveDown'),
				'img' => Theme :: get_common_image_path().'action_down.png',
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('MoveDownNA'),
				'img' => Theme :: get_common_image_path().'action_down_na.png',
			);	
		}	
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
	
	private function check_move_allowed($cloi)
	{
		$moveup_allowed = true;
		$movedown_allowed = true;
		
		$count = $this->rdm->count_complex_learning_object_items($this->condition);
		if($count == 1)
		{
			$moveup_allowed = false;
			$movedown_allowed = false;
		}
		else
		{
			if($cloi->get_display_order() == 1)
			{
				$moveup_allowed = false;
			}
			else
			{
				if($cloi->get_display_order() == $count)
				{
					$movedown_allowed = false;
				}
			}
		}
		
		return array('moveup' =>$moveup_allowed, 'movedown' => $movedown_allowed);
	}
}
?>